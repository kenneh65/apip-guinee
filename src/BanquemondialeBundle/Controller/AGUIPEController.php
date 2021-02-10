<?php

namespace BanquemondialeBundle\Controller;

use BanquemondialeBundle\Entity\Aguipe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \BanquemondialeBundle\Entity\Employe;
use BanquemondialeBundle\Form\EmployeType;
use BanquemondialeBundle\Form\AguipeType;
use BanquemondialeBundle\Entity\FormulaireDelivre;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\JsonResponse;

class AGUIPEController extends Controller {

    public function createAction($idd) {


        $user = $this->container->get('security.context')->getToken()->getUser();
        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $message = "";
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $em = $this->getDoctrine()->getManager();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $listeEmployes = $em->getRepository('BanquemondialeBundle:Employe')->findByDossierDemande($idd);

        $ficheEntreprise = $em->getRepository('BanquemondialeBundle:Aguipe')->findOneByDossierDemande($idd);
        if (!$ficheEntreprise) {
            $ficheEntreprise = new Aguipe();
        }
        //$codLang = $request->getLocale();
        // $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $isAguipe = true;
        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findBySigle("AGUIPE");
        $docAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
        if ($docAguipe) {
            $isAguipe = true;
        } else {
            $isAguipe = false;
        }
        $formeJuridiqueId = $dossierDemande->getFormeJuridique()->getId();
        $nbreEmpPrevu = $dossierDemande->getNombreSalariePrevu();
        $ficheEntreprise->setNombreEmployeActuel($nbreEmpPrevu);
        $ficheEntreprise->setDossierDemande($dossierDemande);
        $ficheEntreprise->setDateDebutActivite($dossierDemande->getDateDebut());
        $ficheEntreprise->setEnActivite($dossierDemande->getEnActivite());
        $form = $this->createForm(new AguipeType(), $ficheEntreprise);
        $rteSuivant = $this->getNextEtapeRoute($formeJuridiqueId, $isAguipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($ficheEntreprise->getNombreEmployeActuel() == $ficheEntreprise->getNombreEmployeGuineen() + $ficheEntreprise->getNombreEmployeEtranger()) {
                $em->persist($ficheEntreprise);
                $em->flush();
            } else {
                $message = $this->get('translator')->trans("message_effectif_guineen_etranger_non_coherent");
            }

            // return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idd)));
        }//,'ficheEntreprise'=>$ficheEntreprise

        $newEmp = new Employe();
        $formEmp = $this->createForm(new EmployeType(array('langue' => $langue, 'definedSexeTraduit' => null, 'definedPaysTraduit' => null)), $newEmp);
        return $this->render('BanquemondialeBundle:AGUIPE:create.html.twig', array(
                    'idd' => $idd, 'form' => $form->createView(), 'formEmp' => $formEmp->createView(), 'listeEmployes' => $listeEmployes, 'rteSuivant' => $rteSuivant, 'message' => $message
        ));
    }

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

