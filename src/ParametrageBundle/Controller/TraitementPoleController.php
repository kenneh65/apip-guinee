<?php

namespace ParametrageBundle\Controller;

use ParametrageBundle\Entity\Formulaire\MembreTenuFormulaire;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ParametrageBundle\Entity\PoleComplementDossier;
use BanquemondialeBundle\Entity\FormulaireDelivre;
use ParametrageBundle\Form\DocumentCollectedSearchType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use BanquemondialeBundle\Entity\Rccm;
use BanquemondialeBundle\Entity\Nif;
use BanquemondialeBundle\Entity\ComplementCnss;
//pour web service
use DefaultBundle\Entity\HistoriqueEchangeDNI;
use ParametrageBundle\Entity\Formulaire\FormulaireSocieteMorale;
use ParametrageBundle\Entity\Formulaire\AssocieFormulaire;
use ParametrageBundle\Entity\Formulaire\PersonneMoraleFormulaire;
use ParametrageBundle\Entity\Formulaire\RenseignementRelatifActiviteMoraleFormulaire;
use ParametrageBundle\Entity\Formulaire\FormulaireGie;
use ParametrageBundle\Entity\Formulaire\AdministrateurFormulaire;
use ParametrageBundle\Entity\Formulaire\RenseignementRelatifGroupementFormulaire;
use ParametrageBundle\Entity\Formulaire\RenseignementRelatifActiviteGroupementFormulaire;
use ParametrageBundle\Entity\Formulaire\ActiviteFormulaire;
use ParametrageBundle\Entity\Formulaire\FormulaireEi;
use ParametrageBundle\Entity\Formulaire\PersonnePhysiqueFormulaire;
use ParametrageBundle\Entity\Formulaire\ConjointFormulaire;
use ParametrageBundle\Entity\Formulaire\ActiviteEtablissementEiFormulaire;
use ParametrageBundle\Entity\Formulaire\AutrePersonnePhysiqueFormulaire;
use ParametrageBundle\Entity\Formulaire\QuartierFormulaire;
use ParametrageBundle\Entity\Formulaire\CommuneFormulaire;
use phpseclib\Net\SFTP;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonDecode;

//fin pour web service

/**
 * Description of TraitementPoleController
 *
 * @author fgueye
 */
class TraitementPoleController extends Controller
{

    public function listDossierPoleAction($data = null, $maxItemPerPage = 25)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->container->get('doctrine')->getManager();
        $data = $this->getRequest()->request->get('data');
        $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();
        if ($request->query->get('maxItemPerPage') > 0 && $request->query->get('maxItemPerPage') < 101) {
            $maxItemPerPage = $request->query->get('maxItemPerPage');
        }
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $form = $this->createForm(new DocumentCollectedSearchType(array('langue' => $langue)));

        $form->bind($request);

        if ($form->isValid()) {
            if (isset($request->query->all()['listDossierPole'])) {

                $data = $request->query->all()['listDossierPole'];
                $listedossier = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDocumentCollectedByParametres($data, $idCodeLangue, $idPole);
            } else {
                $listedossier = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findById(0);
            }


            $listedossier = $this->get('knp_paginator')->paginate($listedossier, $this->getRequest('request')->query->get('page', 1), $maxItemPerPage);
            return $this->render('ParametrageBundle:ParameterPole:dossierPole.html.twig', array('form' => $form->createView(), 'listedossier' => $listedossier, 'langue' => $idCodeLangue, 'maxItemPerPage' => $maxItemPerPage));
        } else {
            //die((string) $form->getErrors(true));
        }


