<?php

namespace BanquemondialeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BanquemondialeBundle\Form\DossierHelpDeskSearchType;

class HelpDeskController extends Controller
{
    public function suivreDossierAction(){
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        //$listDossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findBy(array('statut' => ' not null', 'utilisateur' => $user->getId()), array('id' => 'desc'));
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $isSiege=$user->getEntreprise()->getIsSiege();
        $listDossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDossiersHelphDeskSuiviByParametres(null, $langue->getId(), $user->getEntreprise()->getId(),$isSiege, 25);


        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['dossierEncours'];
            $listDossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDossiersHelphDeskSuiviByParametres($data, $langue->getId(), $user->getEntreprise()->getId(),$isSiege);
        }

        $form = $this->createForm(new DossierHelpDeskSearchType(array('langue' => $langue)));

        $form->bind($request);


        return $this->render('BanquemondialeBundle:HelpDesk:suivreDossier.html.twig', array('listDossier' => $listDossier, 'form' => $form->createView(),'isSiege'=>$isSiege));

    }
    public function situationTraitementPoleAction($idd) {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $user = $this->container->get('security.context')->getToken()->getUser();

        /*if ($dossierDemande == null or $dossierDemande->getUtilisateur() != $user) {
            $translated = $this->get('translator')->trans('acces_inautorise');
            $this->get('session')->getFlashBag()->add('error', $translated);
            return $this->redirectToRoute('administration');
        }*/
        $listDocumentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDocumentCollectedByDossierDemande($dossierDemande, $langue);
        $listPieceEntreprise = $em->getRepository('BanquemondialeBundle:CollectionPieceJointe')->findAllPieceJoined($idd, $langue);
        return $this->render('BanquemondialeBundle:HelpDesk:situationTraitementPole.html.twig', array('listDocumentCollected' => $listDocumentCollected, 'idd' => $idd, 'dd' => $dossierDemande,'listPieceEntreprise'=>$listPieceEntreprise));
    }
}