    public function editEmployeAction($idd, $idE) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        //$request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        //$listeEmployes = $em->getRepository('BanquemondialeBundle:Employe')->findByDossierDemande($idd);

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        //$form = $this->createForm(new AguipeType(), $ficheEntreprise);
        $newEmp = $em->getRepository('BanquemondialeBundle:Employe')->find($idE);
        $nationaliteTraduit = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $newEmp->getNationalite(), 'langue' => $langue));
        $sexeTraduit = $em->getRepository('BanquemondialeBundle:GenreTraduction')->findOneBy(array('genre' => $newEmp->getSexe(), 'langue' => $langue));
        $formEmp = $this->createForm(new EmployeType(array('langue' => $langue, 'definedSexeTraduit' => $sexeTraduit, 'definedPaysTraduit' => $nationaliteTraduit)), $newEmp);


        if ($request->getMethod() == 'POST') {
            $formEmp->bind($request);
            if ($formEmp->isValid()) {
                //die($newEmp);
                $em->persist($newEmp);
                $em->flush();
                return new RedirectResponse($this->container->get('router')->generate('createfiche_entreprise', array('idd' => $dossierDemande->getId())));
            }
        }
        //return new RedirectResponse($this->container->get('router')->generate('createfiche_entreprise', array('idd' => $dossierDemande->getId())));

        return $this->render('BanquemondialeBundle:AGUIPE:edit.html.twig', array(
                    'idd' => $idd, 'idE' => $idE, 'formEmp' => $formEmp->createView()
        ));
    }

    public function detailsEmployeAction($idd, $idE) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $emp = $em->getRepository('BanquemondialeBundle:Employe')->getDetailsEmploye($idE, $langue->getId());
        //die(dump($emp));
        return $this->render('BanquemondialeBundle:AGUIPE:details.html.twig', array(
                    'idd' => $idd, 'idE' => $idE, 'employe' => $emp[0]
        ));
    }

    public function ajoutEmployeAction(Request $request, $idd) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        //$request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $listeEmployes = $em->getRepository('BanquemondialeBundle:Employe')->findByDossierDemande($idd);
        $ficheEntreprise = $em->getRepository('BanquemondialeBundle:Aguipe')->findOneByDossierDemande($idd);
        if (!$ficheEntreprise) {
            $ficheEntreprise = new Aguipe();
        }
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        //$form = $this->createForm(new AguipeType(), $ficheEntreprise);
        $newEmp = new Employe();
        $newEmp->setDossierDemande($dossierDemande);
        $formEmp = $this->createForm(new EmployeType(array('langue' => $langue, 'definedSexeTraduit' => null, 'definedPaysTraduit' => null)), $newEmp);

        if ($request->getMethod() == 'POST') {
            $formEmp->bind($request);
            if ($formEmp->isValid()) {
                //die($newEmp);
                $em->persist($newEmp);
                $em->flush();
                return new RedirectResponse($this->container->get('router')->generate('createfiche_entreprise', array('idd' => $dossierDemande->getId())));
            }
        }
        return new RedirectResponse($this->container->get('router')->generate('createfiche_entreprise', array('idd' => $dossierDemande->getId())));
    }

    public function traiterDossierAguipeAction($idd) {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $em = $this->getDoctrine()->getManager();
        $lesFormulairesDelives = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->findBy(array('pole' => $pole->getId()));

        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $message = "";

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $documentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
        $motif = $documentCollected->getMotif();
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $aguipe = $em->getRepository('BanquemondialeBundle:Aguipe')->findOneByDossierDemande($idd);
        $cnss=$em->getRepository('BanquemondialeBundle:ComplementCnss')->findOneBy(array('dossierDemande'=>$idd));
        $dateImmatriculation = new \DateTime();
        if ($documentCollected && ($documentCollected->getStatutTraitement() && $documentCollected->getDateDelivrance())) {
            $dateImmatriculation = $documentCollected->getStatutTraitement()->getId() == 2 ? $documentCollected->getDateDelivrance() : $dateImmatriculation;
        }
        $gerant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $listeEmployes = $em->getRepository('BanquemondialeBundle:Employe')->getListeDesEmployes($idd, $langue->getId());
        $listeTypeStructures = $em->getRepository('BanquemondialeBundle:TypeStructure')->findAll();

        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));

        if ($request->getMethod() == 'POST') {

            if ($request->request->has('textAreaModifier')) {
                $motif = $request->get('textAreaModifier');
                if (strlen($motif) <= 255) {
                    $documentCollected->setMotif($motif);
                    $statutTraitementModifier = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(3);
                    $documentCollected->setStatutTraitement($statutTraitementModifier);
                    $documentCollected->setDateDerniereModification(new \DateTime());
                    $em->persist($documentCollected);

                    $dossierDemande->setStatut(3);
                    $em->persist($dossierDemande);
                    $em->flush();
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $quittance=$em->getRepository('BanquemondialeBundle\Entity\Quittance')->findOneBy(['dossierDemande'=>$idd]);
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
                if ($aguipe) {

                    $idTypeS = $request->get('typeStructure');
                    $numeroImmatrication = $request->get('numeroAguipe');
                    $numeroCNSS = $request->get('numeroCNSS');
                    $signataire = $request->get('nomSignataire');
                    $fonctionSignataire = $request->get('fonctionSignataire');
                    $nombreEtablissement = $request->get('nombreEtablissement');
                    $typeStructure = $em->getRepository("BanquemondialeBundle:TypeStructure")->find($idTypeS);                    
                    /*if (!$numeroImmatrication || $numeroImmatrication=="") {
                        die(dump($numeroImmatrication));
                        $translated = $this->get('translator')->trans('numero_aguipe_requis');
                        $this->get('session')->getFlashBag()->add('error', $translated);
                        return $this->redirectToRoute('aguipe_traiterDossier',array('idd'=>$idd));
                    }
                    die(dump($numeroImmatrication."jjjjj"));*/
                    $aguipe->setNumeroImmatriculation($numeroImmatrication);
                    $aguipe->setTypeStructure($typeStructure);
                    $aguipe->setNumeroCNSS($numeroCNSS);
                    $aguipe->setDateImmatriculation($dateImmatriculation);
                    $aguipe->setNombreEtablissement($nombreEtablissement);
                    $aguipe->setNomSignataire($signataire);
                    $aguipe->setFonctionSignataire($fonctionSignataire);
                    $em->persist($aguipe);
                    $em->flush();
                } else {
                    $message = $this->get('translator')->trans('dossier_non_aguipe');
                    $this->get('session')->getFlashBag()->add('error', $message);
                }
            }
        }
        return $this->render('BanquemondialeBundle:AGUIPE:traiterDossierAguipe.html.twig', array('idd' => $idd,
                    'dd' => $dossierDemande, 'rep' => $gerant[0], 'message' => $message, 'motif' => $motif, 'dateImmatriculation' => $dateImmatriculation,
                    'pole' => $pole, 'aguipe' => $aguipe,'cnss'=>$cnss, 'listeEmployes' => $listeEmployes, 'lesTypeStructures' => $listeTypeStructures,
                    'rccm' => $rccm, 'statutTraitrement' => $documentCollected->getStatutTraitement(), 'lesFormulaireDuPole' => $lesFormulairesDelives,
                    'activitePrincipale' => $activitePrincipale
        ));
    }

    public function visualiserCertificatRegSocialeAction($idd) {
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
        $aguipe = $em->getRepository('BanquemondialeBundle:Aguipe')->findOneByDossierDemande($idd);
        $gerant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $dateImmatricution = new \DateTime();
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserCertRegularite.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'rccm' => $rccm, 'dateImmatriculation' => $dateImmatricution,
            'aguipe' => $aguipe, 'gerant' => $gerant));
        $nomFichier = "formulaire" . $idd . "_" . $pole->getId() . ".pdf";
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->addFont('Times', '');
        //$html2pdf->set('Times', '');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($nomFichier);
        exit;
    }

    public function visualiserAttestationDeDeclarationAction($idd) {
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
        $aguipe = $em->getRepository('BanquemondialeBundle:Aguipe')->findOneByDossierDemande($idd);
        $gerant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $dateImmatricution = new \DateTime();
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserAttestation.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'rccm' => $rccm, 'dateImmatriculation' => $dateImmatricution,
            'aguipe' => $aguipe, 'gerant' => $gerant));
        $nomFichier = "formulaire" . $idd . "_" . $pole->getId() . ".pdf"; //cofifier après rccm
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->addFont('Times', '');
        //$html2pdf->set('Times', '');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($nomFichier);
        exit;
    }

    public function visualiserFicheEntrepriseAction($idd) {
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

        $dateEffet = new \DateTime();

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $documentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $aguipe = $em->getRepository('BanquemondialeBundle:Aguipe')->findOneByDossierDemande($idd);
        $dateImmatriculation = new \DateTime();
        $gerant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $listeEmployes = $em->getRepository('BanquemondialeBundle:Employe')->getListeDesEmployes($idd, $langue->getId());
        $listeTypeStructures = $em->getRepository('BanquemondialeBundle:TypeStructure')->findAll();

        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));

        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserFicheEntreprise.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'rep' => $gerant[0], 'dateImmatriculation' => $dateImmatriculation,
            'aguipe' => $aguipe, 'listeEmployes' => $listeEmployes, 'lesTypeStructures' => $listeTypeStructures,
            'rccm' => $rccm, 'dateEffet' => $dateEffet, 'activitePrincipale' => $activitePrincipale
        ));
        $nomFichier = "formulaire" . $idd . "_" . $pole->getId() . ".pdf"; //cofifier après rccm
        $html2pdf = new \Html2Pdf_Html2Pdf('L', 'A4', 'fr');
        $html2pdf->addFont('Times', '');
        //$html2pdf->set('Times', '');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($nomFichier);
        exit;
    }

    public function enregistrerPDF($idd, $pole) {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }
        $dateEffet = new \DateTime();
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $aguipe = $em->getRepository('BanquemondialeBundle:Aguipe')->findOneByDossierDemande($idd);
        $gerant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $listeEmployes = $em->getRepository('BanquemondialeBundle:Employe')->getListeDesEmployes($idd, $langue->getId());
        $listeTypeStructures = $em->getRepository('BanquemondialeBundle:TypeStructure')->findAll();
        $dateImmatricution = new \DateTime();
        $dateIm = $aguipe->getDateImmatriculation();
        if ($dateIm) {
            $dateImmatricution = $dateIm;
        }

        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));

        $htmlAttestion = $this->renderView('ParametrageBundle:ParameterPole:visualiserAttestation.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'rccm' => $rccm, 'dateImmatriculation' => $dateImmatricution,
            'aguipe' => $aguipe, 'gerant' => $gerant));
        $htmlCertfRegSociale = $this->renderView('ParametrageBundle:ParameterPole:visualiserCertRegularite.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'rccm' => $rccm, 'dateImmatriculation' => $dateImmatricution,
            'aguipe' => $aguipe, 'gerant' => $gerant));
        $htmlFicheEntreprise = $this->renderView('ParametrageBundle:ParameterPole:visualiserFicheEntreprise.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'rep' => $gerant[0], 'dateImmatriculation' => $dateImmatricution,
            'aguipe' => $aguipe, 'listeEmployes' => $listeEmployes, 'lesTypeStructures' => $listeTypeStructures,
            'rccm' => $rccm, 'dateEffet' => $dateEffet, 'activitePrincipale' => $activitePrincipale
        ));
        $lesFormulairesDelives = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->findBy(array('pole' => $pole->getId()));

        //die(dump($lesFormulairesDelives));
        foreach ($lesFormulairesDelives as $formulaireDel) {
            $nomFichier = "formulaire" . $idd . "_" . $formulaireDel->getId() . ".pdf"; //cofifier après rccm
            $formulaireName = $formulaireDel->getLibelle();
            $formulaireName = $formulaireName ? strtolower($formulaireName) : " ";
            if ($formulaireName) {
                if (strpos($formulaireName, 'fiche') !== false) {
                    $html2pdf = new \Html2Pdf_Html2Pdf('L', 'A4', 'fr');
                } else {
                    $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
                }
            }

            $html2pdf->addFont('Times', '');
            //$html2pdf->set('Times', '');
            $html2pdf->pdf->SetDisplayMode('real');

            if ($formulaireName) {
                if (strpos($formulaireName, 'cert') !== false) {
                    $html2pdf->writeHTML($htmlCertfRegSociale);
                } else if (strpos($formulaireName, 'fiche') !== false) {
                    $html2pdf->writeHTML($htmlFicheEntreprise);
                } else {
                    $html2pdf->writeHTML($htmlAttestion);
                }
            }

            $html2pdf->Output($cheminDownload . $idd . '\\' . $nomFichier, 'F');
            //$html2pdf->
            $formDelivre = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('nomFichier' => $nomFichier, 'pole' => $pole, 'dossierDemande' => $dossierDemande));
            if (!$formDelivre) {
                $date = new \DateTime();
                $formulaireDelivre = new FormulaireDelivre();
                $formulaireDelivre->setPole($pole);
                $formulaireDelivre->setDossierDemande($dossierDemande);
                $formulaireDelivre->setDateCreation($date);
                $formulaireDelivre->setNomFichier($nomFichier);
                $formulaireDelivre->setLibelleFormulaireDelivre($formulaireDel);
                $em->persist($formulaireDelivre);
            } else {
                $date = new \DateTime();
                $formDelivre->setDateCreation($date);
                $em->persist($formDelivre);
            }
            $em->flush();
        }
    }

    public function delivrerDossierAction(Request $request) {
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
        $sms = "";
        $dateActu = new \DateTime();

        //$pole = $em->getRepository('ParametrageBundle:Pole')->find($idPole);
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);

        if ($request->getMethod() == 'POST') {

            $documentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
            if ($documentCollected) {
                //Mise a jour du pole en cours
                $statutTraitement = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(2);

                $documentCollected->setStatutTraitement($statutTraitement);
                $documentCollected->setMotif(null);
                $documentCollected->setDateDelivrance($dateActu);
                $em->persist($documentCollected);
                $em->flush();
                //enregistrer PDF
                $this->enregistrerPDF($idd, $pole);
                // return new JsonResponse(array('resultat' => '1', 'url' => $sms));
                //fin
                //Mise a jour Pole suivant
                $ordre = $documentCollected->getOrdre();
                $statutEncours = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(1);
                //si on a pas d'autres documentcollected du meme ordre
                //$documentsCollectedMemeOrdre = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('ordre' => $ordre, 'dossierDemande' => $dossierDemande, 'statutTraitement' => $statutEncours));
                $documentsCollectedMemeOrdre = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDocumentMemeOrdre($ordre, $dossierDemande, $statutEncours);

                //on verifie si c'etait le dernier pole ou pas
                $documentSuivant = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findPoleSuivant($ordre, $idd);
                //si on a d'autres poles du meme ordre on envoi juste les notifications
                $notif = $this->container->get('utilisateurs.notification');
				
				//return new JsonResponse(array('resultat' => '1', 'url' => $sms));
				
				
                if ($documentsCollectedMemeOrdre) {
                    $message = $this->get('translator')->trans('message_dossier_en_cours');
                    $objet = $this->get('translator')->trans('traitement_dossier_en_cours');
                }
                else {
					//on cherche la liste des documentcollected du prochain ordre
                    //$documentSuivant = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findPoleSuivant($ordre, $idd);
                    $indicateur = $em->getRepository('ParametrageBundle:Chemins')->find(2);
                    $telephone = '' . $indicateur->getNom() . '' . $dossierDemande->getUtilisateur()->getTelephone();

                    if ($documentSuivant) {
                        $statutEncours = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(1);
                        $polesSuivants = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findPolesSuivants($documentSuivant[0]->getOrdre(), $idd);
                        if ($polesSuivants) {
                            $message = $this->get('translator')->trans('message_dossier_recu');
                            $objet = $this->get('translator')->trans('reception_dossier_objet');
                            foreach ($polesSuivants as $poleSuivant) {
                                $poleSuivant->setStatutTraitement($statutEncours);
                                $dateEnCours = $poleSuivant->getDateSoumission() ? $poleSuivant->getDateSoumission() : new \DateTime();
                                $poleSuivant->setDateSoumission($dateEnCours);
                                $em->persist($poleSuivant);
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
                    }
                }
                //Envoie d'une notification
                $em->flush();
				
				
				//envoi email promoteur
				if($dossierDemande->getEmailPromoteur())
				{
					$sujet = $this->get('translator')->trans('sujet_notification_email');
					$texte = $this->get('translator')->trans('envoi_email_soumission_aguipe',array('%denominationSociale%' => $dossierDemande->getDenominationSociale()));
					$ancienEmailUtilisateur = $dossierDemande->getUtilisateur()->getEmail();
					$dossierDemande->getUtilisateur()->setEmail($dossierDemande->getEmailPromoteur());
					try {
					  $this->sendMail($sujet, $texte, $dossierDemande->getUtilisateur());
					} catch (\Exception $e) {
						$translated =  $this->get('translator')->trans("error_send_mail");
						$this->get('session')->getFlashBag()->add('error', $translated);
						$this->get('logger')->error($e->getMessage());
					}
					finally
					{
						$dossierDemande->getUtilisateur()->setEmail($ancienEmailUtilisateur);
					}
				}
				
                return new JsonResponse(array('resultat' => '1', 'url' => $sms));
            }
        }

        return new JsonResponse(array('resultat' => '0'));
    }

    public function listerDocAccompagnantAction($idd, $idS = null) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $idS = $request->get('idS');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang)->getId();
        $listPieceEntreprise = $em->getRepository('BanquemondialeBundle:CollectionPieceJointe')->findAllPieceJoined($idd, $langue);
        $listDocDelivres = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->getListFormulaireDelivre($idd, $langue);
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminUpload = $chemin->getNom(); //a modifier par la requete vers la bdd        

        return $this->render('BanquemondialeBundle:AGUIPE:documentAccompagnant.html.twig', array('listPieceEntreprise' => $listPieceEntreprise, 'listeDocDelivres' => $listDocDelivres, 'idd' => $idd
                    , 'dd' => $dossierDemande, 'idS' => $idS, 'cheminUpload' => $cheminUpload));
    }
	
	function sendMail($sujet, $texte, $utilisateur) {
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

}