        $listedossier = $this->get('knp_paginator')->paginate($listedossier, $this->getRequest('request')->query->get('page', 1), 25);
        return $this->render('ParametrageBundle:ParameterPole:dossierPole.html.twig', array('form' => $form->createView(), 'listedossier' => $listedossier, 'langue' => $idCodeLangue, 'maxItemPerPage' => $maxItemPerPage));
    }

    public function traiterDossierAction($idd)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $idPole = 0; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();

        $dossierDemande = $em->getRepository("BanquemondialeBundle:DossierDemande")->find($idd);

        $codeFormeJ = $dossierDemande->getFormeJuridique()->getSigle();
        $poleName = $pole->getNom();
        $rte = $em->getRepository("ParametrageBundle:FonctionnalitePole")->getRoutePole($idPole);

        if ($poleName == "GREFFE") {
            if ($codeFormeJ == "EI" || $codeFormeJ == "GIE") {
                $rte = $em->getRepository("ParametrageBundle:FonctionnalitePole")->getRoutePole($idPole, $codeFormeJ);
            } else {

                $rte = $em->getRepository("ParametrageBundle:FonctionnalitePole")->getRoutePole($idPole);
            }
        }
        return new RedirectResponse($this->container->get('router')->generate($rte, array('idd' => $idd)));
    }

    public function traiterDossierAnnonceurAction()
    {
    }

    public function enregistrerP1($idd, $pole)
    {
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $representant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }

        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));
        $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire(), 'langue' => 1));
        $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire2(), 'langue' => 1));


        $listeTypeFormalite = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->findBy(array('typeFormulaire' => "P1"));
        $origine = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($idd);
        $listeTypeOrigine = $em->getRepository('ParametrageBundle:TypeOrigine')->findBySiPersonnePhysique(true);
        $activiteAnterieure = $em->getRepository('BanquemondialeBundle:ActiviteAnterieure')->findOneByDossierDemande($idd);
        $personneEngageurs = $em->getRepository('BanquemondialeBundle:PersonneEngageur')->getPersEngageursByDossierDemande($idd, $langue->getId());

        $conjoints = $em->getRepository('BanquemondialeBundle:Conjoint')->findBy(array('representant' => $representant[0]['id']));

        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $libelleSignatureGreffe = "Me Alseny Fofana Greffier en chef du TPI de Kaloum";

        $parametrageSignature = $em->getRepository('DefaultBundle:ReglageActivation')->findFirst();
        if ($parametrageSignature) {
            $libelleSignatureGreffe = $parametrageSignature->getLibelleSignatureGreffe();
        }
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserP1.html.twig', array('idd' => $idd, 'dd' => $dossierDemande, 'rep' => $representant[0], 'listeTypeOrigine' => $listeTypeOrigine, 'origine' => $origine,
            'pole' => $pole, 'listeTypeFormalite' => $listeTypeFormalite, 'dateRccm' => $dateRccm, 'rccm' => $rccm,
            'activiteAnterieure' => $activiteAnterieure, 'personneEngageurs' => $personneEngageurs, 'conjoints' => $conjoints,
            'activitePrincipale' => $activitePrincipale, 'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2, 'libelleSignatureGreffe' => $libelleSignatureGreffe));
        $leFormulaire_a_delive = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->getNomFormulaireDelivre($pole->getId(), "RCC");
        $nomFichier = "formulaire" . $idd . "_" . $leFormulaire_a_delive->getId() . ".pdf";
        $formDelivre = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
        if (!$formDelivre) {
            $formulaireDelivre = new FormulaireDelivre();
            $formulaireDelivre->setPole($pole);
            $formulaireDelivre->setDossierDemande($dossierDemande);
            $formulaireDelivre->setDateCreation($dateRccm);
            $formulaireDelivre->setNomFichier($nomFichier);
            $formulaireDelivre->setLibelleFormulaireDelivre($leFormulaire_a_delive);
            $em->persist($formulaireDelivre);
        } else {
            $formDelivre->setDateCreation($dateRccm);
            $formDelivre->setLibelleFormulaireDelivre($leFormulaire_a_delive);
            $em->persist($formDelivre);
        }
        $em->flush();
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        //$html2pdf->Output('document.pdf');
        //exit;

        $html2pdf->Output($cheminDownload . $idd . '\\' . $nomFichier, 'F');
    }

    public function enregistrerG1($idd, $pole)
    {
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $representant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($idd, 1);
        $leRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findOneBy(array('dossierDemande' => $idd));
        $associe = $em->getRepository('BanquemondialeBundle:Associe')->findBy(array('dossierDemande' => $idd));
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneBy(array('dossierDemande' => $idd));
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }
        $typeF = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->find($rccm->getTypeFormaliteRccm());
        $dossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $formJ = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->getLibelleFormeJuridiqueByLanque($langue->getId(), $dossier->getFormeJuridique());
        $secAct = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossier->getSecteurActivite()));

        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $activites = "";
        if ($dossier->getSecteurActivite()) {
            $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossier->getSecteurActivite()));
            if ($activitePrincipale) {
                $activites = $activites . $activitePrincipale->getLibelle() . ";";
            }
            $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossier->getActiviteSecondaire()));
            if ($activiteSecondaire) {
                $activites = $activites . $activiteSecondaire->getLibelle() . ";";
            }
            $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossier->getActiviteSecondaire2()));
            if ($activiteSecondaire2) {
                $activites = $activites . $activiteSecondaire2->getLibelle();
            }

            if ($dossier->getActiviteSociale()) {
                $activites = $activites . $dossier->getActiviteSociale() . ";";
            }
        }
        $lesActivites = strtoupper($activites);
        $libelleSignatureGreffe = "Me Alseny Fofana Greffier en chef du TPI de Kaloum";

        $parametrageSignature = $em->getRepository('DefaultBundle:ReglageActivation')->findFirst();
        if ($parametrageSignature) {
            $libelleSignatureGreffe = $parametrageSignature->getLibelleSignatureGreffe();
        }
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserG1.html.twig', array('idd' => $idd, 'representant' => $representant
        , 'activites' => $lesActivites, 'dossier' => $dossier, 'formeJ' => $formJ, 'secAct' => $secAct, 'associe' => $associe
        , 'rccm' => $rccm, 'typeF' => $typeF, 'leRepresentant' => $leRepresentant, 'libelleSignatureGreffe' => $libelleSignatureGreffe));
        $lesFormulairesDelives = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->getNomFormulaireDelivre($pole->getId(), "RCCM");
        $nomFichier = "formulaire" . $idd . "_" . $lesFormulairesDelives->getId() . ".pdf";
        $formDelivre = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossier));
        if (!$formDelivre) {
            $formulaireDelivre = new FormulaireDelivre();
            $formulaireDelivre->setPole($pole);
            $formulaireDelivre->setDossierDemande($dossier);
            $formulaireDelivre->setDateCreation($dateRccm);
            $formulaireDelivre->setNomFichier($nomFichier);
            $formulaireDelivre->setLibelleFormulaireDelivre($lesFormulairesDelives);
            $em->persist($formulaireDelivre);
        } else {
            $formDelivre->setDateCreation($dateRccm);
            $formDelivre->setLibelleFormulaireDelivre($lesFormulairesDelives);
            $em->persist($formDelivre);
        }
        $em->flush();
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        //$html2pdf->Output('document.pdf');
        //exit;

        $html2pdf->Output($cheminDownload . "\\" . $idd . "\\" . $nomFichier, 'F');
    }

    public function enregistrerNIF($idd)
    {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);

        $libelleFormulaire = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->findOneByPole($pole);

        $nif = $em->getRepository('BanquemondialeBundle:Nif')->findOneByDossierDemande($idd);

        $dateNif = new \DateTime();
        $dateValiditeTemp = new \DateTime();
        $timestamp = $dateValiditeTemp->getTimestamp();
        $dateValidite = strftime('%d %B %Y', $timestamp);

        if ($nif) {
            $dateNif = $nif->getDate();
        }

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $gerant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());

        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));
        $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire(), 'langue' => 1));
        $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire2(), 'langue' => 1));


        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserNIF.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'dateValidite' => $dateValidite, 'nif' => $nif,
            'rep' => $gerant[0], 'rccm' => $rccm, 'activitePrincipale' => $activitePrincipale,
            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2));
        $nomFichier = "formulaire" . $idd . "_" . $libelleFormulaire->getId() . ".pdf"; //cofifier après rccm
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($cheminDownload . "\\" . $idd . "\\" . $nomFichier, 'F');

        $formDelivre = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('nomFichier' => $nomFichier, 'pole' => $pole, 'dossierDemande' => $dossierDemande));
        if (!$formDelivre) {
            $date = new \DateTime();
            $formulaireDelivre = new FormulaireDelivre();
            $formulaireDelivre->setPole($pole);
            $formulaireDelivre->setDossierDemande($dossierDemande);
            $formulaireDelivre->setDateCreation($date);
            $formulaireDelivre->setNomFichier($nomFichier);
            $formulaireDelivre->setLibelleFormulaireDelivre($libelleFormulaire);
            $em->persist($formulaireDelivre);
        } else {
            $date = new \DateTime();
            $formDelivre->setDateCreation($date);
            $em->persist($formDelivre);
        }
        $em->flush();
    }

    public function enregistrerNI($idd, $pole)
    {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();

        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }


        $listeTypeOrigine = $em->getRepository('ParametrageBundle:TypeOrigine')->findAll();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $complementCnss = $em->getRepository('BanquemondialeBundle:ComplementCnss')->findOneByDossierDemande($idd);
        $leCnss = $em->getRepository('BanquemondialeBundle:Cnss')->findOneByDossierDemande($idd);
        $gerant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());

        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));
        $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire(), 'langue' => 1));
        $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire2(), 'langue' => 1));


        if ($complementCnss) {
            $dateComplementCnss = $complementCnss->getDateImmatriculation();
        } else {
            $complementCnss = new ComplementCnss();
            $complementCnss->setDateEffet($dateComplementCnss);
        }

        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserNI.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'cnss' => $complementCnss, 'dateComplementCnss' => $dateComplementCnss, 'leCnss' => $leCnss, 'rccm' => $rccm, 'user' => $user, 'rep' => $gerant[0], 'activitePrincipale' => $activitePrincipale,
            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2));
        $lesFormulairesDelives = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->findOneBy(array('pole' => $pole->getId()));
        $nomFichier = "formulaire" . $idd . "_" . $lesFormulairesDelives->getId() . ".pdf";
        $formDelivre = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
        if (!$formDelivre) {
            $formulaireDelivre = new FormulaireDelivre();
            $formulaireDelivre->setPole($pole);
            $formulaireDelivre->setDossierDemande($dossierDemande);
            $formulaireDelivre->setDateCreation($dateRccm);
            $formulaireDelivre->setNomFichier($nomFichier);
            $formulaireDelivre->setLibelleFormulaireDelivre($lesFormulairesDelives);
            $em->persist($formulaireDelivre);
        } else {
            $formDelivre->setDateCreation($dateRccm);
            $formDelivre->setLibelleFormulaireDelivre($lesFormulairesDelives);
            $em->persist($formDelivre);
        }
        $em->flush();
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($cheminDownload . "\\" . $idd . "\\" . $nomFichier, 'F');
        //$html2pdf->Output($cheminDownload . 'formulairesDelivres\\' . $nomFichier, 'F');
        //exit;
    }

    public function enregistrerPE1($idd, $pole)
    {
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $representant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }

        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserPE1.html.twig', array('rep' => $representant[0], 'dateRccm' => $dateRccm, 'rccm' => $rccm));
        $leFormulaire_a_delive = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->getNomFormulaireDelivre($pole->getId(), "acc");
        $nomFichier = "formulaire" . $idd . "_" . $leFormulaire_a_delive->getId() . "_2.pdf";
        $formDelivre = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande, 'numero' => 2));
        if (!$formDelivre) {
            $formulaireDelivre = new FormulaireDelivre();
            $formulaireDelivre->setPole($pole);
            $formulaireDelivre->setDossierDemande($dossierDemande);
            $formulaireDelivre->setNumero(2);
            $formulaireDelivre->setDateCreation($dateRccm);
            $formulaireDelivre->setNomFichier($nomFichier);
            $formulaireDelivre->setLibelleFormulaireDelivre($leFormulaire_a_delive);
            $em->persist($formulaireDelivre);
        } else {
            $formDelivre->setDateCreation($dateRccm);
            $formDelivre->setLibelleFormulaireDelivre($leFormulaire_a_delive);
            $em->persist($formDelivre);
        }
        $em->flush();
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        //$html2pdf->Output('document.pdf');
        //exit;

        $html2pdf->Output($cheminDownload . $idd . '\\' . $nomFichier, 'F');
    }

    public function enregistrerGE4($idd, $pole)
    {
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }

        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserGE4.html.twig', array('dd' => $dossierDemande, 'dateRccm' => $dateRccm, 'rccm' => $rccm));
        $leFormulaire_a_delive = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->getNomFormulaireDelivre($pole->getId(), "acc");
        $nomFichier = "formulaire" . $idd . "_" . $leFormulaire_a_delive->getId() . "_2.pdf";
        $formDelivre = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande, 'numero' => 2));
        if (!$formDelivre) {
            $formulaireDelivre = new FormulaireDelivre();
            $formulaireDelivre->setPole($pole);
            $formulaireDelivre->setDossierDemande($dossierDemande);
            $formulaireDelivre->setNumero(2);
            $formulaireDelivre->setDateCreation($dateRccm);
            $formulaireDelivre->setNomFichier($nomFichier);
            $formulaireDelivre->setLibelleFormulaireDelivre($leFormulaire_a_delive);
            $em->persist($formulaireDelivre);
        } else {
            $formDelivre->setDateCreation($dateRccm);
            $formDelivre->setLibelleFormulaireDelivre($leFormulaire_a_delive);
            $em->persist($formDelivre);
        }
        $em->flush();
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        //$html2pdf->Output('document.pdf');
        //exit;

        $html2pdf->Output($cheminDownload . "\\" . $idd . "\\" . $nomFichier, 'F');
    }

    public function enregistrerM0($idd, $pole)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $listeAssocies = $em->getRepository('BanquemondialeBundle:Associe')->findBy(array('dossierDemande' => $idd), array('id' => 'ASC'), 3, null);
        $listeDirigeants = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $listeTypeFormalite = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->findBy(array('typeFormulaire' => null));
        $listeCommissareAuxCptes = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findByDossierDemande($dossierDemande->getId());
        $origine = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($idd);
        $listeTypeOrigine = $em->getRepository('ParametrageBundle:TypeOrigine')->findAll();
        $soussigne = $dossierDemande->getSoussigne();
        $profilCreateur = $dossierDemande->getUtilisateur()->getProfile();
        if ($profilCreateur && $profilCreateur->getDescription() == "saisi") {
            if ($dossierDemande->getTypeDossier()->getLibelle() == "Notaire") {
                $soussigne = $dossierDemande->getSoussigne();
            } else {
                if ($listeDirigeants) {
                    $firstRep = $listeDirigeants[0];
                    $soussigne = $firstRep['prenom'] . " " . $firstRep['nom'];
                }
            }
        }
        $activites = "";
        if ($dossierDemande->getSecteurActivite()) {
            $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossierDemande->getSecteurActivite()));
            if ($activitePrincipale) {
                $activites = $activites . $activitePrincipale->getLibelle() . ";";
            }
            $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossierDemande->getActiviteSecondaire()));
            if ($activiteSecondaire) {
                $activites = $activites . $activiteSecondaire->getLibelle() . ";";
            }
            $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossierDemande->getActiviteSecondaire2()));
            if ($activiteSecondaire2) {
                $activites = $activites . $activiteSecondaire2->getLibelle() . ";";
            }

            if ($dossierDemande->getActiviteSociale()) {
                $activites = $activites . $dossierDemande->getActiviteSociale() . ";";
            }
        }
        $lesActivites = strtoupper($activites);
        $libelleSignatureGreffe = "Me Alseny Fofana Greffier en chef du TPI de Kaloum";

        $parametrageSignature = $em->getRepository('DefaultBundle:ReglageActivation')->findFirst();
        if ($parametrageSignature) {
            $libelleSignatureGreffe = $parametrageSignature->getLibelleSignatureGreffe();
        }
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserM0.html.twig', array('idd' => $idd,
            'activites' => $lesActivites, 'dd' => $dossierDemande, 'listeDirigeants' => $listeDirigeants, 'listeTypeOrigine' => $listeTypeOrigine,
            'listeAssocies' => $listeAssocies, 'listeComissaires' => $listeCommissareAuxCptes, 'listeTypeFormalite' => $listeTypeFormalite, 'origine' => $origine, 'dateRccm' => $dateRccm, 'rccm' => $rccm, 'soussigne' => $soussigne, 'libelleSignatureGreffe' => $libelleSignatureGreffe));

        $leFormulaire_a_delive = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->getNomFormulaireDelivre($pole->getId(), 'RCCM');
        $nomFichier = "formulaire" . $idd . "_" . $leFormulaire_a_delive->getId() . ".pdf";
        $formDelivre = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
        if (!$formDelivre) {
            $formulaireDelivre = new FormulaireDelivre();
            $formulaireDelivre->setPole($pole);
            $formulaireDelivre->setDossierDemande($dossierDemande);
            $formulaireDelivre->setDateCreation($dateRccm);
            $formulaireDelivre->setNomFichier($nomFichier);
            $formulaireDelivre->setLibelleFormulaireDelivre($leFormulaire_a_delive);
            $em->persist($formulaireDelivre);
        } else {
            $formDelivre->setDateCreation($dateRccm);
            $formDelivre->setLibelleFormulaireDelivre($leFormulaire_a_delive);
            $em->persist($formDelivre);
        }
        $em->flush();
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);


        $html2pdf->Output($cheminDownload . $idd . '\\' . $nomFichier, 'F');
    }

    public function enregistrerME1($idd, $pole)
    {
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }

        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserME1.html.twig', array('dateRccm' => $dateRccm, 'rccm' => $rccm, 'dd' => $dossierDemande));
        $leFormulaire_a_delive = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->getNomFormulaireDelivre($pole->getId(), "acc");
        $nomFichier = "formulaire" . $idd . "_" . $leFormulaire_a_delive->getId() . ".pdf";
        $formDelivre = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande, 'numero' => 2));
        if (!$formDelivre) {
            $formulaireDelivre = new FormulaireDelivre();
            $formulaireDelivre->setPole($pole);
            $formulaireDelivre->setDossierDemande($dossierDemande);
            $formulaireDelivre->setNumero(2);
            $formulaireDelivre->setDateCreation($dateRccm);
            $formulaireDelivre->setNomFichier($nomFichier);
            $formulaireDelivre->setLibelleFormulaireDelivre($leFormulaire_a_delive);
            $em->persist($formulaireDelivre);
        } else {
            $formDelivre->setDateCreation($dateRccm);
            $formDelivre->setLibelleFormulaireDelivre($leFormulaire_a_delive);
            $em->persist($formDelivre);
        }
        $em->flush();
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        //$html2pdf->Output('document.pdf');
        //exit;

        $html2pdf->Output($cheminDownload . $idd . '\\' . $nomFichier, 'F');
    }

    public function delivrerDossierAction(Request $request)
    {
        // die(dump('ok'));
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $idPole = 0;
        $nomDuPole = null;
        if ($pole) {
            $idPole = $pole->getId();
            $nomDuPole = $this->get('translator')->trans($pole->getNom());
        }
        $idd = $request->get('idd');
        $retour = '';
        $sms = "";
        $dateActu = new \DateTime();

        //$pole = $em->getRepository('ParametrageBundle:Pole')->find($idPole);
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $texte = $this->get('translator')->trans('envoi_email_soumission_saisie', array('%denominationSociale%' => $dossierDemande->getDenominationSociale()));

        if ($request->getMethod() == 'POST') {

            $documentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
            if ($documentCollected) {
                //si le dossier de reception n'existe pas le creer
                $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
                $cheminUpload = $chemin->getNom();

                $temp = $cheminUpload . $idd . '\\';
                if (!is_dir($temp)) {
                    mkdir($temp);
                }

                //Mise a jour du pole en cours
                $statutTraitement = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(2);

                $documentCollected->setStatutTraitement($statutTraitement);
                $documentCollected->setMotif(null);
                $documentCollected->setDateDelivrance($dateActu);
                $em->persist($documentCollected);
                $em->flush();


                //enregistrer PDF
                if (strtolower($pole->getSigle()) == 'bni') {
                    $this->enregistrerNIF($idd);
                    $texteMail = $this->get('translator')->trans('envoi_email_soumission_nif', array('%denominationSociale%' => $dossierDemande->getDenominationSociale()));
                } else if ($pole->getSigle() == "CNSS") {
                    $this->enregistrerNI($idd, $pole);
                    $texteMail = $this->get('translator')->trans('envoi_email_soumission_cnss', array('%denominationSociale%' => $dossierDemande->getDenominationSociale()));
                } else {
                    $codeFormJ = $dossierDemande->getFormeJuridique()->getSigle();
                    //die(dump($codeFormJ));
                    if ($codeFormJ == "EI") {
                        $this->enregistrerP1($idd, $pole);
                        $this->enregistrerPE1($idd, $pole);
                        $texteMail = $this->get('translator')->trans('envoi_email_soumission_greffe', array('%denominationSociale%' => $dossierDemande->getDenominationSociale()));
                    } else if ($codeFormJ == "GIE") {
                        $this->enregistrerG1($idd, $pole);
                        $this->enregistrerGE4($idd, $pole);
                        $texteMail = $this->get('translator')->trans('envoi_email_soumission_greffe', array('%denominationSociale%' => $dossierDemande->getDenominationSociale()));
                    } else {
                        $this->enregistrerM0($idd, $pole);
                        $this->enregistrerME1($idd, $pole);
                        $texteMail = $this->get('translator')->trans('envoi_email_soumission_greffe', array('%denominationSociale%' => $dossierDemande->getDenominationSociale()));
                    }
                }
                //fin
                //Mise a jour Pole suivant
                $ordre = $documentCollected->getOrdre();
                $statutEncours = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(1);
                //si on a pas d'autres documentcollected du meme ordre                
                $documentsCollectedMemeOrdre = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDocumentMemeOrdre($ordre, $dossierDemande, $statutEncours);

                //on verifie si c'etait le dernier pole ou pas
                $documentSuivant = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findPoleSuivant($ordre, $idd);
                //si on a d'autres poles du meme ordre on envoi juste les notifications
                $notif = $this->container->get('utilisateurs.notification');
                if ($documentsCollectedMemeOrdre) {
                    $message = $this->get('translator')->trans('message_dossier_en_cours');
                    $objet = $this->get('translator')->trans('traitement_dossier_en_cours');
                } //on cherche la liste des documentcollected du prochain ordre
                else {
                    if ($documentSuivant) {
                        $statutEncours = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(1);
                        $polesSuivants = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findPolesSuivants($documentSuivant[0]->getOrdre(), $idd);
                        if ($polesSuivants) {
                            $message = $this->get('translator')->trans('message_dossier_recu');
                            $objet = $this->get('translator')->trans('reception_dossier_objet');
                            foreach ($polesSuivants as $poleSuivant) {
                                $poleSuivant->setStatutTraitement($statutEncours);
                                $dateEnCours = $poleSuivant->getDateSoumission() ? $poleSuivant->getDateSoumission() : $dateActu;
                                $poleSuivant->setDateSoumission($dateEnCours);
                                $em->persist($poleSuivant);
                                foreach ($poleSuivant->getPole()->getUtilisateur() as $user)
                                    $notif->notifier($dossierDemande->getNumeroDossier() . ' ' . $message, $user, $objet);
                            }
                            $message = $this->get('translator')->trans('message_dossier_en_cours');
                            $objet = $this->get('translator')->trans('traitement_dossier_en_cours');

                            if ($pole->getTypePole() && $pole->getTypePole()->getCode() == "00") {
                                //die('recev');
                                $notif->notifier($dossierDemande->getNumeroDossier() . ' ' . $message, $dossierDemande->getUtilisateur(), $objet);

                                $sujet = $this->get('translator')->trans('sujet_notification_email');
                                $texte = $this->get('translator')->trans('texte_notification_email_recevabilite') . " " . $dossierDemande->getNumeroDossier();
                                $this->sendMail($sujet, $texte, $dossierDemande->getUtilisateur());

                                if ($request->getLocale() == 'fr') {
                                    $sms = 'Votre+dossier+' . $dossierDemande->getNumeroDossier() . '+a+été+traité+par+le+pole+' . str_replace(" ", "+", $nomDuPole);
                                    //$retour = $this->sendSMS('fr', $sms, $telephone);
                                } else if ($request->getLocale() == 'en') {
                                    $sms = 'Your+request+' . $dossierDemande->getNumeroDossier() . '+has+been+performed+by+' . str_replace(" ", "+", $nomDuPole);
                                    //$retour = $this->sendSMS('en', $sms, $telephone);
                                } else {
                                    $sms = 'Your+request+' . $dossierDemande->getNumeroDossier() . '+has+been+performed+by+' . str_replace(" ", "+", $nomDuPole);
                                    //$retour = $this->sendSMS($request->getLocale(), $sms, $telephone);
                                }
                            }
                        }
                    } else {
                        //si dernier Pole                    
                        $dossierDemande->setStatut(2);
                        $date = new \DateTime();
                        $dossierDemande->setDateDelivrance($date);
                        $em->persist($dossierDemande);
                        $message = $this->get('translator')->trans('message_dossier_delivre');
                        //Envoie d'une notification 
                        $objet = $this->get('translator')->trans('dossier_delivre');
                        $notif->notifier($dossierDemande->getNumeroDossier() . ' ' . $message, $dossierDemande->getUtilisateur(), $objet);

                        $sujet = $this->get('translator')->trans('sujet_notification_email');
                        $texte = $this->get('translator')->trans('texte_notification_dossier_delivre') . " " . $dossierDemande->getNumeroDossier();
                        $this->sendMail($sujet, $texte, $dossierDemande->getUtilisateur());

                        if ($request->getLocale() == 'fr') {
                            $sms = 'Votre+dossier+' . $dossierDemande->getNumeroDossier() . '+a+été+délivré+par+le+pole+' . str_replace(" ", "+", $nomDuPole);
                            //$retour = $this->sendSMS('fr', $sms, $telephone);
                        } elseif ($request->getLocale() == 'en') {
                            $sms = 'Your+request+' . $dossierDemande->getNumeroDossier() . '+has+been+issued+by+' . str_replace(" ", "+", $nomDuPole);
                            //$retour = $this->sendSMS($request->getLocale(), $sms, $telephone);
                        } else
                            $sms = 'Your+request+' . $dossierDemande->getNumeroDossier() . '+has+been+issued+by+' . str_replace(" ", "+", $nomDuPole);
                    }
                }

                //Envoie d'une notification 

                $em->flush();

                //envoi email promoteur
                if ($dossierDemande->getEmailPromoteur()) {
                    $sujet = $this->get('translator')->trans('sujet_notification_email');
                    $ancienEmailUtilisateur = $dossierDemande->getUtilisateur()->getEmail();
                    $dossierDemande->getUtilisateur()->setEmail($this->valideMail($dossierDemande->getEmailPromoteur()));
                    try {
                        $this->sendMail($sujet, $texteMail, $dossierDemande->getUtilisateur());
                    } catch (\Exception $e) {
                        $translated = $this->get('translator')->trans("error_send_mail");
                        $this->get('session')->getFlashBag()->add('error', $translated);
                        $this->get('logger')->error($e->getMessage());
                    } finally {
                        $dossierDemande->getUtilisateur()->setEmail($this->valideMail($ancienEmailUtilisateur));
                    }
                }
                return new JsonResponse(array('resultat' => '1', 'url' => $sms));
            }
            //return new JsonResponse(array('resultat' => '1'));
        }

        return new JsonResponse(array('resultat' => '0'));
    }

    public function traitercnssAction($idd)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $message = "";
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);

        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));
        $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire(), 'langue' => 1));
        $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire2(), 'langue' => 1));

        $documentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
        $motif = $documentCollected->getMotif();
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $rep = $em->getRepository('BanquemondialeBundle:Representant')->findOneByDossierDemande($idd);
        $cnss = $em->getRepository('BanquemondialeBundle:Cnss')->findOneByDossierDemande($idd);
        $aguipe = $em->getRepository('BanquemondialeBundle:Aguipe')->findOneByDossierDemande($idd);
        $complementCnss = $em->getRepository('BanquemondialeBundle:ComplementCnss')->findOneByDossierDemande($idd);
        $dateComplementCnss = new \DateTime();
        if ($complementCnss) {
            $dateComplementCnss = $complementCnss->getDateImmatriculation();
        } else {
            $complementCnss = new ComplementCnss();
            $complementCnss->setDateEffet($dateComplementCnss);
        }
        if ($request->getMethod() == 'POST') {
            if ($request->request->has('textAreaModifier')) {
                $motif = $request->get('textAreaModifier');
                if (strlen($motif) <= 255) {
                    $documentCollected->setMotif($motif);
                    $statutTraitementModifier = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(3);
                    $documentCollected->setStatutTraitement($statutTraitementModifier);
                    $em->persist($documentCollected);

                    $dossierDemande->setStatut(3);
                    $em->persist($dossierDemande);
                    $em->flush();
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $quittance = $em->getRepository('BanquemondialeBundle\Entity\Quittance')->findOneBy(['dossierDemande' => $idd]);
// die(dump($quittance));
                    $this->get('monservices')->updatePaiementOrangeWhenUpdateDossier($quittance->getId());

                    $notif = $this->container->get('utilisateurs.notification');
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $message2 = $this->get('translator')->trans('par_le_pole');
                    $nomDuPole = $this->get('translator')->trans($pole->getNom());
                    //envoi de la notification
                    $objet = $this->get('translator')->trans('demande_modification_dossier_envoye');
                    $notif->notifier($dossierDemande->getNumeroDossier() . ' ' . $message . ' ' . $message2 . ' ' . $nomDuPole, $dossierDemande->getUtilisateur(), $objet);
                }
            } else {
                if (!$complementCnss) {
                    $newComplementCnss = new ComplementCnss();

                    $newComplementCnss->setDateImmatriculation($dateComplementCnss);
                    $newComplementCnss->setDossierDemande($dossierDemande);
                    $newComplementCnss->setNumeroEmployeur($request->get('numeroEmployeur'));

                    $newComplementCnss->setDateEffet(new \DateTime($request->get('dateEffet')));
                    $newComplementCnss->setCategorie($request->get('categorie'));
                    $newComplementCnss->setPlafonne($request->get('plafonne'));
                    $newComplementCnss->setPlancher($request->get('plancher'));

                    $em->persist($newComplementCnss);
                    $em->flush();
                    $message = $this->get('translator')->trans('message.ajout_succes ');
                    return new RedirectResponse($this->container->get('router')->generate('cnss_traiterDossier', array('idd' => $idd)));
                } else {

                    $complementCnss->setDateImmatriculation($dateComplementCnss);
                    $complementCnss->setDossierDemande($dossierDemande);
                    $complementCnss->setNumeroEmployeur($request->get('numeroEmployeur'));
                    $complementCnss->setDateEffet(new \DateTime($request->get('dateEffet')));
                    $complementCnss->setCategorie($request->get('categorie'));
                    $complementCnss->setPlafonne($request->get('plafonne'));
                    $complementCnss->setPlancher($request->get('plancher'));

                    $em->persist($complementCnss);
                    $em->flush();
                    return new RedirectResponse($this->container->get('router')->generate('cnss_traiterDossier', array('idd' => $idd)));
                }
            }
        }

        return $this->render('ParametrageBundle:ParameterPole:traiterCnss.html.twig', array('idd' => $idd, 'cnss' => $cnss,
            'dd' => $dossierDemande, 'message' => $message, 'motif' => $motif, 'aguipe' => $aguipe, 'user' => $user,
            'pole' => $pole, 'dateComplementCnss' => $dateComplementCnss, 'complementCnss' => $complementCnss,
            'rccm' => $rccm, 'statutTraitrement' => $documentCollected->getStatutTraitement(), 'rep' => $rep, 'activitePrincipale' => $activitePrincipale,
            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2
        ));
    }

    public function traiterSocieteCommercialeAction($idd)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $message = "";
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));
        $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire(), 'langue' => 1));
        $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire2(), 'langue' => 1));

        $documentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
        $motif = $documentCollected->getMotif();
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $styleNumeroFormalite = null;
        $styleNumeroEntreprise = null;
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }
        $date = new \DateTime();
        $listeTypeFormalite = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->findBy(array('typeFormulaire' => null));
        $listeAssocies = $em->getRepository('BanquemondialeBundle:Associe')->findBy(array('dossierDemande' => $idd), array('id' => 'ASC'), 3, null);
        $listeAdministrateurs = $em->getRepository('BanquemondialeBundle:Administrateur')->findBy(array('dossierDemande' => $dossierDemande->getId()), array('id' => 'asc'), 3);

        $listeDirigeants = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $listeCommissareAuxCptes = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findByDossierDemande($dossierDemande->getId());
        $origine = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($idd);
        $listeTypeOrigine = $em->getRepository('ParametrageBundle:TypeOrigine')->findAll();
        $lettreFormulaire = "B";
        if ($listeTypeFormalite) {
            $lettreFormulaire = $listeTypeFormalite[0]->getLettreFormulaire();
        }


        if (date('Y') >= 2018) {
            $debutNumRccm = "GN.TCC." . $dateRccm->format("Y") . "." . $lettreFormulaire . ".";
            $debutNumRccmFormalite = "GN.TCC." . $dateRccm->format("Y") . ".";
        } else {
            $debutNumRccm = "GC.TCC." . $dateRccm->format("Y") . "." . $lettreFormulaire . ".";
            $debutNumRccmFormalite = "GC.TCC." . $dateRccm->format("Y") . ".";
        }
        $numSequentiel = "";
        $numSequentielEntreprise = "";
        if ($rccm) {
            $tableau = explode('.', $rccm->getNumRccmFormalite());
            if ($tableau) {
                $numSequentiel = ($tableau[3]) ? $tableau[3] : "";
            }
            $tableauEntreprise = explode('.', $rccm->getNumRccmEntreprise());

            if ($tableauEntreprise) {
                $numSequentielEntreprise = ($tableauEntreprise[4]) ? $tableauEntreprise[4] : "";
            }
        }
        $activites = "";
        if ($dossierDemande->getSecteurActivite()) {
            $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossierDemande->getSecteurActivite()));
            if ($activitePrincipale) {
                $activites = $activites . $activitePrincipale->getLibelle() . ";";
            }
            $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossierDemande->getActiviteSecondaire()));
            if ($activiteSecondaire) {
                $activites = $activites . $activiteSecondaire->getLibelle() . ";";
            }
            $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossierDemande->getActiviteSecondaire2()));
            if ($activiteSecondaire2) {
                $activites = $activites . $activiteSecondaire2->getLibelle();
            }

            if ($dossierDemande->getActiviteSociale()) {
                $activites = $activites . $dossierDemande->getActiviteSociale() . ";";
            }
        }
        $lesActivites = strtoupper($activites);
        if ($request->getMethod() == 'POST') {
            if ($request->request->has('textAreaModifier')) {
                $motif = $request->get('textAreaModifier');
                if (strlen($motif) <= 255) {
                    $documentCollected->setMotif($motif);
                    $statutTraitementModifier = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(3);
                    $documentCollected->setStatutTraitement($statutTraitementModifier);
                    $documentCollected->setDateDerniereModification($date);
                    $em->persist($documentCollected);

                    $dossierDemande->setStatut(3);
                    $em->persist($dossierDemande);
                    $em->flush();
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $quittance = $em->getRepository('BanquemondialeBundle\Entity\Quittance')->findOneBy(['dossierDemande' => $idd]);
                    // die(dump($quittance));
                    $this->get('monservices')->updatePaiementOrangeWhenUpdateDossier($quittance->getId());

                    $notif = $this->container->get('utilisateurs.notification');
                    $message2 = $this->get('translator')->trans('par_le_pole');
                    $nomDuPole = $this->get('translator')->trans($pole->getNom());
                    //envoi de la notification
                    $objet = $this->get('translator')->trans('demande_modification_dossier_envoye');
                    $notif->notifier($dossierDemande->getNumeroDossier() . ' ' . $message . ' ' . $message2 . ' ' . $nomDuPole, $dossierDemande->getUtilisateur(), $objet);
                }
            } else {
                $complementNumRccm = $request->get('complementNumRccm');

                if (!$rccm) {
                    $newRccm = new Rccm();
                    $idTypeF = $request->get('radioFormalite');
                    $rccmFormalite = $debutNumRccmFormalite . $request->get('rccmFormalite');
                    $rccmEntreprise = $complementNumRccm . $request->get('rccmEntreprise');
                    $formalite = $em->getRepository("BanquemondialeBundle:TypeFormaliteRccm")->find($idTypeF);

                    $newRccm->setDate($dateRccm);
                    $newRccm->setDossierDemande($dossierDemande);
                    $newRccm->setNumRccmEntreprise($rccmEntreprise);
                    $newRccm->setNumRccmFormalite($rccmFormalite);
                    $newRccm->setTypeFormaliteRccm($formalite);

                    $rccmFormaliteTest = $em->getRepository('BanquemondialeBundle:Rccm')->verifierNumeroFormalite($rccmFormalite, $idd);
                    $rccmEntrepriseTest = $em->getRepository('BanquemondialeBundle:Rccm')->verifierNumeroEntreprise($rccmEntreprise, $idd);

                    if ($rccmFormaliteTest || $rccmEntrepriseTest) {
                        if ($rccmFormaliteTest) {
                            $message = $this->get('translator')->trans('numero_formalite_existe');
                            $this->get('session')->getFlashBag()->add('error', $message);
                            $styleNumeroFormalite = "border:1px red solid;";
                        }
                        if ($rccmEntrepriseTest) {
                            $message = $this->get('translator')->trans('numero_entreprise_existe');
                            $this->get('session')->getFlashBag()->add('error', $message);
                            $styleNumeroEntreprise = "border:1px red solid;";
                        }


                        return $this->render('ParametrageBundle:ParameterPole:traiterDossierM0.html.twig', array('idd' => $idd,
                            'activites' => $lesActivites, 'dd' => $dossierDemande, 'listeDirigeants' => $listeDirigeants, 'message' => $message, 'motif' => $motif, 'listeTypeOrigine' => $listeTypeOrigine,
                            'listeAssocies' => $listeAssocies, 'listeComissaires' => $listeCommissareAuxCptes, 'origine' => $origine, "numSequentiel" => $numSequentiel,
                            'pole' => $pole, 'listeTypeFormalite' => $listeTypeFormalite, 'dateRccm' => $dateRccm, "formatRccm" => $debutNumRccm,
                            'numSequentielEntreprise' => $numSequentielEntreprise, 'debutRccmFormalite' => $debutNumRccmFormalite,
                            'rccm' => $rccm, 'statutTraitrement' => $documentCollected->getStatutTraitement(), 'listeAdministrateurs' => $listeAdministrateurs,
                            'styleNumeroFormalite' => $styleNumeroFormalite, 'styleNumeroEntreprise' => $styleNumeroEntreprise, 'activitePrincipale' => $activitePrincipale,
                            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2));
                    }


                    $em->persist($newRccm);
                    $em->flush();
                    $message = $this->get('translator')->trans('message.ajout_succes ');
                    return new RedirectResponse($this->container->get('router')->generate('traiter_pers_morale', array('idd' => $idd)));
                } else {

                    $idTypeF = $request->get('radioFormalite');
                    $rccmFormalite = $debutNumRccmFormalite . $request->get('rccmFormalite');
                    $rccmEntreprise = $complementNumRccm . $request->get('rccmEntreprise');
                    $formalite = $em->getRepository("BanquemondialeBundle:TypeFormaliteRccm")->find($idTypeF);

                    $rccm->setDate($dateRccm);
                    $rccm->setDossierDemande($dossierDemande);
                    $rccm->setNumRccmEntreprise($rccmEntreprise);
                    $rccm->setNumRccmFormalite($rccmFormalite);
                    $rccm->setTypeFormaliteRccm($formalite);

                    $rccmFormaliteTest = $em->getRepository('BanquemondialeBundle:Rccm')->verifierNumeroFormalite($rccmFormalite, $idd);
                    $rccmEntrepriseTest = $em->getRepository('BanquemondialeBundle:Rccm')->verifierNumeroEntreprise($rccmEntreprise, $idd);

                    if ($rccmFormaliteTest || $rccmEntrepriseTest) {
                        if ($rccmFormaliteTest) {
                            $message = $this->get('translator')->trans('numero_formalite_existe');
                            $this->get('session')->getFlashBag()->add('error', $message);
                            $styleNumeroFormalite = "border:1px red solid;";
                        }
                        if ($rccmEntrepriseTest) {
                            $message = $this->get('translator')->trans('numero_entreprise_existe');
                            $this->get('session')->getFlashBag()->add('error', $message);
                            $styleNumeroEntreprise = "border:1px red solid;";
                        }


                        return $this->render('ParametrageBundle:ParameterPole:traiterDossierM0.html.twig', array('idd' => $idd,
                            'activites' => $lesActivites, 'dd' => $dossierDemande, 'listeDirigeants' => $listeDirigeants, 'message' => $message, 'motif' => $motif, 'listeTypeOrigine' => $listeTypeOrigine,
                            'listeAssocies' => $listeAssocies, 'listeComissaires' => $listeCommissareAuxCptes, 'origine' => $origine, "numSequentiel" => $numSequentiel,
                            'pole' => $pole, 'listeTypeFormalite' => $listeTypeFormalite, 'dateRccm' => $dateRccm, "formatRccm" => $debutNumRccm,
                            'numSequentielEntreprise' => $numSequentielEntreprise, 'debutRccmFormalite' => $debutNumRccmFormalite,
                            'rccm' => $rccm, 'statutTraitrement' => $documentCollected->getStatutTraitement(), 'listeAdministrateurs' => $listeAdministrateurs,
                            'styleNumeroFormalite' => $styleNumeroFormalite, 'styleNumeroEntreprise' => $styleNumeroEntreprise, 'activitePrincipale' => $activitePrincipale,
                            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2));
                    }


                    $em->persist($rccm);
                    $em->flush();
                    return new RedirectResponse($this->container->get('router')->generate('traiter_pers_morale', array('idd' => $idd)));
                }
            }
        }

        return $this->render('ParametrageBundle:ParameterPole:traiterDossierM0.html.twig', array('idd' => $idd,
            'activites' => $lesActivites, 'dd' => $dossierDemande, 'listeDirigeants' => $listeDirigeants, 'message' => $message, 'motif' => $motif, 'listeTypeOrigine' => $listeTypeOrigine,
            'listeAssocies' => $listeAssocies, 'listeComissaires' => $listeCommissareAuxCptes, 'origine' => $origine, "numSequentiel" => $numSequentiel,
            'pole' => $pole, 'listeTypeFormalite' => $listeTypeFormalite, 'dateRccm' => $dateRccm, "formatRccm" => $debutNumRccm, 'debutRccmFormalite' => $debutNumRccmFormalite,
            'numSequentielEntreprise' => $numSequentielEntreprise, 'styleNumeroFormalite' => $styleNumeroFormalite, 'styleNumeroEntreprise' => $styleNumeroEntreprise,
            'rccm' => $rccm, 'statutTraitrement' => $documentCollected->getStatutTraitement(), 'listeAdministrateurs' => $listeAdministrateurs, 'activitePrincipale' => $activitePrincipale,
            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2
        ));
    }

    public function traiterGroupementInteretEconomiqueAction($idd)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $message = "";
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $documentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
        $motif = $documentCollected->getMotif();
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }
        $date = new \DateTime();
        $listeTypeFormalite = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->findBy(array('typeFormulaire' => null));
        $listeAssocies = $em->getRepository('BanquemondialeBundle:Associe')->findBy(array('dossierDemande' => $idd), array('id' => 'ASC'), 3, null);
        $listeAdministrateurs = $em->getRepository('BanquemondialeBundle:Administrateur')->findBy(array('dossierDemande' => $dossierDemande->getId()), array('id' => 'asc'), 3);

        $listeDirigeants = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $listeCommissareAuxCptes = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findByDossierDemande($dossierDemande->getId());
        $origine = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($idd);
        $listeTypeOrigine = $em->getRepository('ParametrageBundle:TypeOrigine')->findAll();
        if ($request->getMethod() == 'POST') {
            if ($request->request->has('textAreaModifier')) {
                $motif = $request->get('textAreaModifier');
                if (strlen($motif) <= 255) {
                    $documentCollected->setMotif($motif);
                    $statutTraitementModifier = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(3);
                    $documentCollected->setDateDerniereModification($date);
                    $documentCollected->setStatutTraitement($statutTraitementModifier);
                    $em->persist($documentCollected);

                    $dossierDemande->setStatut(3);
                    $em->persist($dossierDemande);
                    $em->flush();
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $quittance = $em->getRepository('BanquemondialeBundle\Entity\Quittance')->findOneBy(['dossierDemande' => $idd]);
                    // die(dump($quittance));
                    $this->get('monservices')->updatePaiementOrangeWhenUpdateDossier($quittance->getId());

                    $notif = $this->container->get('utilisateurs.notification');
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $message2 = $this->get('translator')->trans('par_le_pole');
                    $nomDuPole = $this->get('translator')->trans($pole->getNom());
                    //envoi de la notification
                    $objet = $this->get('translator')->trans('demande_modification_dossier_envoye');
                    $notif->notifier($dossierDemande->getNumeroDossier() . ' ' . $message . ' ' . $message2 . ' ' . $nomDuPole, $dossierDemande->getUtilisateur(), $objet);
                }
            } else {
                if (!$rccm) {
                    $newRccm = new Rccm();
                    $idTypeF = $request->get('radioFormalite');
                    $rccmFormalite = $request->get('rccmFormalite');
                    $rccmEntreprise = $request->get('rccmEntreprise');
                    $formalite = $em->getRepository("BanquemondialeBundle:TypeFormaliteRccm")->find($idTypeF);

                    $newRccm->setDate($dateRccm);
                    $newRccm->setDossierDemande($dossierDemande);
                    $newRccm->setNumRccmEntreprise($rccmFormalite);
                    $newRccm->setNumRccmFormalite($rccmEntreprise);
                    $newRccm->setTypeFormaliteRccm($formalite);

                    $em->persist($newRccm);
                    $em->flush();
                    $message = $this->get('translator')->trans('message.ajout_succes ');
                    return new RedirectResponse($this->container->get('router')->generate('traiter_pers_morale', array('idd' => $idd)));
                } else {

                    $idTypeF = $request->get('radioFormalite');
                    $rccmFormalite = $request->get('rccmFormalite');
                    $rccmEntreprise = $request->get('rccmEntreprise');
                    $formalite = $em->getRepository("BanquemondialeBundle:TypeFormaliteRccm")->find($idTypeF);

                    $rccm->setDate($dateRccm);
                    $rccm->setDossierDemande($dossierDemande);
                    $rccm->setNumRccmEntreprise($rccmFormalite);
                    $rccm->setNumRccmFormalite($rccmEntreprise);
                    $rccm->setTypeFormaliteRccm($formalite);

                    $em->persist($rccm);
                    $em->flush();
                    return new RedirectResponse($this->container->get('router')->generate('traiter_pers_morale', array('idd' => $idd)));
                }
            }
        }

        return $this->render('ParametrageBundle:ParameterPole:traiterDossierM0.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'listeDirigeants' => $listeDirigeants, 'message' => $message, 'motif' => $motif, 'listeTypeOrigine' => $listeTypeOrigine,
            'listeAssocies' => $listeAssocies, 'listeComissaires' => $listeCommissareAuxCptes, 'origine' => $origine,
            'pole' => $pole, 'listeTypeFormalite' => $listeTypeFormalite, 'dateRccm' => $dateRccm,
            'rccm' => $rccm, 'statutTraitrement' => $documentCollected->getStatutTraitement(), 'listeAdministrateurs' => $listeAdministrateurs
        ));
    }

    public function traiterPersonnePhysiqueAction($idd)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $message = "";
        $styleNumeroFormalite = null;
        $styleNumeroEntreprise = null;
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $documentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
        $motif = $documentCollected->getMotif();

        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));
        $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire(), 'langue' => 1));
        $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire2(), 'langue' => 1));

        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }
        $date = new \DateTime();
        $listeTypeFormalite = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->findBy(array('typeFormulaire' => "P1"));
        $gerant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $origine = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($idd);
        $listeTypeOrigine = $em->getRepository('ParametrageBundle:TypeOrigine')->findBySiPersonnePhysique(true);
        $activiteAnterieure = $em->getRepository('BanquemondialeBundle:ActiviteAnterieure')->findOneByDossierDemande($idd);
        $personneEngageurs = $em->getRepository('BanquemondialeBundle:PersonneEngageur')->getPersEngageursByDossierDemande($idd, $langue->getId());

        $conjoints = $em->getRepository('BanquemondialeBundle:Conjoint')->findBy(array('representant' => $gerant[0]['id']));
        //die(dump($conjoints));
        $lettreFormulaire = "B";
        if ($listeTypeFormalite) {
            $lettreFormulaire = $listeTypeFormalite[0]->getLettreFormulaire();
        }


        if (date('Y') >= 2018) {
            $debutNumRccm = "GN.TCC." . $dateRccm->format("Y") . "." . $lettreFormulaire . ".";
        } else {
            $debutNumRccm = "GC.TCC." . $dateRccm->format("Y") . "." . $lettreFormulaire . ".";
        }

        $numSequentiel = "";
        $numSequentielEntreprise = "";
        if ($rccm) {
            $tableau = explode('.', $rccm->getNumRccmFormalite());
            if ($tableau) {
                $numSequentiel = ($tableau[3]) ? $tableau[3] : "";
            }
            $tableauEntreprise = explode('.', $rccm->getNumRccmEntreprise());

            if ($tableauEntreprise) {
                $numSequentielEntreprise = ($tableauEntreprise[4]) ? $tableauEntreprise[4] : "";
            }
        }
        if ($request->getMethod() == 'POST') {

            if ($request->request->has('textAreaModifier')) {
                $motif = $request->get('textAreaModifier');
                if (strlen($motif) <= 255) {
                    $documentCollected->setMotif($motif);
                    $statutTraitementModifier = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(3);
                    $documentCollected->setDateDerniereModification($date);
                    $documentCollected->setStatutTraitement($statutTraitementModifier);
                    $em->persist($documentCollected);


                    $dossierDemande->setStatut(3);
                    $em->persist($dossierDemande);
                    $em->flush();
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $quittance = $em->getRepository('BanquemondialeBundle\Entity\Quittance')->findOneBy(['dossierDemande' => $idd]);
// die(dump($quittance));
                    $this->get('monservices')->updatePaiementOrangeWhenUpdateDossier($quittance->getId());

                    $notif = $this->container->get('utilisateurs.notification');
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $message2 = $this->get('translator')->trans('par_le_pole');
                    $nomDuPole = $this->get('translator')->trans($pole->getNom());
                    //envoi de la notification
                    $objet = $this->get('translator')->trans('demande_modification_dossier_envoye');
                    $notif->notifier($dossierDemande->getNumeroDossier() . ' ' . $message . ' ' . $message2 . ' ' . $nomDuPole, $dossierDemande->getUtilisateur(), $objet);


                    return new RedirectResponse($this->container->get('router')->generate('traiter_pers_physique', array('idd' => $idd)));
                }
            } else {
                $complementNumRccm = $request->get('complementNumRccm');
                if (!$rccm) {

                    $newRccm = new Rccm();
                    $idTypeF = $request->get('radioFormalite');
                    $rccmFormalite = substr($complementNumRccm, 0, -2) . $request->get('rccmFormalite');
                    $rccmEntreprise = $complementNumRccm . $request->get('rccmEntreprise');
                    $formalite = $em->getRepository("BanquemondialeBundle:TypeFormaliteRccm")->find($idTypeF);

                    $rccmFormaliteTest = $em->getRepository('BanquemondialeBundle:Rccm')->verifierNumeroFormalite($rccmFormalite, $idd);
                    $rccmEntrepriseTest = $em->getRepository('BanquemondialeBundle:Rccm')->verifierNumeroEntreprise($rccmEntreprise, $idd);

                    if ($rccmFormaliteTest || $rccmEntrepriseTest) {
                        if ($rccmFormaliteTest) {
                            $message = $this->get('translator')->trans('numero_formalite_existe');
                            $this->get('session')->getFlashBag()->add('error', $message);
                            $styleNumeroFormalite = "border:1px red solid;";
                        }
                        if ($rccmEntrepriseTest) {
                            $message = $this->get('translator')->trans('numero_entreprise_existe');
                            $this->get('session')->getFlashBag()->add('error', $message);
                            $styleNumeroEntreprise = "border:1px red solid;";
                        }


                        return $this->render('ParametrageBundle:ParameterPole:traiterDossierP1.html.twig', array('idd' => $idd,
                            'dd' => $dossierDemande, 'rep' => $gerant[0], 'message' => $message, 'motif' => $motif, 'listeTypeOrigine' => $listeTypeOrigine,
                            'origine' => $origine, "formatRccm" => $debutNumRccm, "numSequentiel" => $numSequentiel,
                            'pole' => $pole, 'listeTypeFormalite' => $listeTypeFormalite, 'dateRccm' => $dateRccm,
                            'rccm' => $rccm, 'statutTraitrement' => $documentCollected->getStatutTraitement(), 'numSequentielEntreprise' => $numSequentielEntreprise,
                            'activiteAnterieure' => $activiteAnterieure, 'personneEngageurs' => $personneEngageurs, 'conjoints' => $conjoints,
                            'styleNumeroFormalite' => $styleNumeroFormalite, 'styleNumeroEntreprise' => $styleNumeroEntreprise, 'activitePrincipale' => $activitePrincipale,
                            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2
                        ));
                    }

                    $newRccm->setDate($dateRccm);
                    $newRccm->setDossierDemande($dossierDemande);
                    $newRccm->setNumRccmEntreprise($rccmEntreprise);
                    $newRccm->setNumRccmFormalite($rccmFormalite);
                    $newRccm->setTypeFormaliteRccm($formalite);

                    $em->persist($newRccm);
                    $em->flush();
                    $message = $this->get('translator')->trans('message.ajout_succes ');
                } else {

                    $idTypeF = $request->get('radioFormalite');
                    $rccmFormalite = $complementNumRccm . $request->get('rccmFormalite');
                    $rccmEntreprise = $complementNumRccm . $request->get('rccmEntreprise');
                    $formalite = $em->getRepository("BanquemondialeBundle:TypeFormaliteRccm")->find($idTypeF);

                    $rccmFormaliteTest = $em->getRepository('BanquemondialeBundle:Rccm')->verifierNumeroFormalite($rccmFormalite, $idd);
                    $rccmEntrepriseTest = $em->getRepository('BanquemondialeBundle:Rccm')->verifierNumeroEntreprise($rccmEntreprise, $idd);

                    if ($rccmFormaliteTest || $rccmEntrepriseTest) {
                        if ($rccmFormaliteTest) {
                            $message = $this->get('translator')->trans('numero_formalite_existe');
                            $this->get('session')->getFlashBag()->add('error', $message);
                            $styleNumeroFormalite = "border:1px red solid;";
                        }
                        if ($rccmEntrepriseTest) {
                            $message = $this->get('translator')->trans('numero_entreprise_existe');
                            $this->get('session')->getFlashBag()->add('error', $message);
                            $styleNumeroEntreprise = "border:1px red solid;";
                        }


                        return $this->render('ParametrageBundle:ParameterPole:traiterDossierP1.html.twig', array('idd' => $idd,
                            'dd' => $dossierDemande, 'rep' => $gerant[0], 'message' => $message, 'motif' => $motif, 'listeTypeOrigine' => $listeTypeOrigine,
                            'origine' => $origine, "formatRccm" => $debutNumRccm, "numSequentiel" => $numSequentiel,
                            'pole' => $pole, 'listeTypeFormalite' => $listeTypeFormalite, 'dateRccm' => $dateRccm,
                            'rccm' => $rccm, 'statutTraitrement' => $documentCollected->getStatutTraitement(), 'numSequentielEntreprise' => $numSequentielEntreprise,
                            'activiteAnterieure' => $activiteAnterieure, 'personneEngageurs' => $personneEngageurs, 'conjoints' => $conjoints,
                            'styleNumeroFormalite' => $styleNumeroFormalite, 'styleNumeroEntreprise' => $styleNumeroEntreprise, 'activitePrincipale' => $activitePrincipale,
                            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2
                        ));
                    }

                    $rccm->setDate($dateRccm);
                    $rccm->setDossierDemande($dossierDemande);
                    $rccm->setNumRccmEntreprise($rccmEntreprise);
                    $rccm->setNumRccmFormalite($rccmFormalite);
                    $rccm->setTypeFormaliteRccm($formalite);

                    $em->persist($rccm);
                    $em->flush();
                }

                return new RedirectResponse($this->container->get('router')->generate('traiter_pers_physique', array('idd' => $idd)));
            }
        }

        return $this->render('ParametrageBundle:ParameterPole:traiterDossierP1.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'rep' => $gerant[0], 'message' => $message, 'motif' => $motif, 'listeTypeOrigine' => $listeTypeOrigine,
            'origine' => $origine, "formatRccm" => $debutNumRccm, "numSequentiel" => $numSequentiel,
            'pole' => $pole, 'listeTypeFormalite' => $listeTypeFormalite, 'dateRccm' => $dateRccm,
            'rccm' => $rccm, 'statutTraitrement' => $documentCollected->getStatutTraitement(), 'numSequentielEntreprise' => $numSequentielEntreprise,
            'activiteAnterieure' => $activiteAnterieure, 'personneEngageurs' => $personneEngageurs, 'conjoints' => $conjoints,
            'styleNumeroFormalite' => $styleNumeroFormalite, 'styleNumeroEntreprise' => $styleNumeroEntreprise, 'activitePrincipale' => $activitePrincipale,
            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2
        ));
    }

    public function visualiserM0Action($idd)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $libelleFormeJ = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('formeJuridique' => $dossierDemande->getFormeJuridique(), 'langue' => $langue))->getLibelle();
        $listeAssocies = $em->getRepository('BanquemondialeBundle:Associe')->findByDossierDemande($dossierDemande->getId(), null, 3);
        $listeDirigeants = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $listeTypeFormalite = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->findBy(array('typeFormulaire' => null));
        $listeCommissareAuxCptes = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findByDossierDemande($dossierDemande->getId());
        $origine = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($idd);
        $listeTypeOrigine = $em->getRepository('ParametrageBundle:TypeOrigine')->findAll();

        $activites = "";
        if ($dossierDemande->getSecteurActivite()) {
            $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossierDemande->getSecteurActivite()));
            if ($activitePrincipale) {
                $activites = $activites . $activitePrincipale->getLibelle() . ";";
            }
            $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossierDemande->getActiviteSecondaire()));
            if ($activiteSecondaire) {
                $activites = $activites . $activiteSecondaire->getLibelle() . ";";
            }
            $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossierDemande->getActiviteSecondaire2()));
            if ($activiteSecondaire2) {
                $activites = $activites . $activiteSecondaire2->getLibelle();
            }

            if ($dossierDemande->getActiviteSociale()) {
                $activites = $activites . $dossierDemande->getActiviteSociale() . ";";
            }
        }
        $lesActivites = strtoupper($activites);
        $soussigne = $dossierDemande->getSoussigne();
        $profilCreateur = $dossierDemande->getUtilisateur()->getProfile();
        if ($profilCreateur && $profilCreateur->getDescription() == "saisi") {
            if ($dossierDemande->getTypeDossier()->getLibelle() == "Notaire") {
                $soussigne = $dossierDemande->getSoussigne();
            } else {
                if ($listeDirigeants) {
                    $firstRep = $listeDirigeants[0];
                    //die(dump($firstRep));
                    $civilite = "M.";
                    if ($firstRep['genre'] == "Femme") {
                        $civilite = "Mme.";
                    }
                    $soussigne = $civilite . " " . $firstRep['prenom'] . " " . $firstRep['nom'] . ", " . $firstRep['libelleFonction'];
                }
            }
        }
        $libelleSignatureGreffe = "Me Alseny Fofana Greffier en chef du TPI de Kaloum";

        $parametrageSignature = $em->getRepository('DefaultBundle:ReglageActivation')->findFirst();
        if ($parametrageSignature) {
            $libelleSignatureGreffe = $parametrageSignature->getLibelleSignatureGreffe();
        }
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserM0.html.twig', array('idd' => $idd, 'libelleFormeJ' => $libelleFormeJ,
            'activites' => $lesActivites, 'dd' => $dossierDemande, 'listeDirigeants' => $listeDirigeants, 'listeTypeOrigine' => $listeTypeOrigine,
            'listeAssocies' => $listeAssocies, 'libelleSignatureGreffe' => $libelleSignatureGreffe, 'listeComissaires' => $listeCommissareAuxCptes, 'listeTypeFormalite' => $listeTypeFormalite, 'origine' => $origine, 'dateRccm' => $dateRccm, 'rccm' => $rccm, 'soussigne' => $soussigne));
        $nomFichier = "formulaire" . $idd . "_" . $pole->getId() . ".pdf"; //cofifier après rccm
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($nomFichier);
        exit;
    }

    public function visualiserChefGreffeM0Action($idd)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $afficherSignature = 0;
        $afficherQRCodeGreffe = 0;
        $libelleSignatureGreffe = "Me Alseny Fofana Greffier en chef du TPI de Kaloum";

        $parametrageSignature = $em->getRepository('DefaultBundle:ReglageActivation')->findFirst();
        if ($parametrageSignature) {
            $afficherSignature = $parametrageSignature->getIsSignatureVisible();
            $afficherQRCodeGreffe = $parametrageSignature->getIsQRVisible();
            $libelleSignatureGreffe = $parametrageSignature->getLibelleSignatureGreffe();
        }

        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $libelleFormeJ = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('formeJuridique' => $dossierDemande->getFormeJuridique(), 'langue' => $langue))->getLibelle();
        $listeAssocies = $em->getRepository('BanquemondialeBundle:Associe')->findByDossierDemande($dossierDemande->getId(), null, 3);
        $listeDirigeants = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $listeTypeFormalite = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->findBy(array('typeFormulaire' => null));
        $listeCommissareAuxCptes = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findByDossierDemande($dossierDemande->getId());
        $origine = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($idd);
        $listeTypeOrigine = $em->getRepository('ParametrageBundle:TypeOrigine')->findAll();

        $activites = "";
        if ($dossierDemande->getSecteurActivite()) {
            $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossierDemande->getSecteurActivite()));
            if ($activitePrincipale) {
                $activites = $activites . $activitePrincipale->getLibelle() . ";";
            }
            $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossierDemande->getActiviteSecondaire()));
            if ($activiteSecondaire) {
                $activites = $activites . $activiteSecondaire->getLibelle() . ";";
            }
            $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossierDemande->getActiviteSecondaire2()));
            if ($activiteSecondaire2) {
                $activites = $activites . $activiteSecondaire2->getLibelle();
            }

            if ($dossierDemande->getActiviteSociale()) {
                $activites = $activites . $dossierDemande->getActiviteSociale() . ";";
            }
        }
        $lesActivites = strtoupper($activites);
        $soussigne = $dossierDemande->getSoussigne();
        $profilCreateur = $dossierDemande->getUtilisateur()->getProfile();
        if ($profilCreateur && $profilCreateur->getDescription() == "saisi") {
            if ($dossierDemande->getTypeDossier()->getLibelle() == "Notaire") {
                $soussigne = $dossierDemande->getSoussigne();
            } else {
                if ($listeDirigeants) {
                    $firstRep = $listeDirigeants[0];
                    //die(dump($firstRep));
                    $civilite = "M.";
                    if ($firstRep['genre'] == "Femme") {
                        $civilite = "Mme.";
                    }
                    $soussigne = $civilite . " " . $firstRep['prenom'] . " " . $firstRep['nom'] . ", " . $firstRep['libelleFonction'];
                }
            }
        }
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserChefGreffeM0.html.twig', array('idd' => $idd, 'libelleFormeJ' => $libelleFormeJ,
            'activites' => $lesActivites, 'dd' => $dossierDemande, 'listeDirigeants' => $listeDirigeants, 'listeTypeOrigine' => $listeTypeOrigine,
            'listeAssocies' => $listeAssocies, 'listeComissaires' => $listeCommissareAuxCptes, 'listeTypeFormalite' => $listeTypeFormalite,
            'origine' => $origine, 'dateRccm' => $dateRccm, 'rccm' => $rccm, 'soussigne' => $soussigne, 'afficherSignature' => $afficherSignature,
            'afficherQRCodeGreffe' => $afficherQRCodeGreffe, 'libelleSignatureGreffe' => $libelleSignatureGreffe, 'documentValide' => false));
        $nomFichier = "formulaire" . $idd . "_" . $pole->getId() . ".pdf"; //cofifier après rccm
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', array(5, 5, 5, 0));
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($nomFichier);
        exit;
    }

    public function visualiserME1Action($idd)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }

        //$chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        //$cheminDownload = $chemin->getNom();
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserME1.html.twig', array('dateRccm' => $dateRccm, 'rccm' => $rccm, 'dd' => $dossierDemande));
        //$nomFichier = "formulaire" . $idd . "_" . $pole->getId() . "_2.pdf";

        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output('document.pdf');
        exit;

        //$html2pdf->Output($cheminDownload . 'formulairesDelivres\\' . $nomFichier, 'F');
    }

    public function visualiserNIAction($idd)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $rep = $em->getRepository('BanquemondialeBundle:Representant')->findOneByDossierDemande($idd);
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $complementCnss = $em->getRepository('BanquemondialeBundle:ComplementCnss')->findOneByDossierDemande($idd);
        $leCnss = $em->getRepository('BanquemondialeBundle:Cnss')->findOneByDossierDemande($idd);
        $dateComplementCnss = new \DateTime();
        if ($complementCnss) {
            $dateComplementCnss = $complementCnss->getDateImmatriculation();
        } else {
            $complementCnss = new ComplementCnss();
            $complementCnss->setDateEffet($dateComplementCnss);
        }
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);


        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));
        $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire(), 'langue' => 1));
        $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire2(), 'langue' => 1));


        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserNI.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'cnss' => $complementCnss, 'dateComplementCnss' => $dateComplementCnss,
            'leCnss' => $leCnss, 'rccm' => $rccm, 'user' => $user, 'rep' => $rep, 'activitePrincipale' => $activitePrincipale,
            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2));
        $nomFichier = "formulaire" . $idd . "_" . $pole->getId() . ".pdf"; //cofifier après rccm
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($nomFichier);
        exit;
    }

    public function traiterDemandeNIFAction($idd)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $message = "";
        $styleNIF = "";
        $styleNumeroFormulaire = "";
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);

        $documentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
        $motif = $documentCollected->getMotif();
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $nif = $em->getRepository('BanquemondialeBundle:Nif')->findOneByDossierDemande($idd);


        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));
        $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire(), 'langue' => 1));
        $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire2(), 'langue' => 1));


        $dateNif = new \DateTime();

        //setlocale(LC_TIME, 'fr_FR.UTF8','fr.UTF8','fr_FR.UTF-8','fr.UTF-8');
        $dateValidite = new \DateTime();
        $anneeActuelle = date('Y');
        $dateFinAnnee = new \DateTime($anneeActuelle . '-12-31');


        if ($dossierDemande->getFormeJuridique()->getSigle() == 'EI') {
            $dateValidite->add(new \DateInterval('P3M'));
        } else {
            $dateValidite->add(new \DateInterval('P6M'));
        }

        if ($dateValidite > $dateFinAnnee) {
            $dateValidite = $dateFinAnnee;
        }

        if ($nif) {
            $dateNif = $nif->getDate();
        }

        $gerant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());

        if ($request->getMethod() == 'POST') {

            if ($request->request->has('textAreaModifier')) {
                $motif = $request->get('textAreaModifier');
                if (strlen($motif) <= 255) {
                    //die(dump($_POST));
                    $documentCollected->setMotif($motif);
                    $statutTraitementModifier = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(3);
                    $documentCollected->setStatutTraitement($statutTraitementModifier);
                    $em->persist($documentCollected);


                    $dossierDemande->setStatut(3);
                    $em->persist($dossierDemande);
                    $em->flush();
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $quittance = $em->getRepository('BanquemondialeBundle\Entity\Quittance')->findOneBy(['dossierDemande' => $idd]);
// die(dump($quittance));
                    $this->get('monservices')->updatePaiementOrangeWhenUpdateDossier($quittance->getId());


                    $notif = $this->container->get('utilisateurs.notification');
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $message2 = $this->get('translator')->trans('par_le_pole');
                    $nomDuPole = $this->get('translator')->trans($pole->getNom());
                    //envoi de la notification
                    $objet = $this->get('translator')->trans('demande_modification_dossier_envoye');
                    $notif->notifier($dossierDemande->getNumeroDossier() . ' ' . $message . ' ' . $message2 . ' ' . $nomDuPole, $dossierDemande->getUtilisateur(), $objet);


                    return new RedirectResponse($this->container->get('router')->generate('traiter_demande_nif', array('idd' => $idd)));
                }
            } else {
                if (!$nif) {

                    $newNif = new Nif();
                    $numeroIdentificationFiscale = $request->get('numeroIdentificationFiscale');
                    $numeroFormulaire = $request->get('numeroFormulaire');
                    $numeroFormulaireBis = $request->get('numeroFormulaireBis');
                    $secteur = $request->get('secteur');
                    $quartier = $request->get('quartier');
                    $rue = $request->get('rue');
                    $marche = $request->get('marche');
                    $boutique = $request->get('boutique');

                    $nifTest1 = $em->getRepository('BanquemondialeBundle:Nif')->verifierNumeroIdentificationFiscale($numeroIdentificationFiscale, $idd);
                    $nifTest2 = $em->getRepository('BanquemondialeBundle:Nif')->verifierNumeroFormulaire($numeroFormulaire, $idd);
                    if ($nifTest1 /* || $nifTest2 */) {
                        if ($nifTest1) {
                            $message = $this->get('translator')->trans('nif_existe');
                            $this->get('session')->getFlashBag()->add('error', $message);
                            $styleNIF = "border:1px red solid;";
                        }
                        /* if ($nifTest2) {
                          $message = $this->get('translator')->trans('numero_formulaire_existe');
                          $this->get('session')->getFlashBag()->add('error', $message);
                          $styleNumeroFormulaire = "border:1px red solid;";
                          } */
                        return $this->render('ParametrageBundle:ParameterPole:traiterDossierNIF.html.twig', array('idd' => $idd, 'dd' => $dossierDemande, 'rep' => $gerant[0], 'message' => $message, 'motif' => $motif, 'pole' => $pole, 'rccm' => $rccm, 'statutTraitrement' => $documentCollected->getStatutTraitement(), 'dateValidite' => $dateValidite, 'nif' => $newNif, 'styleNIF' => $styleNIF, 'styleNumeroFormulaire' => $styleNumeroFormulaire, 'activitePrincipale' => $activitePrincipale,
                            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2));
                    }

                    $newNif->setDate($dateNif);
                    $newNif->setDossierDemande($dossierDemande);
                    $newNif->setNumeroIdentificationFiscale($numeroIdentificationFiscale);
                    $newNif->setNumeroFormulaire($numeroFormulaire);
                    $newNif->setNumeroFormulaireBis($numeroFormulaireBis);
                    $newNif->setSecteur($secteur);
                    $newNif->setQuartier($quartier);
                    $newNif->setRue($rue);
                    $newNif->setMarche($marche);
                    $newNif->setBoutique($boutique);


                    $em->persist($newNif);
                    $em->flush();

                    $message = $this->get('translator')->trans('message.ajout_succes');
                    $this->get('session')->getFlashBag()->add('info', $message);
                } else {

                    $numeroIdentificationFiscale = $request->get('numeroIdentificationFiscale');
                    $numeroFormulaire = $request->get('numeroFormulaire');
                    $numeroFormulaireBis = $request->get('numeroFormulaireBis');
                    $secteur = $request->get('secteur');
                    $quartier = $request->get('quartier');
                    $rue = $request->get('rue');
                    $marche = $request->get('marche');
                    $boutique = $request->get('boutique');

                    $nifTest1 = $em->getRepository('BanquemondialeBundle:Nif')->verifierNumeroIdentificationFiscale($numeroIdentificationFiscale, $idd);
                    $nifTest2 = $em->getRepository('BanquemondialeBundle:Nif')->verifierNumeroFormulaire($numeroFormulaire, $idd);
                    if ($nifTest1/* || $nifTest2 */) {
                        if ($nifTest1) {
                            $message = $this->get('translator')->trans('nif_existe');
                            $this->get('session')->getFlashBag()->add('error', $message);
                            $styleNIF = "border:1px red solid;";
                        }

                        return $this->render('ParametrageBundle:ParameterPole:traiterDossierNIF.html.twig', array('idd' => $idd, 'dd' => $dossierDemande, 'rep' => $gerant[0], 'message' => $message, 'motif' => $motif, 'pole' => $pole, 'rccm' => $rccm, 'statutTraitrement' => $documentCollected->getStatutTraitement(), 'dateValidite' => $dateValidite, 'nif' => $nif, 'styleNIF' => $styleNIF, 'styleNumeroFormulaire' => $styleNumeroFormulaire, 'activitePrincipale' => $activitePrincipale,
                            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2));
                    }

                    $nif->setDate($dateNif);
                    $nif->setDossierDemande($dossierDemande);
                    $nif->setNumeroIdentificationFiscale($numeroIdentificationFiscale);
                    $nif->setNumeroFormulaire($numeroFormulaire);
                    $nif->setNumeroFormulaireBis($numeroFormulaireBis);
                    $nif->setSecteur($secteur);
                    $nif->setQuartier($quartier);
                    $nif->setRue($rue);
                    $nif->setMarche($marche);
                    $nif->setBoutique($boutique);


                    $em->persist($nif);
                    $em->flush();
                    $message = $this->get('translator')->trans('message.ajout_succes');
                    $this->get('session')->getFlashBag()->add('info', $message);
                }

                //die('test');
                return new RedirectResponse($this->container->get('router')->generate('traiter_demande_nif', array('idd' => $idd)));
            }
        }
        return $this->render('ParametrageBundle:ParameterPole:traiterDossierNIF.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'rep' => $gerant[0], 'message' => $message, 'motif' => $motif, 'pole' => $pole, 'rccm' => $rccm, 'statutTraitrement' => $documentCollected->getStatutTraitement(), 'dateValidite' => $dateValidite, 'nif' => $nif, 'styleNIF' => $styleNIF, 'styleNumeroFormulaire' => $styleNumeroFormulaire, 'activitePrincipale' => $activitePrincipale,
            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2));
    }

    public function visualiserNIFAction($idd)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);

        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));
        $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire(), 'langue' => 1));
        $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire2(), 'langue' => 1));

        $nif = $em->getRepository('BanquemondialeBundle:Nif')->findOneByDossierDemande($idd);

        $dateNif = new \DateTime();
        $dateValidite = new \DateTime();
        $anneeActuelle = date('Y');
        $dateFinAnnee = new \DateTime($anneeActuelle . '-12-31');


        if ($dossierDemande->getFormeJuridique()->getSigle() == 'EI') {
            $dateValidite->add(new \DateInterval('P3M'));
        } else {
            $dateValidite->add(new \DateInterval('P6M'));
        }

        if ($dateValidite > $dateFinAnnee) {
            $dateValidite = $dateFinAnnee;
        }

        if ($nif) {
            $dateNif = $nif->getDate();
        }


        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }

        $gerant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $documentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
        $listeAssocies = $em->getRepository('BanquemondialeBundle:Associe')->findByDossierDemande($dossierDemande->getId());
        $listeDirigeants = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $listeTypeFormalite = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->findBy(array('typeFormulaire' => null));
        $listeCommissareAuxCptes = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findByDossierDemande($dossierDemande->getId());
        $origine = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($idd);
        $listeTypeOrigine = $em->getRepository('ParametrageBundle:TypeOrigine')->findAll();
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserNIF.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'listeDirigeants' => $listeDirigeants, 'listeTypeOrigine' => $listeTypeOrigine, 'dateValidite' => $dateValidite, 'nif' => $nif,
            'rep' => $gerant[0], 'statutTraitrement' => $documentCollected->getStatutTraitement(),
            'listeAssocies' => $listeAssocies, 'listeComissaires' => $listeCommissareAuxCptes, 'listeTypeFormalite' => $listeTypeFormalite,
            'origine' => $origine, 'dateRccm' => $dateRccm, 'rccm' => $rccm, 'locale' => $codLang, 'activitePrincipale' => $activitePrincipale,
            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2));
        $nomFichier = "formulaire" . $idd . "_" . $pole->getId() . ".pdf"; //cofifier après rccm
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($nomFichier);
        exit;
    }

    function sendSMS($langue, $sms, $telephone)
    {
        $url = '';
        $retour = "";
        if (strlen($sms) > 160) {
            $sms = substr($sms, 0, 159);
        }
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Accept-language: fr\r\n"
            )
        );

        $context = stream_context_create($opts);

        if ($langue == 'fr') {
            $url = "http://192.168.1.164/sms/traitersms.php?username=kannel&password=kannel&to=" . $telephone . "&text=" . $sms . "&sms_platform=4&signature=SySCE";
        } else {
            $url = "http://192.168.1.164/sms/traitersms.php?username=kannel&password=kannel&to=" . $telephone . "&text=" . $sms . "&sms_platform=4&signature=BRTS";
        }

        try {
            $retour = file_get_contents($url, false, $context);
        } catch (\Exception $e) {
            //\Doctrine\Common\Util\Debug::dump($e);
        }
        return $retour;
    }

    function sendMail($sujet, $texte, $utilisateur)
    {
        $em = $this->getDoctrine()->getManager();

        $messagerie = $em->getRepository('ParametrageBundle:Messagerie')->find(1);
        $transport = \Swift_SmtpTransport::newInstance($messagerie->getMailerHost(), $messagerie->getMailerPort())
            ->setUsername($messagerie->getMailerUser())
            ->setPassword($messagerie->getMailerPassword())->setEncryption($messagerie->getEncryption());
        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance($transport);

        $translated = $translated = $this->get('translator')->trans("notification");

        $message->setSubject($sujet)
            ->setFrom(array($messagerie->getExpediteurEmail() => $messagerie->getExpediteurName()))
            ->setTo($utilisateur->getEmail())
            ->setBody($this->renderView('ParametrageBundle:Parametrage:email\notification.email.twig', array('texte' => $texte, 'user' => $utilisateur)), 'text/html');

        try {
            $mailer->send($message);
        } catch (\Exception $e) {
            //\Doctrine\Common\Util\Debug::dump($e);
        }
    }

    public function sendRccmAction(Request $request, $idd)
    {
        // $this->get('monservices')->UpdateSecteurActiviteNonValid();
        $encoders = array(new JsonEncoder(new JsonEncode(JSON_UNESCAPED_UNICODE), new JsonDecode(false)));
        $normalizers = new PropertyNormalizer();
        $normalizers->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $serializer = new Serializer(array($normalizers), $encoders);
        $errors = '';
        $em = $this->getDoctrine()->getManager();
        /* recherche des donnees du formulaire */
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $poleGreffe = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle('GF');
        $poleNIF = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle('BNI');
        $cheminUpload = $em->getRepository('ParametrageBundle:Chemins')->find(1)->getNom();
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(4);
        $ipServeurDNI = ($chemin) ? $chemin->getNom() : "";
        $formulaireDelivreRccm = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('dossierDemande' => $idd, 'pole' => $poleGreffe));
        $cheminFichierRccmOut = $cheminUpload . $idd . '\\' . $formulaireDelivreRccm->getNomFichier();
        $collectionPieceJointe = $em->getRepository('BanquemondialeBundle:CollectionPieceJointe')->findAllPieceJoined($idd, 1);
        //historique
        $documentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('pole' => $poleNIF, 'dossierDemande' => $idd));
        $statutEchecEnvoiDni = $em->getRepository('BanquemondialeBundle:StatutTraitement')->findOneByCode("DM"); //EE ->EM on m'a demande de la renvoyer a modifier chez le user
        $codeEchecEnvoiDni = $em->getRepository('BanquemondialeBundle:StatutTraitementTraduction')->getLibelleStatutTraitementByLangue(1, $statutEchecEnvoiDni);
        $statutEnCoursEnvoiDni = $em->getRepository('BanquemondialeBundle:StatutTraitement')->findOneByCode("EC");
        $codeEnCoursEnvoiDni = $em->getRepository('BanquemondialeBundle:StatutTraitementTraduction')->getLibelleStatutTraitementByLangue(1, $statutEnCoursEnvoiDni);
        $historiqueEnvoi = new HistoriqueEchangeDNI();
        $historiqueEnvoi->setNumeroDossier($dossierDemande->getNumeroDossier());
        $historiqueEnvoi->setDateEnvoiRccm(new \DateTime());
        $historiqueEnvoi->setContenuEnvoi("");
        //$historiqueEnvoi->setCodeRetourDNI($codeEchecEnvoiDni);
        $historiqueEnvoi->setDossierDemande($dossierDemande);
        $documentCollected->setStatutTraitement($statutEchecEnvoiDni);
        $historiqueEnvoi->setCodeRetourDNI("APIP00");
        $em->persist($documentCollected);
        $em->persist($historiqueEnvoi);
        $em->flush();
        /* try {
          set_error_handler(function($errno, $errstr, $errfile, $errline ) {
          throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
          });

          $sftp = new SFTP('10.13.15.204');

          if (!$sftp->login('apip', 'apip@P@ssw0d')) {
          //throw new \Exception('Cannot login into your server !');
          } else {
          //$sftp->chdir('../public/NIFp_apip');
          $remoteFile = 'rccm_' . $dossierDemande->getNumeroDossier() . '.pdf';
          $localFile = $cheminFichierRccmOut;
          //$sftp->put($remoteFile, $localFile, SFTP::SOURCE_LOCAL_FILE);
          /*
          //envoi des pieces jointes
          foreach($collectionPieceJointe as $pieceJointe)
          {
          $nomPieceJointe = 'rccm_'.$pieceJointe['libelleDocument'];
          if($pieceJointe['proprietaire'])
          {
          $nomPieceJointe = $nomPieceJointe.'_'.ucwords(strtolower($pieceJointe['proprietaire']));
          }
          if($pieceJointe['fonction'])
          {
          $nomPieceJointe = $nomPieceJointe.'_'.$pieceJointe['fonction'];
          }
          $remoteFile = $nomPieceJointe.'.pdf';
          $localFile = $cheminUpload . $idd->getId().'\\'.$pieceJointe['pieceName'];
          $sftp->put($remoteFile, $localFile, SFTP::SOURCE_LOCAL_FILE);
          }

          }
          } catch (\PhpErrorException $e) {
          //die("pas de connection");
          } catch (\ErrorException $e) {
          //die("erreur 1");
          } catch (\Exception $e) {
          //die("exception 1");
          }
         */
        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));
        $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire(), 'langue' => 1));
        $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire2(), 'langue' => 1));
        $activitePrincipaleLibelle = null;
        $codeActivite = "";
        $activiteSecondaire1Libelle = "";
        $codeActivite1 = "";
        $activiteSecondaire2Libelle = "";
        $codeActivite2 = "";
        if ($activitePrincipale) {
            $activitePrincipaleLibelle = $activitePrincipale->getLibelle();
            if ($dossierDemande->getSecteurActivite()) {
                $codeActivite = $dossierDemande->getSecteurActivite()->getCode();
            }
        }
        if ($activiteSecondaire) {
            $activiteSecondaire1Libelle = $activiteSecondaire->getLibelle();
            if ($dossierDemande->getActiviteSecondaire()) {
                $codeActivite1 = $dossierDemande->getActiviteSecondaire()->getCode();
            }
        }
        if ($activiteSecondaire2) {
            $activiteSecondaire2Libelle = $activiteSecondaire2->getLibelle();
            if ($dossierDemande->getActiviteSecondaire2()) {
                $codeActivite2 = $dossierDemande->getActiviteSecondaire2()->getCode();
            }
        }
        $categorieActivitePrincipale = null;
        $codeCategorie = "";
        $categorieActiviteSecondaire1 = null;
        $codeCategorie1 = "";
        $categorieActiviteSecondaire2 = null;
        $codeCategorie2 = "";
        if ($dossierDemande->getSecteurActivite()) {
            $categorieActivitePrincipale = $em->getRepository('ParametrageBundle:CategorieActiviteTraduction')->findOneBy(array('categorieActivite' => $dossierDemande->getSecteurActivite()->getCategorieActivite(), 'langue' => 1));
            if ($dossierDemande->getSecteurActivite()) {
                $codeCategorie = $dossierDemande->getSecteurActivite()->getCategorieActivite()->getCode();
            }
        }
        if ($dossierDemande->getActiviteSecondaire()) {
            $categorieActiviteSecondaire1 = $em->getRepository('ParametrageBundle:CategorieActiviteTraduction')->findOneBy(array('categorieActivite' => $dossierDemande->getActiviteSecondaire()->getCategorieActivite(), 'langue' => 1));
            if ($dossierDemande->getActiviteSecondaire()) {
                $codeCategorie1 = $dossierDemande->getActiviteSecondaire()->getCategorieActivite()->getCode();
            }
        }
        if ($dossierDemande->getActiviteSecondaire2()) {
            $categorieActiviteSecondaire2 = $em->getRepository('ParametrageBundle:CategorieActiviteTraduction')->findOneBy(array('categorieActivite' => $dossierDemande->getActiviteSecondaire2()->getCategorieActivite(), 'langue' => 1));
            if ($dossierDemande->getActiviteSecondaire2()) {
                $codeCategorie2 = $dossierDemande->getActiviteSecondaire2()->getCategorieActivite()->getCode();
            }
        }
        $categorieActivitePrincipaleLibelle = "";
        $categorieActiviteSecondaire1Libelle = "";
        $categorieActiviteSecondaire2Libelle = "";
        if ($categorieActivitePrincipale) {
            $categorieActivitePrincipaleLibelle = $categorieActivitePrincipale->getLibelle();
        }
        if ($categorieActiviteSecondaire1) {
            $categorieActiviteSecondaire1Libelle = $categorieActiviteSecondaire1->getLibelle();
        }
        if ($categorieActiviteSecondaire2) {
            $categorieActiviteSecondaire2Libelle = $categorieActiviteSecondaire2->getLibelle();
        }
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }
        $typeFormalite = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->find($rccm->getTypeFormaliteRccm());
        $adresse = $dossierDemande->getRegion()->getLibelle() . "-" . $dossierDemande->getPrefecture()->getLibelle() . "-" . $dossierDemande->getSousPrefecture()->getLibelle();
        $sigleOuEnseigne = null;

        // var_dump($this->get('monservices')->truncateWord($dossierDemande->getDenominationSociale(),50));die();
        if ($dossierDemande->getSigle()) {
            $sigleOuEnseigne = $dossierDemande->getSigle();
        } else if ($dossierDemande->getEnseigne()) {
            $sigleOuEnseigne = $this->get('monservices')->createAcronym($dossierDemande->getEnseigne(), true);
        } else {
            $sigleOuEnseigne = $this->get('monservices')->createAcronym($dossierDemande->getDenominationSociale(), true);
        }
        $nomPrecedentEploitant = null;
        $prenomPrecedentEploitant = null;
        $adressePrecedentEploitant = null;
        $rccmPrecedentEploitant = null;
        $loueurFondPrecedentEploitant = null;
        $sigleSucc = null;
        $nomCommercialSucc = null;
        $dateOuvertureSucc = null;
        $adresseSucc = null;
        $activiteSucc = null;
        $typeOrigine = null;
        $formulaire = null;

