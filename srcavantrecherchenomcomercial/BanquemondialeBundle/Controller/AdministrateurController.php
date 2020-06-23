<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BanquemondialeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use BanquemondialeBundle\Entity\Administrateur;
use BanquemondialeBundle\Form\AdministrateurType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Description of AdministrationController
 *
 * @author DELL
 */
class AdministrateurController extends Controller {

    //put your code here

    public function listerAdminAction($id = null, $idd = null) {
        $message = '';
        $idDossier = $idd;
        $em = $this->getDoctrine()->getManager();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $numeroDossier = $dossierDemande->getNumeroDossier();
        if ((isset($id) && ($id != 0))) {  // modification d'un acteur existant : on recherche ses données
            $message = $this->get('translator')->trans("message_modification_en_cours");

            $creationAdmin = $em->find('BanquemondialeBundle:Administrateur', $id);
            if (!$creationAdmin) {
                $message = $this->get('translator')->trans("message_administrateur_non_trouve");
            }
            $request = $this->get('request');
            $codLang = $request->getLocale();
            $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);


            $form = $this->container->get('form.factory')->create(new AdministrateurType(array('langue' => $langue)), $creationAdmin, array());
            $request = $this->container->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                //if ($form->isValid())  {
                //$message= 'OK2';
                $doc = $em->find('BanquemondialeBundle:DossierDemande', $idd);
                $creationAdmin->setDossierDemande($doc);
                $em->persist($creationAdmin);
                $em->flush();
                if ((isset($id)) && ($id != 0)) {
                    $message = $message = $this->get('translator')->trans("succes_modification");
                } else {
                    $message = $message = $this->get('translator')->trans("message_administrateur_ajouter_succes");
                }
                // }
            }
        } else {
            $creationAdmin = new Administrateur();
            //$creationAdmin.idDossierDemande = $id;
            $request = $this->get('request');
            $codLang = $request->getLocale();
            $em = $this->getDoctrine()->getManager();
            $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
            $form = $this->container->get('form.factory')->create(new AdministrateurType(array('langue' => $langue)), $creationAdmin);
            $request = $this->container->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {
                    $doc = $em->find('BanquemondialeBundle:DossierDemande', $idd);
                    $creationAdmin->setDossierDemande($doc);
                    $em->persist($creationAdmin);
                    $em->flush();
                    $message = $message = $this->get('translator')->trans("message_administrateur_ajouter_succes");
                    $creationAdmin = new Administrateur();
                    $form = $this->container->get('form.factory')->create(new AdministrateurType(array('langue' => $langue)), $creationAdmin);
                }
            }
        }

        $em = $this->getDoctrine()->getManager();
        //$listeradmin= $em->getRepository('BanquemondialeBundle:Administrateur')->findAll();
        //$listeradmin= $em->getRepository('BanquemondialeBundle:Administrateur')->getListSearchAdministrateur($idDossier);
        $listeradmin = $em->getRepository('BanquemondialeBundle:Administrateur')->findByDossierDemande($idd);
        //$listeradmin= $em->getRepository('BanquemondialeBundle:Administrateur')->search($idDossier);
        // $query = $this->getDoctrine()->getRepository('AcmeDemoBundle:Pony')->search($form->getData());
        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:Administrateur/layout/administrateur.html.twig', array('form' => $form->createView(),
                    'message' => $message,
                    'listeradmin' => $listeradmin,
                    'idDossier' => $idd,
                    'admin' => $creationAdmin,
                    'numeroDossier' => $numeroDossier));
    }

    public function supprimerAction($id) {
        $em = $this->getDoctrine()->getManager();
        $admin = $em->find('BanquemondialeBundle:Administrateur', $id);
        if (!$admin) {
            throw new NotFoundHttpException("Administrateur non trouvé");
        }
        $em->remove($admin);
        $em->flush();
        return new RedirectResponse($this->container->get('router')->generate('Administrateur_listeradmin', array('id' => 0)));
    }

    public function ListeAdministrateurDossierAction(Request $request, $idd) {
        $em = $this->getDoctrine()->getManager();
        //$request = $this->get('request');            
        $entities = $em->getRepository('BanquemondialeBundle:Administrateur')->rechercheByDossierDemande($idd);
        $creationdossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findOneBy(array('id' => $idd));
        $idFormeJ = $creationdossier->getFormeJuridique()->getId();
        $numeroDossier = $creationdossier->getNumeroDossier();
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $nextRte = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextEtapePoleRoute($currentRoute, $idFormeJ);
        if (!$nextRte) {
            $nextRte = 'traiterDossier';
        }
        $routeAvant = $em->getRepository('ParametrageBundle:EtapeCreation')->getEtapePoleRouteBefore($currentRoute, $idFormeJ);
        try {
            $referer = $request->headers->get('referer');
            $path = substr($referer, strpos($referer, $request->getBaseUrl()));
            $path = str_replace($request->getBaseUrl(), '', $path);

            $matcher = $this->get('router')->getMatcher();
            $parameters = $matcher->match($path);
            $previous = $parameters['_route'];

            if ($routeAvant != $previous and $previous != $nextRte) {
                $translated = $this->get('translator')->trans('suivre_etape_acces_page');
                $this->get('session')->getFlashBag()->add('error', $translated);

                return $this->redirectToRoute($previous);
            }
        } catch (\Exception $e) {
            $translated = $this->get('translator')->trans('suivre_etape_acces_page');
            $this->get('session')->getFlashBag()->add('error', $translated);
            return $this->redirectToRoute('administration');
        }
        return $this->render('BanquemondialeBundle:Default:Administrateur/layout/listeAdministrateur.html.twig', array('listeradmin' => $entities, 'idd' => $idd, 'rteSuivant' => $nextRte, 'routeAvant' => $routeAvant, 'numeroDossier' => $numeroDossier));
    }

}
