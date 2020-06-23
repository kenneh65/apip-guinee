<?php

namespace BanquemondialeBundle\Controller;

use BanquemondialeBundle\Entity\DossierDemande;
use BanquemondialeBundle\Form\DossierDemandeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CreationDossierController extends Controller {

    public function ajouterAction($idLangue = null) {
        $message = '';
        $creationdemande = new DossierDemande();
        $form = $this->container->get('form.factory')->create(new DossierDemandeType(), $creationdemande);
        $em = $this->getDoctrine()->getManager();
        $request = $this->container->get('request');
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($idLangue);
        $paysTraductions = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findByLangue($langue);
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($creationdemande);
                $em->flush();
                $message = $this->get('translator')->trans('demande_transmise_succes');
                $creationdemande = new DossierDemande();
                $form = $this->container->get('form.factory')->create(new DossierDemandeType(), $creationdemande);
            }
        }
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/index.html.twig', array('form' => $form->createView(), 'message' => $message, 'paysTraductions' => $paysTraductions));
    }

    public function listerAction() {
        $em = $this->container->get('doctrine')->getManager();
        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findAll();
        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:DemandeCreation/layout/lister.html.twig', array('listerdemande' => $listerdemande));
    }

    public function editerAction($id = null) {
        $message = '';
        $em = $this->container->get('doctrine')->getManager();
        if (isset($id)) {  // modification d'un acteur existant : on recherche ses donn�es
            $demande = $em->find('BanquemondialeBundle:DossierDemande', $id);
            if (!$demande) {
                $message = 'Aucune demande trouvée';
            }
        } else {
            $demande = new DossierDemande();
        }
        $form = $this->container->get('form.factory')->create(new DossierDemandeType(), $demande);
        $request = $this->container->get('request');
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($demande);
                $em->flush();
                if (isset($id)) {
                    $message = 'Demande modifiée avec succès !';
                } else {
                    $message = 'Demande ajouté avec succès !';
                }
            }
        }
        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:DemandeCreation/layout/editer.html.twig', array('form' => $form->createView(), 'message' => $message, 'demande' => $demande));
    }

    public function visualiserAction($id = null) {
        $message = '';
        $em = $this->container->get('doctrine')->getManager();
        if (isset($id)) {  // modification d'un acteur existant : on recherche ses donn�es
            $demande = $em->find('BanquemondialeBundle:DossierDemande', $id);
            if (!$demande) {
                $message = 'Aucune demande trouvée';
            }
        } else {
            $demande = new DossierDemande();
        }
        $form = $this->container->get('form.factory')->create(new DossierDemandeType(), $demande);
        $request = $this->container->get('request');
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {


                $html = $this->renderView('BanquemondialeBundle:Default:DemandeCreation/layout/matar.html.twig', array('demande' => $demande));


                $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');


                $html2pdf->pdf->SetDisplayMode('real');

                $html2pdf->writeHTML($html);

                $html2pdf->Output('Facture.pdf');



                $response = new Response();
                $response->headers->set('Content-type', 'application/pdf');
                return $response;
            }
        } else
            return new Response('echec');
    }

    public function supprimerAction($id) {
        $em = $this->container->get('doctrine')->getManager();
        $demande = $em->find('BanquemondialeBundle:DossierDemande', $id);
        if (!$demande) {
            throw new NotFoundHttpException("Demande de creation non trouvé");
        }
        $em->remove($demande);
        $em->flush();
        return new RedirectResponse($this->container->get('router')->generate('banquemondiale_lister'));
    }

   

}
