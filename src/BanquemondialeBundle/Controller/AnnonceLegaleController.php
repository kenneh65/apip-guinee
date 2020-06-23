<?php

namespace BanquemondialeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BanquemondialeBundle\Entity\DossierDemande;
use BanquemondialeBundle\Entity\CollectionPieceJointe;
use BanquemondialeBundle\Entity\DocumentCollected;
use BanquemondialeBundle\Form\DossierDemandeType;
use BanquemondialeBundle\Form\DossierDemandeSearchType;
use BanquemondialeBundle\Form\CapitalSocialType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AnnonceLegaleController extends Controller {
    /*
    public function VisualiserAction($idd = null, $maxItemPerPage = 5) {

        //$translated = $this->get('translator')->trans("Votre dossier N° '+$idd+' a été soumis avec succes");
        //$this->get('session')->getFlashBag()->add('info', $translated);


        $em = $this->container->get('doctrine')->getManager();
        $data = NULL;
        $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();
        if ($request->query->get('maxItemPerPage') > 0 && $request->query->get('maxItemPerPage') < 101) {
            $maxItemPerPage = $request->query->get('maxItemPerPage');
        }
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findById($data);

        //$form = $this->container->get('form.factory')->create(new DossierDemandeSearchType(), array('langue' => $langue));
        $form = $this->createForm(new DossierDemandeSearchType(array('langue' => $langue)));

        $form->bind($request);

        if ($form->isValid()) {
            if (isset($request->query->all()['dossierEncours'])) {

                $data = $request->query->all()['dossierEncours'];
                $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDemandeDossierByParametres($data, $idCodeLangue);
            } else {
                $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findById(0);
            }


            $listerdemande = $this->get('knp_paginator')->paginate($listerdemande, $this->getRequest('request')->query->get('page', 1), $maxItemPerPage);
            return $this->render('BanquemondialeBundle:Default:AnnonceLegale/layout/Visualiser.html.twig', array('form' => $form->createView(), 'listerdemande' => $listerdemande, 'langue' => $idCodeLangue, 'maxItemPerPage' => $maxItemPerPage));
        } else {
            //die((string) $form->getErrors(true));
        }


        $listerdemande = $this->get('knp_paginator')->paginate($listerdemande, $this->getRequest('request')->query->get('page', 1), 5);
        return $this->render('BanquemondialeBundle:Default:AnnonceLegale/layout/Visualiser.html.twig', array('form' => $form->createView(), 'listerdemande' => $listerdemande, 'langue' => $idCodeLangue, 'maxItemPerPage' => $maxItemPerPage));
    }*/
	
	
	public function VisualiserAction() {        

        $em = $this->container->get('doctrine')->getManager();
        $data = NULL;
        $request = $this->get('request');
        //$request->setLocale("en");
		$codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();
		$listerdemande = NULL;

		
		if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['dossierEncours'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findAnnonceLegaleByParametres($data, $idCodeLangue);
        }

        //$form = $this->container->get('form.factory')->create(new DossierDemandeSearchType(), array('langue' => $langue));
        $form = $this->createForm(new DossierDemandeSearchType(array('langue' => $langue)));

        $form->bind($request);

        return $this->render('BanquemondialeBundle:Default:AnnonceLegale/layout/Visualiser.html.twig', array('form' => $form->createView(), 'listerdemande' => $listerdemande, 'langue' => $idCodeLangue));
    }
	
    
    public function annonceAction($idd) {
        $em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();
        $creationdossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findOneBy(array('id' => $idd));
        $definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $creationdossier->getPays(), 'langue' => $langue));
        $definedTypeOperation = $em->getRepository('BanquemondialeBundle:TypeOperationTraduction')->findOneBy(array('typeOperation' => $creationdossier->getTypeOperation(), 'langue' => $langue));
        $definedFormeJuridiqueTraduction = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('formeJuridique' => $creationdossier->getFormeJuridique(), 'langue' => $langue));

        return $this->render('BanquemondialeBundle:Default:AnnonceLegale/layout/Annonce.html.twig', array(
                    'creationdossier' => $creationdossier, 'definedPaysTraduction' => $definedPaysTraduction,
                    'definedTypeOperation' => $definedTypeOperation,
                    'definedFormeJuridiqueTraduction' => $definedFormeJuridiqueTraduction, 'idd' => $idd
        ));
    }
    
     public function ReportAnnonceAction($idd)
    {
        $em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();
        $creationdossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findOneBy(array('id' => $idd));
        $definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $creationdossier->getPays(), 'langue' => $langue));
        $definedTypeOperation = $em->getRepository('BanquemondialeBundle:TypeOperationTraduction')->findOneBy(array('typeOperation' => $creationdossier->getTypeOperation(), 'langue' => $langue));
        $definedFormeJuridiqueTraduction = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('formeJuridique' => $creationdossier->getFormeJuridique(), 'langue' => $langue));
        $name="moi";
        //on stocke la vue à convertir en PDF, en n'oubliant pas les paramètres twig si la vue comporte des données dynamiques
        $html = $this->renderView('BanquemondialeBundle:Default:AnnonceLegale/layout/VisualiserAnnonce.html.twig', array(
                    'name' => $name,
                    'creationdossier' => $creationdossier, 'definedPaysTraduction' => $definedPaysTraduction,
                    'definedTypeOperation' => $definedTypeOperation,
                    'definedFormeJuridiqueTraduction' => $definedFormeJuridiqueTraduction, 'idd' => $idd
        ));
         
        //on instancie la classe Html2Pdf_Html2Pdf en lui passant en paramètre
        //le sens de la page "portrait" => p ou "paysage" => l
        //le format A4,A5...
        //la langue du document fr,en,it...
        $html2pdf = new \Html2Pdf_Html2Pdf('P','A4','fr');
 
        //SetDisplayMode définit la manière dont le document PDF va être affiché par l’utilisateur
        //fullpage : affiche la page entière sur l'écran
        //fullwidth : utilise la largeur maximum de la fenêtre
        //real : utilise la taille réelle
        $html2pdf->pdf->SetDisplayMode('real');
 
        //writeHTML va tout simplement prendre la vue stocker dans la variable $html pour la convertir en format PDF
        $html2pdf->writeHTML($html);
 
        //Output envoit le document PDF au navigateur internet avec un nom spécifique qui aura un rapport avec le contenu à convertir (exemple : Facture, Règlement…)
        $html2pdf->Output('Facture.pdf');
         exit;

    }
	
	public function nomCommercialAction() {
		$em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');
		$codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();
		$listerdemande=NULL;
		
		if ($request->getMethod() == 'POST') {
			$nomCommercial = $request->request->get("nomCommercial");            
            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findAnnonceLegaleByNomCommercial($nomCommercial, $idCodeLangue);
        }

        //$form = $this->container->get('form.factory')->create(new DossierDemandeSearchType(), array('langue' => $langue));
        $form = $this->createForm(new DossierDemandeSearchType(array('langue' => $langue)));

        $form->bind($request);

        return $this->render('BanquemondialeBundle:Default:AnnonceLegale/layout/Visualiser.html.twig', array('form' => $form->createView(), 'listerdemande' => $listerdemande, 'langue' => $idCodeLangue));
        
    }
}

