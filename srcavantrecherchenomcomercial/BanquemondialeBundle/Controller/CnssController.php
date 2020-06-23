<?php

namespace BanquemondialeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BanquemondialeBundle\Form\CnssType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BanquemondialeBundle\Entity\Cnss;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Cnss controller.
 *
 * @Route("/Cnss")
 */
class CnssController extends Controller {

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
     * Lists all Cnss entities.
     *
     * @Route("/", name="Cnss_index")
     * @Method("GET")
     */
    public function indexAction(Request $request, $id = null, $idd = null) {
        $cnss = new Cnss();
        $request = $this->get('request');
        $message = '';
        $idDossier = $idd;
        $em = $this->getDoctrine()->getManager();
        $cnss = $em->getRepository('BanquemondialeBundle:Cnss')->findOneByDossierDemande($idd);
        $form = $this->createForm(new CnssType(), $cnss);
        $form->handleRequest($request);
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $isAguipe = true;
        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findBySigle("AGUIPE");
        $docAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
        if ($docAguipe) {
            $isAguipe = true;
        } else {
            $isAguipe = false;
        }
        $numeroDossier = $dossierDemande->getNumeroDossier();
        $formeJuridiqueId = $dossierDemande->getFormeJuridique()->getId();
        $effectifTotal = $dossierDemande->getNombreSalariePrevu();
        $request = $this->get('request');
        if ($cnss) {
            if ($request->getMethod() == 'POST') {
                if ($cnss->getEffectifTotal() == $cnss->getEffectifHomme() + $cnss->getEffectifFemme()) {
                    $cnss->setEffectifTotal($dossierDemande->getNombreSalariePrevu());
                    $em->persist($cnss);
                    $em->flush();
                    $message = $this->get('translator')->trans("message_cnss_modifier_succes");
                    $rteSuivant = $this->getNextEtapeRoute($formeJuridiqueId,$isAguipe);
                    return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idDossier, 'numeroDossier' => $idDossier)));
                } else {
                    $message = $this->get('translator')->trans("message_effectif_homme_femme_non_coherent");
                }
            }
        } else {
            if ($request->getMethod() == 'POST') {
                $currentcnss = new Cnss();
                $form = $this->createForm(new CnssType(), $currentcnss);
                $form->handleRequest($request);
                if ($currentcnss->getEffectifTotal() == $currentcnss->getEffectifHomme() + $currentcnss->getEffectifFemme()) {
                    $currentcnss->setEffectifTotal($dossierDemande->getNombreSalariePrevu());
                    $em->persist($currentcnss);
                    $em->flush();
                    $message = $this->get('translator')->trans("message_cnss_ajouter_succes");
                    $rteSuivant = $this->getNextEtapeRoute($formeJuridiqueId,$isAguipe);
                    return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idd, 'numeroDossier' => $numeroDossier)));
                } else {
                    $message = $this->get('translator')->trans("message_effectif_homme_femme_non_coherent");
                }
            }
        }

        return $this->render('BanquemondialeBundle:Default:Cnss/layout/index.html.twig', array('form' => $form->createView(), 'cnss' => $cnss, 'numeroDossier' => $numeroDossier,
                    'idDossier' => $idDossier, 'message' => $message, 'effectifTotal' => $effectifTotal));
    }

}