//       GROUPEMENT INTERET ECONOMIQUE
        if ($dossierDemande->getFormeJuridique()->getSigle() == "GIE") {
            $json_url = "http://nifp.mbudget.gov.gn/api/v1/constitutions/";
            $listeAssocies = $em->getRepository('BanquemondialeBundle:Associe')->findBy(array('dossierDemande' => $idd), array('id' => 'ASC'), 3, null);
            $associes = array();
            $listeAdministrateurs = $em->getRepository('BanquemondialeBundle:Administrateur')->findBy(array('dossierDemande' => $dossierDemande->getId()), array('id' => 'asc'), 3);
            $administrateurs = array();
            foreach ($listeAssocies as $associe) {
                $associeTemp = new MembreTenuFormulaire();
                $associeTemp->setNom($associe->getNom());
                $associeTemp->setPrenom($associe->getPrenom());
                if ($associe->getDateNaissance()) {
                    $associeTemp->setDateNaissance($associe->getDateNaissance()->format('Y-m-d'));
                } else if ($rccm->getDate()) {
                    $associeTemp->setDateNaissance($rccm->getDate()->format('Y-m-d'));
                }
                $associeTemp->setAdresse($associe->getAdresse());
                array_push($associes, $associeTemp);
            }
            $listeDirigeants = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), 1);
            foreach ($listeDirigeants as $administrateur) {
                $administrateurTemp = new AdministrateurFormulaire();
                $administrateurTemp->setNom($administrateur["nom"]);
                $administrateurTemp->setPrenom($administrateur["prenom"]);
                $administrateurTemp->setDateNaissance($administrateur["dateDeNaissance"]->format('Y-m-d'));
                $administrateurTemp->setLieuNaissance($administrateur["LieuNaissance"]);
                $administrateurTemp->setAdresse($administrateur["adresse"]);
                $administrateurTemp->setFonction($administrateur["libelleFonction"]);
                array_push($administrateurs, $administrateurTemp);
            }
            foreach ($listeAdministrateurs as $administrateur) {
                $administrateurTemp = new AdministrateurFormulaire();
                $administrateurTemp->setNom($administrateur->getNom());
                $administrateurTemp->setPrenom($administrateur->getPrenom());
                $administrateurTemp->setDateNaissance($administrateur->getDateNaissance()->format('Y-m-d'));
                $administrateurTemp->setLieuNaissance($administrateur->getLieuNaissance());
                $administrateurTemp->setAdresse($administrateur->getAdresse());
                $administrateurTemp->setFonction("administrateur");
                array_push($administrateurs, $administrateurTemp);
            }
            /* partie relatif groupement */
            $renseignementRelatifGroupement = new RenseignementRelatifGroupementFormulaire();
            $renseignementRelatifGroupement->setDenomination($dossierDemande->getDenominationSociale());
            $renseignementRelatifGroupement->setSigle($sigleOuEnseigne);
            $renseignementRelatifGroupement->setAdresse($adresse);
            $renseignementRelatifGroupement->setAdresseSiege($dossierDemande->getAdresseSiege());
            $renseignementRelatifGroupement->setFormeJuridique("Groupement d'Intèrêt Economique");
            $renseignementRelatifGroupement->setCapitalSocial(empty($dossierDemande->getCapitalSocial()) ? 0 : $dossierDemande->getCapitalSocial());
            $renseignementRelatifGroupement->setDuree(empty($dossierDemande->getDuree()) ? 0 : $dossierDemande->getDuree());

            /* partie relatif activites */
            $renseignementRelatifActivite = new RenseignementRelatifActiviteGroupementFormulaire();
            $categorieActiviteCodifie = new ActiviteFormulaire();
            $categorieActiviteCodifie->setCode($codeCategorie);
            $categorieActiviteCodifie->setLibelle($categorieActivitePrincipaleLibelle);
            $renseignementRelatifActivite->setGroupeActivitePrincipale($categorieActiviteCodifie);

            $activitePCodifie = new ActiviteFormulaire();
            $activitePCodifie->setCode($codeActivite);
            $activitePCodifie->setLibelle($activitePrincipaleLibelle);
            $renseignementRelatifActivite->setActivitePrincipale($activitePCodifie);

            $categorieSecondCodifie = new ActiviteFormulaire();
            $categorieSecondCodifie->setCode($codeCategorie1);
            $categorieSecondCodifie->setLibelle($categorieActiviteSecondaire1Libelle);
            $renseignementRelatifActivite->setGroupeActiviteSecondaire1($categorieSecondCodifie);

            $activiteSecondCodifie = new ActiviteFormulaire();
            $activiteSecondCodifie->setCode($codeActivite1);
            $activiteSecondCodifie->setLibelle($activiteSecondaire1Libelle);
            $renseignementRelatifActivite->setActiviteSecondaire1($activiteSecondCodifie);

            $categorieSecond2Codifie = new ActiviteFormulaire();
            $categorieSecond2Codifie->setCode($codeCategorie2);
            $categorieSecond2Codifie->setLibelle($categorieActiviteSecondaire2Libelle);
            $renseignementRelatifActivite->setGroupeActiviteSecondaire2($categorieSecond2Codifie);

            $activiteSecond2Codifie = new ActiviteFormulaire();
            $activiteSecond2Codifie->setCode($codeActivite2);
            $activiteSecond2Codifie->setLibelle($activiteSecondaire2Libelle);
            $renseignementRelatifActivite->setActiviteSecondaire2($activiteSecond2Codifie);
            $renseignementRelatifActivite->setDateDebut($dossierDemande->getDateDebut()->format('Y-m-d'));
            $renseignementRelatifActivite->setNbSalaries($dossierDemande->getNombreSalariePrevu());

            /* partie generale */
            $formulaire = new FormulaireGie();
            $formulaire->setNumeroDossier($dossierDemande->getNumeroDossier());
            $formulaire->setNomEntreprise($dossierDemande->getDenominationSociale());
            $formulaire->setSigle($sigleOuEnseigne);
            $formulaire->setTypeEntreprise("personne morale");
            $formulaire->setTypeRccm("GIE");


            $formulaire->setEmail($this->valideMail($dossierDemande->getEmail()));
            $formulaire->setTelephone(empty($dossierDemande->getTelephone()) ? "" : $dossierDemande->getTelephone());
            $communeCodif = new CommuneFormulaire();
            $codeC = ($dossierDemande->getSousPrefecture()) ? $dossierDemande->getSousPrefecture()->getCode() : "";
            $libelleC = ($dossierDemande->getSousPrefecture()) ? $dossierDemande->getSousPrefecture()->getLibelle() : "";
            $communeCodif->setCode($codeC);
            $communeCodif->setLibelle($libelleC);
            $formulaire->setCommune($communeCodif);
            $quartierCodif = new QuartierFormulaire();
            $codeQ = ($dossierDemande->getQuartierCodifie()) ? $dossierDemande->getQuartierCodifie()->getCode() : "";
            $libelleQ = ($dossierDemande->getQuartierCodifie()) ? $dossierDemande->getQuartierCodifie()->getLibelle() : "";
            $quartierCodif->setCode($codeQ);
            $quartierCodif->setLibelle($libelleQ);
            $formulaire->setQuartier($quartierCodif);
            $formulaire->setRccm($rccm->getNumRccmEntreprise());
            $formulaire->setDateRccm($rccm->getDate()->format('Y-m-d'));
            $formulaire->setTypeDemande(strtoupper($typeFormalite->getLibelle()));
            $formulaire->setRenseignementRelatifGroupement($renseignementRelatifGroupement);
            $formulaire->setRenseignementRelatifActivite($renseignementRelatifActivite);
            $formulaire->setAssocies(array_values(array_unique($associes, SORT_REGULAR)));
            $formulaire->setAdministrateurs(array_values(array_unique($administrateurs, SORT_REGULAR)));
        } //        ENTREPRISE  INDIVIDUEL
        else if ($dossierDemande->getFormeJuridique()->getSigle() == "EI") {
            $json_url = "http://nifp.mbudget.gov.gn/api/v1/formulaires/";

            $gerants = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), 1);
            $gerant = $gerants[0]; //$em->getRepository('BanquemondialeBundle:Representant')->findBy(array('dossierDemande' => $idd), array('id' => 'ASC'), 1, null)[0];

            $personneEngageurs = $em->getRepository('BanquemondialeBundle:PersonneEngageur')->getPersEngageursByDossierDemande($idd, 1);
            // die(dump($personneEngageurs));

            $listeConjoints = $em->getRepository('BanquemondialeBundle:Conjoint')->findBy(array('representant' => $gerant['id']));
            $conjoints = array();

            foreach ($listeConjoints as $conjoint) {
                $conjointTemp = new ConjointFormulaire();
                $conjointTemp->setNom($conjoint->getNom());
                $conjointTemp->setPrenom($conjoint->getPrenom());
                $conjointTemp->setDateMariage($conjoint->getDateMariage()->format('Y-m-d'));
                $conjointTemp->setLieuMariage($conjoint->getLieuMariage());
                $conjointTemp->setOptionMatrimoniale($conjoint->getOptionMatrimoniale());
                //die(dump($conjoint->getRegimeMatrimonial()));
                $conjointTemp->setRegimeMatrimonial((!empty($conjoint->getRegimeMatrimonial())) ? $conjoint->getRegimeMatrimonial()->getLibelle() : "");
                $conjointTemp->setClausesRestrictives($conjoint->getClausesRestrictives());
                $conjointTemp->setDemandeSeparationBiens($conjoint->getDemandeSeparationBiens());
                array_push($conjoints, $conjointTemp);
            }

            $origine = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($idd);
            $typeOrigineLibelle = null;
            $sigleSucc = null;
            $nomCommercialSucc = null;
            $dateOuvertureSucc = null;
            $adresseSucc = null;
            $activiteSucc = null;

            if ($origine) {
                $sigleSucc = $origine->getSigleOuEnseigne();
                $nomCommercialSucc = $origine->getNomCommercial();
                $dateOuvertureSucc = $origine->getDateOuverture();
               // die(dump($dossierDemande));
                $adresseSucc = $origine->getAdresseEtablissementSecondaire();
                $activiteSucc = $origine->getActiviteEtablissementSecondaire();
                $typeOrigine = $origine->getTypeOrigine();

                if ($typeOrigine) {
                    $typeOrigineLibelle = str_replace("é", "e", $typeOrigine->getLibelle());
                }
            } else {
                $typeOrigineLibelle = "creation";
            }


            /* partie relatif personne physique */
            $renseignementRelatifPersonnePhysique = new PersonnePhysiqueFormulaire();
            $titre = str_replace(".", "", $gerant['civilite']->getCode());
            $renseignementRelatifPersonnePhysique->setTitre($titre);
            $renseignementRelatifPersonnePhysique->setNom($gerant['nom']);
            $renseignementRelatifPersonnePhysique->setPrenom($gerant['prenom']);
            $renseignementRelatifPersonnePhysique->setDateLieuNaissance($gerant['dateDeNaissance']->format('Y-m-d') . " à " . $gerant['LieuNaissance']);
            $renseignementRelatifPersonnePhysique->setNationalite($gerant['nationalite']);
            $renseignementRelatifPersonnePhysique->setAdresse($dossierDemande->getAdresseSiege());
            $renseignementRelatifPersonnePhysique->setAdressePostale(strval($dossierDemande->getBoitePostale()));
            $renseignementRelatifPersonnePhysique->setTelephone(empty($gerant['telephone']) ? "" : $gerant['telephone']);
            // $renseignementRelatifPersonnePhysique->setTelephone($gerant['telephone']);
            $renseignementRelatifPersonnePhysique->setDomicilePersonnel(empty($gerant['adresse']) ? "" : $gerant['adresse']);
