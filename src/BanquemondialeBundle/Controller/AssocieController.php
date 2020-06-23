<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BanquemondialeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use BanquemondialeBundle\Entity\Associe;
use BanquemondialeBundle\Form\AssocieType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of AssocieController
 *
 * @author DELL
 */
class AssocieController extends Controller {

    public function listerAssocieAction(Request $request, $id = null, $idd = null) {
        $message = '';
        $idDossier = $idd;
        $em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $fonctionTraduit = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findByLangue($langue->getId());
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $numeroDossier = $dossierDemande->getNumeroDossier();


        if ((isset($id) && ($id != 0))) {  // modification d'un acteur existant : on recherche ses données
            $message = $this->get('translator')->trans("message_modification_en_cours");

            $creationAssocie = $em->find('BanquemondialeBundle:Associe', $id);
            if (!$creationAssocie) {
                $message = $this->get('translator')->trans("message_associe_non_trouve");
            }


            $definedPaysTraduction = $em->getRepository("BanquemondialeBundle:PaysTraduction")->getPaysTraduction($creationAssocie->getPays(), $langue->getId());
            $definedTypeEntrepriseTraduction = $em->getRepository("ParametrageBundle:TypeEntrepriseTraduction")->getTypeEntrepriseTraduction($creationAssocie->getTypeEntreprise(), $langue->getId());
            /* $definedFonctionTraduction = $em->getRepository("BanquemondialeBundle:FonctionTraduction")->getFonctionTraduction($creationAssocie->getFonction(), $langue->getId());
              $definedGenreTraduction = $em->getRepository("BanquemondialeBundle:GenreTraduction")->getGenreTraduction($creationAssocie->getGenre(), $langue->getId());
              $definedSituationMatrimonialeTraduction = $em->getRepository("BanquemondialeBundle:SituationMatrimonialeTraduction")->getSituationMatrimonialeTraduction($creationAssocie->getSituationMatrimoniale(), $langue->getId()); */
            $form = $this->container->get('form.factory')->create(new AssocieType(array('langue' => $langue)), $creationAssocie, array('definedPaysTraduction' => $definedPaysTraduction
                , 'definedTypeEntrepriseTraduction' => $definedTypeEntrepriseTraduction));
            $request = $this->container->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                //if ($form->isValid())  {
                $doc = $em->find('BanquemondialeBundle:DossierDemande', $idd);
                $creationAssocie->setDossierDemande($doc);
                $em->persist($creationAssocie);
                $em->flush();
                if ((isset($id)) && ($id != 0)) {
                    $message = $this->get('translator')->trans("message_associe_modifier_succes");
                } else {
                    $message = $this->get('translator')->trans("message_associe_ajouter_succes");
                }
                // }
            }
        } else {
            $creationAssocie = new Associe();
            //$creationAdmin.idDossierDemande = $id;
            //$definedPaysTraduction =$em->getRepository("BanquemondialeBundle:PaysTraduction")->getPaysTraduction($creationAssocie->getPays(),$langue->getId());
            $form = $this->container->get('form.factory')->create(new AssocieType(array('langue' => $langue)), $creationAssocie);
            $request = $this->container->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {
                    $doc = $em->find('BanquemondialeBundle:DossierDemande', $idd);
                    $creationAssocie->setDossierDemande($doc);
                    $em->persist($creationAssocie);
                    $em->flush();
                    $message = $this->get('translator')->trans("message_associe_ajouter_succes");
                    $creationAssocie = new Associe();
                    $form = $this->container->get('form.factory')->create(new AssocieType(array('langue' => $langue)), $creationAssocie);
                }
            }
        }

        $em = $this->container->get('doctrine')->getManager();
        //$listerassocie= $em->getRepository('BanquemondialeBundle:Associe')->findAll();
        //$listerassocie= $em->getRepository('BanquemondialeBundle:Associe')->getListSearchAdministrateur($idDossier);
        $listerassocie = $em->getRepository('BanquemondialeBundle:Associe')->findByDossierDemande($idDossier);
        //$listerassocie= $em->getRepository('BanquemondialeBundle:Associe')->search($idDossier);
        // $query = $this->getDoctrine()->getRepository('AcmeDemoBundle:Pony')->search($form->getData());
        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:Associe/layout/Associe.html.twig', array('form' => $form->createView(), 'message' => $message, 'listerAssocie' => $listerassocie, 'idDossier' => $idDossier,
                    'fonctionTraduit' => $fonctionTraduit, 'associe' => $creationAssocie, 'numeroDossier' => $numeroDossier));
    }

    public function supprimerAction($id) {
        $em = $this->container->get('doctrine')->getManager();
        $associe = $em->find('BanquemondialeBundle:Associe', $id);
        if (!$associe) {
            $message = $this->get('translator')->trans("message_associe_non_trouve");
            throw new NotFoundHttpException($message);
        }
        $idDossier = $associe->getDossierDemande()->getId();

        $em->remove($associe);
        $em->flush();
        return new RedirectResponse($this->container->get('router')->generate('Associe_listerassocie', array('id' => 0, 'idd' => $idDossier)));
    }

    public function ListeAssocieDossierAction($idd) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $dd = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $idFormJ=$dd->getFormeJuridique()->getId();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $entities = $em->getRepository('BanquemondialeBundle:Associe')->rechercheByDossierDemande($idd);
        //$fonctionTraduit = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findByLangue($langue->getId());
        //$genreTraduit = $em->getRepository('BanquemondialeBundle:GenreTraduction')->findByLangue($langue->getId());
        //$situationMatrimonialeTraduit = $em->getRepository('BanquemondialeBundle:SituationMatrimonialeTraduction')->findByLangue($langue->getId());
        $paysTraduit = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findByLangue($langue->getId());
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $nextRte = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextEtapePoleRoute($currentRoute,$idFormJ);
        if (!$nextRte) {
            $nextRte = 'traiterDossier';
        }
        $routeAvant = $em->getRepository('ParametrageBundle:EtapeCreation')->getEtapePoleRouteBefore($currentRoute,$idFormJ);
        try {
            $referer = $request->headers->get('referer');
            $path = substr($referer, strpos($referer, $request->getBaseUrl()));
            $path = str_replace($request->getBaseUrl(), '', $path);

            $matcher = $this->get('router')->getMatcher();
            $parameters = $matcher->match($path);
            $previous = $parameters['_route'];

            if ($previous != "associe_detailspole" and $routeAvant != $previous and $previous != $nextRte) {
                $translated = $this->get('translator')->trans('suivre_etape_acces_page');
                $this->get('session')->getFlashBag()->add('error', $translated);

                return $this->redirectToRoute($previous);
            }
        } catch (\Exception $e) {
            $translated = $this->get('translator')->trans('suivre_etape_acces_page');
            $this->get('session')->getFlashBag()->add('error', $translated);
            return $this->redirectToRoute('administration');
        }
        return $this->render('BanquemondialeBundle:Default:Associe/layout/listeAssocie.html.twig', array('listerassocie' => $entities,
                    'paysTraduit' => $paysTraduit,
                    'idd' => $idd, 'dd' => $dd, 'rteSuivant' => $nextRte, 'routeAvant' => $routeAvant));
    }

    public function detailsAssocieAction(Associe $associe) {
        $em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $associe->getPays(), 'langue' => $langue));

        //$definedGenreTraduction = $em->getRepository('BanquemondialeBundle:GenreTraduction')->findOneBy(array('genre' => $associe->getGenre(), 'langue' => $langue));
        //$definedSituationMatrimonialeTraduction = $em->getRepository('BanquemondialeBundle:SituationMatrimonialeTraduction')->findOneBy(array('situationMatrimoniale' => $associe->getSituationMatrimoniale(), 'langue' => $langue));
        //$definedFonctionTraduction = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('fonction' => $associe->getFonction(), 'langue' => $langue));

        $definedTypeEntrepriseTraduction = $em->getRepository("ParametrageBundle:TypeEntrepriseTraduction")->findOneBy(array('typeEntreprise' => $associe->getTypeEntreprise(), 'langue' => $langue));

        return $this->render('BanquemondialeBundle:Default:Associe/layout/details.html.twig', array(
                    'associe' => $associe, 'definedPaysTraduction' => $definedPaysTraduction, 'definedTypeEntrepriseTraduction' => $definedTypeEntrepriseTraduction,
        ));
    }

    public function detailsAssociePoleAction(Associe $associe) {
        $em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();


        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $associe->getPays(), 'langue' => $langue));

        $definedGenreTraduction = $em->getRepository('BanquemondialeBundle:GenreTraduction')->findOneBy(array('genre' => $associe->getGenre(), 'langue' => $langue));

        $definedSituationMatrimonialeTraduction = $em->getRepository('BanquemondialeBundle:SituationMatrimonialeTraduction')->findOneBy(array('situationMatrimoniale' => $associe->getSituationMatrimoniale(), 'langue' => $langue));

        $definedFonctionTraduction = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('fonction' => $associe->getFonction(), 'langue' => $langue));



        return $this->render('BanquemondialeBundle:Default:Associe/layout/detailspole.html.twig', array(
                    'associe' => $associe, 'definedPaysTraduction' => $definedPaysTraduction,
                    'definedGenreTraduction' => $definedGenreTraduction,
                    'definedSituationMatrimonialeTraduction' => $definedSituationMatrimonialeTraduction,
                    'definedFonctionTraduction' => $definedFonctionTraduction
        ));
    }

}
