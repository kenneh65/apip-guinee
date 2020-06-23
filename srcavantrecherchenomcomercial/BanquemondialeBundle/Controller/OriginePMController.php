<?php

namespace BanquemondialeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BanquemondialeBundle\Form\OriginePMType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BanquemondialeBundle\Entity\OriginePM;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * OriginePM controller.
 *
 * @Route("/OriginePM")
 */
class OriginePMController extends Controller {

    public function getNextEtapeRoute($formeJuridique, $isAguipe) {
        $em = $this->getDoctrine()->getManager();
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $etape = $em->getRepository('ParametrageBundle:Fonctionnalite')->findOneBy(array('route' => $currentRoute));
        $etapeC = $em->getRepository('ParametrageBundle:EtapeCreation')->findOneBy(array('etape' => $etape->getId(), 'formeJuridique' => $formeJuridique));
        $ordre = 1;
        if ($etapeC) {
            $ordre = $etapeC->getOrdre();
        }
        $etapeSuivant = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextStepFormeJuridique($ordre, $formeJuridique, $isAguipe);
        $rteSuivant = null;
        if ($etapeSuivant) {
            $rteSuivant = $etapeSuivant->getEtape()->getRoute();
        }
        return $rteSuivant;
    }

    /**
     * Lists all OriginePM entities.
     *
     * @Route("/", name="OriginePM_index")
     * @Method("GET")
     */
    public function indexAction(Request $request, $id = null, $idd = null) {
        $originePM = new OriginePM();
        $request = $this->get('request');
        $message = '';
        $isEntrepriseIndividuelle = false;
        $idDossier = $idd;
        $em = $this->getDoctrine()->getManager();
        $originePM = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($idd);
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $isAguipe = true;
        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findBySigle("AGUIPE");
        $docAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
        if ($docAguipe) {
            $isAguipe = true;
        } else {
            $isAguipe = false;
        }
        //$definedTypeOrigine = $em->getRepository("ParametrageBundle:TypeOrigine")->getTypeOriginePP(false);
        $formeJuridiqueId = $dossierDemande->getFormeJuridique()->getId();
        $definedTypeOrigine = false;
        if ($dossierDemande->getFormeJuridique()->getSigle() == "EI") {
            if ($originePM == null) {
                $originePM = new OriginePM();
            }
            //$definedTypeOrigine = $em->getRepository("ParametrageBundle:TypeOrigine")->getTypeOriginePP(true);
            $definedTypeOrigine = true;
            //die(dump($originePM));
            $originePM->setSiExploitant(false);
            $originePM->setSiEtablissementSecondaire(false);
            $isEntrepriseIndividuelle = true;
        }
        $form = $this->createForm(new OriginePMType(array('typeOrigine' => $definedTypeOrigine)), $originePM);
        $form->handleRequest($request);
        //$getFormeJuridiqueEI = $em->getRepository('BanquemondialeBundle:FormeJuridique')->findBySigle("EI");
        //$idFJ = $dossierDemande->getFormeJuridique()->getSigle();
        //die(dump($idFJ))
        //die(dump($isEntrepriseIndividuelle));
        $numeroDossier = $dossierDemande->getNumeroDossier();
        $request = $this->get('request');
        if ($originePM) {
            if ($request->getMethod() == 'POST') {
                $em->persist($originePM);
                $em->flush();
                $message = $this->get('translator')->trans("message_originepm_modifier_succes");
                $rteSuivant = $this->getNextEtapeRoute($formeJuridiqueId,$isAguipe);
                return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idDossier, 'numeroDossier' => $idDossier)));
            }
        } else {
            if ($request->getMethod() == 'POST') {
                $currentoriginePM = new OriginePM();
                $form = $this->createForm(new OriginePMType(), $currentoriginePM);
                $form->handleRequest($request);
                $em->persist($currentoriginePM);
                $em->flush();
                $message = $this->get('translator')->trans("message_originepm_ajouter_succes");
                $rteSuivant = $this->getNextEtapeRoute($formeJuridiqueId,$isAguipe);
                return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idDossier, 'numeroDossier' => $idDossier)));
            }
        }

        return $this->render('BanquemondialeBundle:Default:OriginePM/layout/index.html.twig', array('form' => $form->createView(), 'originePM' => $originePM, 'numeroDossier' => $numeroDossier,
                    'idDossier' => $idDossier, 'estEI' => $isEntrepriseIndividuelle, 'message' => $message));
    }

}