//            $renseignementRelatifPersonnePhysique->setDomicilePersonnel($gerant['adresse']);
            $renseignementRelatifPersonnePhysique->setVille($gerant['ville']);
            $renseignementRelatifPersonnePhysique->setQuartier(empty($gerant['quartier']) ? "" : $gerant['quartier']);
            $renseignementRelatifPersonnePhysique->setEmail($this->valideMail($gerant['email']));
            $renseignementRelatifPersonnePhysique->setSituationMatrimoniale(empty($gerant['situationMatrimoniale']) ? "" : $gerant['situationMatrimoniale']);
            $renseignementRelatifPersonnePhysique->setAutresPrecisions($gerant['numeroIdentiteNational'] . ' | ' . date('Y') . ' (' . $gerant['typeIdentification'] . ')');
            $renseignementRelatifPersonnePhysique->setConjoints(array_values(array_unique($conjoints, SORT_REGULAR)));
            /* partie relatif activite etablissement */
            $renseignementRelatifActiviteEtablissement = new ActiviteEtablissementEiFormulaire();
            $renseignementRelatifActiviteEtablissement->setNomCommercial($dossierDemande->getNomCommercial());
            $renseignementRelatifActiviteEtablissement->setSigle($sigleOuEnseigne);

            $categorieActiviteCodifie = new ActiviteFormulaire();
            $categorieActiviteCodifie->setCode($codeCategorie);
            $categorieActiviteCodifie->setLibelle($categorieActivitePrincipaleLibelle);
            $renseignementRelatifActiviteEtablissement->setGroupeActivitePrincipale($categorieActiviteCodifie);

            $activitePCodifie = new ActiviteFormulaire();
            $activitePCodifie->setCode($codeActivite);
            $activitePCodifie->setLibelle($activitePrincipaleLibelle);
            $renseignementRelatifActiviteEtablissement->setActiviteExercee($activitePCodifie);

            $categorieSecondCodifie = new ActiviteFormulaire();
            $categorieSecondCodifie->setCode($codeCategorie1);
            $categorieSecondCodifie->setLibelle($categorieActiviteSecondaire1Libelle);
            $renseignementRelatifActiviteEtablissement->setGroupeActiviteSecondaire1($categorieSecondCodifie);

            $activiteSecondCodifie = new ActiviteFormulaire();
            $activiteSecondCodifie->setCode($codeActivite1);
            $activiteSecondCodifie->setLibelle($activiteSecondaire1Libelle);
            $renseignementRelatifActiviteEtablissement->setActiviteSecondaire1($activiteSecondCodifie);

            $categorieSecond2Codifie = new ActiviteFormulaire();
            $categorieSecond2Codifie->setCode($codeCategorie2);
            $categorieSecond2Codifie->setLibelle($categorieActiviteSecondaire2Libelle);
            $renseignementRelatifActiviteEtablissement->setGroupeActiviteSecondaire2($categorieSecond2Codifie);

            $activiteSecond2Codifie = new ActiviteFormulaire();
            $activiteSecond2Codifie->setCode($codeActivite2);
            $activiteSecond2Codifie->setLibelle($activiteSecondaire2Libelle);
            $renseignementRelatifActiviteEtablissement->setActiviteSecondaire2($activiteSecond2Codifie);

            $renseignementRelatifActiviteEtablissement->setDateDebut($dossierDemande->getDateDebut()->format('Y-m-d'));
            $renseignementRelatifActiviteEtablissement->setRccm($rccm->getNumRccmEntreprise());
            $renseignementRelatifActiviteEtablissement->setAdressePrincipale($dossierDemande->getAdresseSiege());
            $renseignementRelatifActiviteEtablissement->setOrigine(mb_strtoupper($typeOrigineLibelle, 'UTF-8'));
            $renseignementRelatifActiviteEtablissement->setNomCommercialSucc($nomCommercialSucc);
            $renseignementRelatifActiviteEtablissement->setSigleSucc($sigleSucc);
            $renseignementRelatifActiviteEtablissement->setDateOuvertureSucc($dateOuvertureSucc);
                      //  $renseignementRelatifActiviteEtablissement->setDateOuvertureSucc($dateOuvertureSucc);

            $renseignementRelatifActiviteEtablissement->setAdresseSucc($adresseSucc);
            $renseignementRelatifActiviteEtablissement->setActiviteSucc($activiteSucc);

            /* partie relatif autre personne physique */
            $autres_personnes_physiques = array();

            foreach ($personneEngageurs as $engageur) {
                $engageurTemp = new AutrePersonnePhysiqueFormulaire();
                $engageurTemp->setNom($engageur['nom']);
                $engageurTemp->setPrenoms($engageur['prenom']);
                $engageurTemp->setDateNaissance($engageur['dateNaissance']->format('Y-m-d'));
                $engageurTemp->setLieuNaissance($engageur['lieuNaissance']);
                $engageurTemp->setNationalite($engageur['nationalite']);
                $engageurTemp->setDomicile(empty($engageur['domicile']) ? "" : $engageur['domicile']);
                // $engageurTemp->setDomicile($engageur['domicile']);
                array_push($autres_personnes_physiques, $engageurTemp);
            }


            /* partie generale */
            $formulaire = new FormulaireEi();
            $formulaire->setNumeroDossier($dossierDemande->getNumeroDossier());
            $formulaire->setNomEntreprise($dossierDemande->getDenominationSociale());
            $formulaire->setSigle($sigleOuEnseigne);
            $formulaire->setTypeEntreprise("personne physique");
            $formulaire->setTypeRccm($dossierDemande->getFormeJuridique()->getSigle());
            $formulaire->setEmail($this->valideMail($dossierDemande->getEmail()));
            $formulaire->setTelephone(empty($dossierDemande->getTelephone()) ? "" : $dossierDemande->getTelephone());
            $communeCodif = new CommuneFormulaire();
            $codeC = ($dossierDemande->getSousPrefecture()) ? $dossierDemande->getSousPrefecture()->getCode() : "";
            $libelleC = ($dossierDemande->getSousPrefecture()) ? $dossierDemande->getSousPrefecture()->getLibelle() : "";
            $communeCodif->setCode($codeC);
            $communeCodif->setLibelle($libelleC);
            $formulaire->setCommune($communeCodif);
            $quartierCodif = new QuartierFormulaire();
            $codeQ = ($dossierDemande->getQuartierCodifie()) ? $dossierDemande->getQuartierCodifie()->getCode() : "";
            $libelleQ = ($dossierDemande->getQuartierCodifie()) ? $dossierDemande->getQuartierCodifie()->getLibelle() : "";
            $quartierCodif->setCode($codeQ);
            $quartierCodif->setLibelle($libelleQ);
            $formulaire->setQuartier($quartierCodif);
            $formulaire->setRccm($rccm->getNumRccmEntreprise());
            $formulaire->setDateRccm($rccm->getDate()->format('Y-m-d'));
            $formulaire->setTypeDemande(strtoupper($typeFormalite->getLibelle()));
            $formulaire->setPersPhysique($renseignementRelatifPersonnePhysique);
            $formulaire->setActiviteEtablissement($renseignementRelatifActiviteEtablissement);
            $formulaire->setAutresPersonnes(array_values(array_unique($autres_personnes_physiques, SORT_REGULAR)));
            // die(dump($formulaire));
        } // PERSONNE MORALE
        else {

            $json_url = "http://nifp.mbudget.gov.gn/api/v1/declarations/";
            $origine = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($idd);
            $typeOrigineLibelle = null;
            if ($origine) {
                $nomPrecedentEploitant = $origine->getNomExploitant();
                $prenomPrecedentEploitant = $origine->getPrenomExploitant();
                $adressePrecedentEploitant = $origine->getAdresseExploitant();
                $rccmPrecedentEploitant = $origine->getRccmExploitant();
                $loueurFondPrecedentEploitant = $origine->getLoueurFondExploitant();
                $sigleSucc = $origine->getSigleOuEnseigne();
                $nomCommercialSucc = $origine->getNomCommercial();
                $dateOuvertureSucc = $origine->getDateOuverture();
                $adresseSucc = $origine->getAdresseEtablissementSecondaire();
                $activiteSucc = $origine->getActiviteEtablissementSecondaire();
                $typeOrigine = $origine->getTypeOrigine();
                if ($typeOrigine) {
                    $typeOrigineLibelle = str_replace("é", "e", $typeOrigine->getLibelle());
                }
            } else {
                $typeOrigineLibelle = "creation";
            }
            $listeAssocies = $em->getRepository('BanquemondialeBundle:Associe')->findBy(array('dossierDemande' => $idd), array('id' => 'ASC'), 3, null);
            $associes = array();
            $listeDirigeants = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), 1);
            $listeAdministrateurs = $em->getRepository('BanquemondialeBundle:Administrateur')->findBy(array('dossierDemande' => $dossierDemande->getId()), array('id' => 'asc'), 3);
            $administrateurs = array();

            foreach ($listeAssocies as $associe) {
                $associeTemp = new AssocieFormulaire();
                $associeTemp->setNom($associe->getNom());
                $associeTemp->setPrenom($associe->getPrenom());
                if ($associe->getDateNaissance()) {
                    $associeTemp->setDateNaissance($associe->getDateNaissance()->format('Y-m-d'));
                }
                $associeTemp->setLieuNaissance($associe->getLieuNaissance());
                $associeTemp->setAdresse($associe->getAdresse());

                array_push($associes, $associeTemp);
            }
            foreach ($listeDirigeants as $administrateur) {
                $administrateurTemp = new AdministrateurFormulaire();
                $administrateurTemp->setNom($administrateur["nom"]);
                $administrateurTemp->setPrenom($administrateur["prenom"]);
                $administrateurTemp->setDateNaissance($administrateur["dateDeNaissance"]->format('Y-m-d'));
                $administrateurTemp->setLieuNaissance($administrateur["LieuNaissance"]);
                $administrateurTemp->setAdresse($administrateur["adresse"]);
                $administrateurTemp->setFonction($administrateur["libelleFonction"]);
                array_push($administrateurs, $administrateurTemp);
            }
            foreach ($listeAdministrateurs as $administrateur) {
                $administrateurTemp = new AdministrateurFormulaire();
                $administrateurTemp->setNom($administrateur->getNom());
                $administrateurTemp->setPrenom($administrateur->getPrenom());
                $administrateurTemp->setDateNaissance($administrateur->getDateNaissance()->format('Y-m-d'));
                $administrateurTemp->setLieuNaissance($administrateur->getLieuNaissance());
                $administrateurTemp->setAdresse($administrateur->getAdresse());
                $administrateurTemp->setFonction("administrateur");
                array_push($administrateurs, $administrateurTemp);
            }
            // die(dump(($administrateurs)));
            // die(dump(array_unique($administrateurs,SORT_REGULAR)));

            $formeJuridiqueTraduction = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('formeJuridique' => $dossierDemande->getFormeJuridique(), 'langue' => 1));
            $formeJuridiqueLibelle = null;
            if ($formeJuridiqueTraduction) {
                $formeJuridiqueLibelle = $formeJuridiqueTraduction->getLibelle();
            }
            /* renseignement_relatif_personne_morale */
            $renseignementRelatifPersonneMorale = new PersonneMoraleFormulaire();
            $renseignementRelatifPersonneMorale->setDenomination($dossierDemande->getDenominationSociale());
            $renseignementRelatifPersonneMorale->setNomCommercial($dossierDemande->getNomCommercial());
            $renseignementRelatifPersonneMorale->setAdresse($adresse);
            $renseignementRelatifPersonneMorale->setAdresseSiege($dossierDemande->getAdresseSiege());
            $renseignementRelatifPersonneMorale->setAdresseEtablissement($dossierDemande->getAdresseEtablissement());
            $renseignementRelatifPersonneMorale->setFormeJuridique($formeJuridiqueLibelle);
            $renseignementRelatifPersonneMorale->setRccmSiege($dossierDemande->getRccmSiege());
            ///////////Avec l'operateur terniare on verifie si l'element son pas vide
            $renseignementRelatifPersonneMorale->setCapitalSocial(empty($dossierDemande->getCapitalSocial()) ? 0 : $dossierDemande->getCapitalSocial());
            $renseignementRelatifPersonneMorale->setDontNumeraire(empty($dossierDemande->getApportNumeraire()) ? 0 : $dossierDemande->getApportNumeraire());
            $renseignementRelatifPersonneMorale->setDontNature(empty($dossierDemande->getApportNature()) ? 0 : $dossierDemande->getApportNature());
            $renseignementRelatifPersonneMorale->setDuree(empty($dossierDemande->getDuree()) ? 0 : $dossierDemande->getDuree());

            /* renseignement_relatif_activite_et_etablisement */
            $renseignementRelatifActiviteMoraleFormulaire = new RenseignementRelatifActiviteMoraleFormulaire();
            $categorieActiviteCodifie = new ActiviteFormulaire();
            $categorieActiviteCodifie->setCode($codeCategorie);
            $categorieActiviteCodifie->setLibelle($categorieActivitePrincipaleLibelle);
            $renseignementRelatifActiviteMoraleFormulaire->setGroupeActivitePrincipale($categorieActiviteCodifie);

            $activitePCodifie = new ActiviteFormulaire();
            $activitePCodifie->setCode($codeActivite);
            $activitePCodifie->setLibelle($activitePrincipaleLibelle);
            $renseignementRelatifActiviteMoraleFormulaire->setActivitePrincipale($activitePCodifie);

            $categorieSecondCodifie = new ActiviteFormulaire();
            $categorieSecondCodifie->setCode($codeCategorie1);
            $categorieSecondCodifie->setLibelle($categorieActiviteSecondaire1Libelle);
            $renseignementRelatifActiviteMoraleFormulaire->setGroupeActiviteSecondaire1($categorieSecondCodifie);

            $activiteSecondCodifie = new ActiviteFormulaire();
            $activiteSecondCodifie->setCode($codeActivite1);
            $activiteSecondCodifie->setLibelle($activiteSecondaire1Libelle);
            $renseignementRelatifActiviteMoraleFormulaire->setActiviteSecondaire1($activiteSecondCodifie);

            $categorieSecond2Codifie = new ActiviteFormulaire();
            $categorieSecond2Codifie->setCode($codeCategorie2);
            $categorieSecond2Codifie->setLibelle($categorieActiviteSecondaire2Libelle);
            $renseignementRelatifActiviteMoraleFormulaire->setGroupeActiviteSecondaire2($categorieSecond2Codifie);

            $activiteSecond2Codifie = new ActiviteFormulaire();
            $activiteSecond2Codifie->setCode($codeActivite2);
            $activiteSecond2Codifie->setLibelle($activiteSecondaire2Libelle);
            $renseignementRelatifActiviteMoraleFormulaire->setActiviteSecondaire2($activiteSecond2Codifie);

            $renseignementRelatifActiviteMoraleFormulaire->setDateDebut($dossierDemande->getDateDebut()->format('Y-m-d'));
            $renseignementRelatifActiviteMoraleFormulaire->setNbSalaries($dossierDemande->getNombreSalariePrevu());
            $renseignementRelatifActiviteMoraleFormulaire->setEtablissementPrincipalOuSuccursale($dossierDemande->getDenominationSociale());
            $renseignementRelatifActiviteMoraleFormulaire->setOrigine(mb_strtoupper($typeOrigineLibelle, 'UTF-8'));
            $renseignementRelatifActiviteMoraleFormulaire->setAdresse($dossierDemande->getAdresseSiege());
            $renseignementRelatifActiviteMoraleFormulaire->setNomPrecedentExploitant($nomPrecedentEploitant);
            $renseignementRelatifActiviteMoraleFormulaire->setPrenomPrecedentExploitant($prenomPrecedentEploitant);
            $renseignementRelatifActiviteMoraleFormulaire->setAdressePrecedentExploitant($adressePrecedentEploitant);
            $renseignementRelatifActiviteMoraleFormulaire->setRccmPrecedentExploitant($rccmPrecedentEploitant);
            $renseignementRelatifActiviteMoraleFormulaire->setLoueurDeFond($loueurFondPrecedentEploitant);
            $renseignementRelatifActiviteMoraleFormulaire->setAdresseEtablissementSecondaire($adresseSucc);
            $renseignementRelatifActiviteMoraleFormulaire->setActiviteEtablissementSecondaire($activiteSucc);


            /* partie generale */
            $formulaire = new FormulaireSocieteMorale();
            $formulaire->setNumeroDossier($dossierDemande->getNumeroDossier());
            $formulaire->setNomEntreprise($dossierDemande->getDenominationSociale());
            $formulaire->setSigle($sigleOuEnseigne);
            $formulaire->setTypeEntreprise("personne morale");
            $formulaire->setTypeRccm($dossierDemande->getFormeJuridique()->getSigle());
            $formulaire->setEmail($this->valideMail($dossierDemande->getEmail()));
            $formulaire->setTelephone(empty($dossierDemande->getTelephone()) ? "" : $dossierDemande->getTelephone());
            $communeCodif = new CommuneFormulaire();
            $codeC = ($dossierDemande->getSousPrefecture()) ? $dossierDemande->getSousPrefecture()->getCode() : "";
            $libelleC = ($dossierDemande->getSousPrefecture()) ? $dossierDemande->getSousPrefecture()->getLibelle() : "";
            $communeCodif->setCode($codeC);
            $communeCodif->setLibelle($libelleC);
            $formulaire->setCommune($communeCodif);
            $quartierCodif = new QuartierFormulaire();
            $codeQ = ($dossierDemande->getQuartierCodifie()) ? $dossierDemande->getQuartierCodifie()->getCode() : "";
            $libelleQ = ($dossierDemande->getQuartierCodifie()) ? $dossierDemande->getQuartierCodifie()->getLibelle() : "";
            $quartierCodif->setCode($codeQ);
            $quartierCodif->setLibelle($libelleQ);
            $formulaire->setQuartier($quartierCodif);
            $formulaire->setRccm($rccm->getNumRccmEntreprise());
            $formulaire->setDateRccm($rccm->getDate()->format('Y-m-d'));
            $formulaire->setTypeDemande(strtoupper($typeFormalite->getLibelle()));
            $formulaire->setRenseignementRelatifPersonneMorale($renseignementRelatifPersonneMorale);
            $formulaire->setRenseignementRelatifActiviteEtablissement($renseignementRelatifActiviteMoraleFormulaire);
            //die(dump($associes));
            $formulaire->setAssocies($associes);
            // die(dump(array_values(array_unique($associes,SORT_REGULAR))));
            $formulaire->setAssocies(array_values(array_unique($associes, SORT_REGULAR)));
            $formulaire->setAdministrateurs(array_values(array_unique($administrateurs, SORT_REGULAR)));
        }

        $validator = $this->get('validator');
        $violations = $validator->validate($formulaire);

        if (count($violations) !== 0) {
            foreach ($violations as $violation) {

                echo $violation->getMessage() . '<br>';
                $errors = $errors . $violation . ' <br>';

            }
            $documentCollected->setStatutTraitement($statutEchecEnvoiDni);
            $jsonFormulaire7 = $serializer->serialize($formulaire, 'json');
            $historiqueEnvoi->setContenuEnvoi($errors);
            $historiqueEnvoi->setContenuEnvoi($jsonFormulaire7);
            $historiqueEnvoi->setCodeRetourDNI('008');
            $historiqueEnvoi->setContenuDataRecu($errors);
            $em->persist($historiqueEnvoi);

            $em->flush();
            //exit;
            return new JsonResponse(array('resultat' => '0'));
        }
        else {

            try {

                $username = "dnsi";  // authentication
                $password = "dnsi";  // authentication
                $auth = '{"username":"dnsi","password":"dnsi"}';
                //generation du token                
                $json_url0 = "http://" . $ipServeurDNI . "/login";
                // die(dump($json_url0));
                $em->flush();
                $ch0 = curl_init($json_url0);

                // Configuring curl options
                $options0 = array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => array("Content-type: application/json"),
                    CURLOPT_POSTFIELDS => $auth,
                    CURLOPT_CONNECTTIMEOUT => 120,
                    CURLOPT_TIMEOUT => 120
                );

                // Setting curl options
                curl_setopt_array($ch0, $options0);
                // Getting results
                $resultTo = curl_exec($ch0); // Getting jSON result string

                $dataReponseToken = json_decode($resultTo, true);

                $token = $dataReponseToken["token"];
                //  die(dump($token));
                curl_close($ch0);
                //fin genera
                $formulaire->setToken($token);

                $jsonFormulaire = $serializer->serialize($formulaire, 'json');
              //  die(dump($jsonFormulaire));
                $jsonFormulaire0 = str_replace("\/", "/", $jsonFormulaire);
                $jsonFormulaire1 = preg_replace('/\bnull\b/u', '""', $jsonFormulaire0);
                //die(dump($jsonFormulaire0));
                $historiqueEnvoi->setContenuEnvoi($jsonFormulaire1);
                $em->persist($historiqueEnvoi);
                $em->flush();
                //Transformer en csv
                //ecrire dans un fichier texte
                $nomF = $dossierDemande->getNumeroDossier();
                $nomFComplet = 'data_' . $nomF . ".json";
                $remote2 = "D:\data_" . $nomF . ".json";
                /* $fichierTest = fopen("D:\data_" . $nomF . ".json", "w+");
                  fwrite($fichierTest, $jsonFormulaire1);
                  fclose($fichierTest); */
                //$sftp->put("D:\declaration.txt", SFTP::SOURCE_LOCAL_FILE);
                /* $sftp2 = new SFTP('10.13.15.204');

                  if (!$sftp2->login('apip', 'apip@P@ssw0d')) {
                  //throw new \Exception('Cannot login into your server !');
                  } else {
                  $sftp2->chdir('../public/IN');
                  //$sftp2->put($nomFComplet,$remote2, SFTP::SOURCE_LOCAL_FILE);
                  } */
                //fin texte
                //$this->jsonToCsv($jsonFormulaire, "D:/declaration.csv");
                //fin transformation
                //juste pour test a enlever
                $json_url = "http://" . $ipServeurDNI . "/apip/demande";
                // fama : cette partie sera commenté provisoirement avant que le NIF soit pret
                // Initializing curl
                $ch = curl_init($json_url);
                $formulaire->setToken($token);

                $jsonFormulaire5 = $serializer->serialize($formulaire, 'json');
                // Configuring curl options
                $options = array(
                    CURLOPT_RETURNTRANSFER => true,
                    //CURLOPT_USERPWD => $username . ":" . $password,  // authentication
                    CURLOPT_HTTPHEADER => array("Content-type: application/json"),
                    CURLOPT_POSTFIELDS => $jsonFormulaire5,
                    CURLOPT_CONNECTTIMEOUT => 120,
                    CURLOPT_TIMEOUT => 120
                );
                // Setting curl options
                curl_setopt_array($ch, $options);
                // Getting results
                $result = curl_exec($ch); // Getting jSON result string
                $historiqueEnvoi->setContenuDataRecu($result);

                $em->persist($historiqueEnvoi);
                if (curl_errno($ch)) {
                    print curl_error($ch);
                    $dataReponse = curl_errno($ch);
                    $historiqueEnvoi->setContenuDataRecu($result);
                    $em->persist($historiqueEnvoi);
                }
                else {
                    $dataReponse = json_decode($result, true);
                }
                curl_close($ch);
                try {
                    //traitement du code de retour
                    $motif = "";
                    if ($dataReponse["code"]) {
                        if ($dataReponse["code"] == "DNI01") {
                            $documentCollected->setStatutTraitement($statutEnCoursEnvoiDni);
                            $documentCollected->setDateSoumission(new \DateTime());
                            //$documentCollected->
//                                                           	
                        }
                        else {

                            $messages = $dataReponse["messages"] ? $dataReponse["messages"] : array();
                            $motif = $messages[0]; //implode("_", $messages);
                            $documentCollected->setStatutTraitement($statutEchecEnvoiDni);
                            $documentCollected->setMotif($motif);
                            //$em->flush();
                        }
                    }
                    else {
                        $dataReponse["code"] = "APIP00";
                        $messages = $dataReponse["messages"] ? $dataReponse["messages"] : array();
                        $motif = $messages[0]; //implode("_", $messages);
                        $documentCollected->setStatutTraitement($statutEchecEnvoiDni);
                        $documentCollected->setMotif($motif);
                        // $em->flush();
                    }
//                  					switch ($dataReponse["code"]) {
//                                                            case "DNI01":
//                  							//Donnée enregistrer avec succès
//                  							$documentCollected->setStatutTraitement($statutEnCoursEnvoiDni);
//                  							//$documentCollected->setDateSoumission(new \DateTime());
//                  							break;
////                  						
//                  						case "APIP02":
//                  							//Format de données envoyé incorect (non valide)
//                  							$documentCollected->setStatutTraitement($statutEchecEnvoiDni);
//                  							break;  						
//                  						default:
//                  							$dataReponse["code"] = "APIP00";
//                  							$documentCollected->setStatutTraitement($statutEchecEnvoiDni);
//                  					}

                    $historiqueEnvoi->setCodeRetourDNI($dataReponse["code"]);
                    $em->persist($documentCollected);
                    $em->persist($historiqueEnvoi);
                    $em->flush();
                    //die(dump($historiqueEnvoi));
                }
                catch (\Exception $ex0) {
                    $historiqueEnvoi->setCodeRetourDNI("APIP10");
                    $em->persist($historiqueEnvoi);
                    $em->flush();
                    return new JsonResponse(array('resultat' => '0'));
                }
            }
            catch (\Exception $ex) {
               // die(dump($ex));
                $historiqueEnvoi->setCodeRetourDNI("APIP00");
                $em->persist($historiqueEnvoi);
                // die(dump($historiqueEnvoi));
                $em->flush();
                return new JsonResponse(array('resultat' => '0'));
            }
        }
        return new JsonResponse(array('resultat' => '1'));
    }

    function jsonToCsv($json, $csvFilePath = false, $boolOutputFile = false)
    {

        // See if the string contains something
        if (empty($json)) {
            die("The JSON string is empty!");
        }

        // If passed a string, turn it into an array
        if (is_array($json) === false) {
            $json = json_decode($json, true);
            //$json = $json['response'];
        }

        // If a path is included, open that file for handling. Otherwise, use a temp file (for echoing CSV string)
        if ($csvFilePath !== false) {
            $f = fopen($csvFilePath, 'w+');
            if ($f === false) {
                die("Couldn't create the file to store the CSV, or the path is invalid. Make sure you're including the full path, INCLUDING the name of the output file (e.g. '../save/path/csvOutput.csv')");
            }
        } else {
            $boolEchoCsv = true;
            if ($boolOutputFile === true) {
                $boolEchoCsv = false;
            }
            $strTempFile = 'csvOutput' . date("U") . ".csv";
            $f = fopen($strTempFile, "w+");
        }

        $firstLineKeys = false;
        foreach ($json as $line) {
            if (empty($firstLineKeys)) {
                $firstLineKeys = array_keys($line);
                fputcsv($f, $firstLineKeys);
                $firstLineKeys = array_flip($firstLineKeys);
            }

            // Using array_merge is important to maintain the order of keys acording to the first element
            fputcsv($f, array_merge($firstLineKeys, $line));
        }
        fclose($f);

        // Take the file and put it to a string/file for output (if no save path was included in function arguments)
        if ($boolOutputFile === true) {
            if ($csvFilePath !== false) {
                $file = $csvFilePath;
            } else {
                $file = $strTempFile;
            }

            // Output the file to the browser (for open/save)
            if (file_exists($file)) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename=' . basename($file));
                header('Content-Length: ' . filesize($file));
                readfile($file);
            }
        } elseif ($boolEchoCsv === true) {
            if (($handle = fopen($strTempFile, "r")) !== FALSE) {
                while (($data = fgetcsv($handle)) !== FALSE) {
                    echo implode(",", $data);
                    echo "<br />";
                }
                fclose($handle);
            }
        }

        // Delete the temp file
        unlink($strTempFile);
    }

    /**
     * @param $mail
     * @return string
     */
    function valideMail($mail)
    {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return 'abcdef@gmail.com';
        } else {
            return $mail;
        }
    }
}
