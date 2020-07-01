<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BanquemondialeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BanquemondialeBundle\Entity\Quittance;
use BanquemondialeBundle\Entity\RepartitionQuittance;
use BanquemondialeBundle\Entity\FormulaireDelivre;
use BanquemondialeBundle\Form\QuittanceType;
use BanquemondialeBundle\Form\QuittanceSearchType;
use BanquemondialeBundle\Form\RepartitionQuittanceSearchType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use \DateTime;

//use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of QuittanceController
 *
 * @author isow
 */
class QuittanceController extends Controller
{

    public function indexAction()
    {

        $message = '';
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idl = $langue->getId();
        $quittance = new Quittance();

        if ($user->getEntreprise() /* && $user->getEntreprise()->getIsSiege() == false */) {

//            if($user->getEntreprise()->getIsSiege()==true){
//                $listQuittance = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceEnAttenteByParametres(null, $langue->getId(), $user->getId(), 25, null);
//            }else{ }
            $listQuittance = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceEnAttenteByParametres(null, $langue->getId(), $user->getId(), 25, $user->getEntreprise()->getSousPrefecture()->getId());


        }

        if ($request->getMethod() == 'POST') {

            $data = $request->request->all()['quittances'];
          ///  die(dump($data));
            $listQuittance = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceEnAttenteByParametres($data, $langue->getId(), $user->getId(), null);
            if ($user->getEntreprise() /* && $user->getEntreprise()->getIsSiege() == false */) {
                if ($user->getEntreprise()->getSousPrefecture()) {
                    $idS = $user->getEntreprise()->getSousPrefecture()->getId();
                    //die(dump($idS));
                    $listQuittance = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceEnAttenteByParametres($data, $langue->getId(), $user->getId(), null, $idS);
                }
            }
        }
        $form = $this->createForm(new QuittanceSearchType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('BanquemondialeBundle:Default:Quittance/layout/index.html.twig', array('form' => $form->createView(), 'message' => $message, 'listQuittance' => $listQuittance));
    }

    public function reportingAction()
    {

        $message = '';
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idl = $langue->getId();
        $quittance = new Quittance();

        if ($user->getEntreprise() && $user->getEntreprise()->getIsSiege() == false) {
            $listQuittance = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceValideByParametres(null, $langue->getId(), $user->getId(), 25, $user->getEntreprise()->getSousPrefecture()->getId());
        } else {
            $listQuittance = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceValideByParametres(null, $langue->getId(), $user->getId(), 25);
        }
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['quittances'];
            //die(dump($data));
            if ($user->getEntreprise() && $user->getEntreprise()->getIsSiege() == false) {
                $listQuittance = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceValideByParametres(null, $langue->getId(), $user->getId(), null, $user->getEntreprise()->getSousPrefecture()->getId());
            } else {
                $listQuittance = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceValideByParametres($data, $langue->getId(), $user->getId());
                //die(dump($listQuittance));
            }
        }
        $form = $this->createForm(new QuittanceSearchType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('BanquemondialeBundle:Default:Quittance/layout/reporting.html.twig', array('form' => $form->createView(), 'message' => $message, 'listQuittance' => $listQuittance));
    }

    public function chargerAction($idq)
    {
        if ($this->get('monServices')->pingIPServer()==true){
            $verificatioTemoin= $this->get('monservices')->verifyQuittancePayementStstus($idq);
           // var_dump($verificatioTemoin);die();
        }else{
            $verificatioTemoin=false;
        }


        if ($verificatioTemoin==true){
            return $this->redirectToRoute('reporting_quittance');
        }
        else{
            $message = '';
            $em = $this->getDoctrine()->getManager();
            $user = $this->container->get('security.context')->getToken()->getUser();
            $request = $this->get('request');
            $codLang = $request->getLocale();
            $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
            $idl = $langue->getId();
            $actualDate = new \DateTime();
            $quittance = $em->getRepository('BanquemondialeBundle:Quittance')->find($idq);
            if ($quittance->getDatePaiement() == null) {
                $quittance->setDatePaiement($actualDate);
            }
            $quittance->setMontantVerse($quittance->getMontantTotalFacture());
            $dossierDemande = $quittance->getDossierDemande();
            $typeDossier = $dossierDemande->getTypeDossier();
            $quittance->setTypeDossier($dossierDemande->getTypeDossier());
            $montant = $quittance->getMontantRestant();
            $nomCommercial = $quittance->getDenominationSociale();
            $poleCaisse = $user->getPole(); // $em->getRepository('ParametrageBundle:Pole')->getPoleCaisse();
            $documentCaisse = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('dossierDemande' => $dossierDemande->getId(), 'pole' => $poleCaisse->getId()));
            $definedFormeJuridiqueTraduction = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('formeJuridique' => $quittance->getFormeJuridique(), 'langue' => $langue));
            $definedModeTraduit = null;
            if ($quittance->getModePaiement()) {
                $definedModeTraduit = $em->getRepository('BanquemondialeBundle:ModePaiementTraduction')->findOneBy(array('modePaiement' => $quittance->getModePaiement(), 'langue' => $langue));
            }
            $form = $this->createForm(new QuittanceType(array('langue' => $langue, 'formeJTraduit' => $definedFormeJuridiqueTraduction, 'modeTraduit' => $definedModeTraduit)), $quittance);
            if ($request->getMethod() == 'POST') {
                $quittance = $em->getRepository('BanquemondialeBundle:Quittance')->find($idq);
                if ($request->request->has('textAreaModifier')) {
                    $motif = $request->get("textAreaModifier");
                    $quittance->setStatut(3);
                    $quittance->setMotif($motif);
                    $em->persist($quittance);
                    //Mise a documentacoolecter
                    if ($documentCaisse) {
                        $statutTraitementModifier = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(3);
                        $documentCaisse->setDateDerniereModification(new \DateTime());
                        $documentCaisse->setStatutTraitement($statutTraitementModifier);
                        $documentCaisse->setMotif($motif);
                        $em->persist($documentCaisse);
                    }
                    if ($dossierDemande->getUtilisateurDepot()) {
                        $dossierDemande->setStatut(-2);
                    } else {
                        $dossierDemande->setStatut(3);
                    }
                    $dossierDemande->setMotif($motif);
                    $em->persist($dossierDemande);
                    $em->flush();
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $this->get('session')->getFlashBag()->add('info', $message);
                    return new RedirectResponse($this->container->get('router')->generate('gestion_caisse'));
                } else {
                    $form->bind($request);
                    if ($form->isValid()) {
                        $data = $request->request->all()['quittance'];
                        // var_dump($quittance->getModePaiement()->getId());die();
                        // Recuperation du  Mode de payement Choisi par l'utilisateur
                        $payMode = $quittance->getModePaiement()->getId();
                        ///  Recuperation Mode de paiement 3 = Orange-Money dans la BAse de Donee
                        $isOrangewebPay = $this->get('monservices')->getModePayement(3);
                        ///> On test si le Mode de paiement Choisi est bien egale 3 (Orange-Money) dans la DB /////
                        if ($payMode == $isOrangewebPay->getId()) {

                            $session = $request->getSession();
                            /// Supression  variables de session
                            $session->remove('sessionData');
                            $session->remove('sessionIdq');
                            $session->remove('codLang');
                            /// Creation  variables de session
                            if (!$session->has('sessionData')) {
                                $session->set('sessionData', $data);
                                $session->set('sessionIdq', $idq);
                                $session->set('codLang', $request->getLocale());
                            }
                            // On envoi un SMS===
                            $this->get('monservices')->payementSmsOrange($data['telephone'],$dossierDemande);
                            // ========= On redirige user sur la confirmation=======////////
                            return $this->redirectToRoute('confirmation-payement-orange-money');
                        }
                        $quittance = $em->getRepository('BanquemondialeBundle:Quittance')->find($idq);
                        $quittance->setSerie($data['serie']);
                        $quittance->setNumeroVolume($data['numeroVolume']);
                        $quittance->setNumeroQuittance($data['numeroQuittance']);
                        $quittance->setRefTitreRecette($data['refTitreRecette']);
                        $quittance->setTypeDossier($typeDossier);
                        $natureRecette = $em->getRepository('BanquemondialeBundle:NatureRecette')->find($data['natureRecette']);
                        $quittance->setNatureRecette($natureRecette);
                        $montantVerse = $data['montantVerse'];
                        $modePaiement = $em->getRepository('BanquemondialeBundle:ModePaiementTraduction')->find($data['modePaiement'])->getModePaiement();
                        if ($montantVerse > $quittance->getMontantRestant()) {
                            $translated = $this->get('translator')->trans("message_paiement_superieur_facture");
                            $this->get('session')->getFlashBag()->add('info', $translated);

                            $quittance->setMontantVerse($montantVerse);
                            $form = $this->createForm(new QuittanceType(array('langue' => $langue, 'formeJTraduit' => $definedFormeJuridiqueTraduction, 'modeTraduit' => $definedModeTraduit)), $quittance);
                        }
                        else if ($montantVerse < $quittance->getMontantRestant()) {
                            $translated = $this->get('translator')->trans("message_paiement_inferieur_facture");
                            $this->get('session')->getFlashBag()->add('info', $translated);
                            $quittance->setMontantVerse($montantVerse);
                            $form = $this->createForm(new QuittanceType(array('langue' => $langue, 'formeJTraduit' => $definedFormeJuridiqueTraduction, 'modeTraduit' => $definedModeTraduit)), $quittance);
                        }
                        else {
                            $quittance->setUtilisateur($user);
                            $quittance->setMontantVerse($montantVerse);
                            $quittance->setMontantRestant(0);
                            $quittance->setModePaiement($modePaiement);
                            $quittance->setIsPaid(true);
                            $em->persist($quittance);
                            //Mise à jour dossier demande pour le pole APIP -Cicuit depot -caisse -agent saisi
                            $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
                            $userDepot = $dossierDemande->getUtilisateurDepot();
                            if ($userDepot) {
                                $poleUserDepot = $userDepot->getPole();
                                if ($poleAPIP == $poleUserDepot) {
                                    //die(dump($userDepot));
                                    //Recherche des Agent de saisi pour continuer le traitement du dossier
                                    $profilSaisi = $em->getRepository('UtilisateursBundle:Profile')->findOneByDescription('saisi');
                                    if ($profilSaisi) {
                                        $dossierDemande->setStatut(null);

                                        if (!$dossierDemande->getUtilisateur()) {
                                            $idUserAgentSaisi = $em->getRepository('BanquemondialeBundle:DossierDemande')->findLeastLoadedUserSaisi($userDepot->getEntreprise()->getId());
                                            $userAgentSaisi = $em->getRepository('UtilisateursBundle:Utilisateurs')->find($idUserAgentSaisi);
                                            //die(dump($userAgentSaisi));
                                            //$firstUserAgentSaisi = $em->getRepository('UtilisateursBundle:Utilisateurs')->findOneBy(array('profile' => $profilSaisi->getId(), 'pole' => $poleUserDepot->getId(), 'entreprise' => $userDepot->getEntreprise()->getId()));
                                            $dossierDemande->setUtilisateur($userAgentSaisi);
                                        }
                                        $em->persist($dossierDemande);
                                    }
                                }
                            }
                            //mise a jour repartition facturation pour brouillard de caisse et autres
                            $listRepartitionQuittance = $em->getRepository('BanquemondialeBundle:RepartitionQuittance')->findByDossierDemande($quittance->getDossierDemande());
                            if ($quittance->getDatePaiement() == null) {
                                $dateReceptionPaiement = new \DateTime();
                            } else {
                                $dateReceptionPaiement = $quittance->getDatePaiement();
                            }
                            //retrait de l'ancienne repartition du dossier
                            foreach ($listRepartitionQuittance as $repartitionQuittance) {
                                $em->remove($repartitionQuittance);
                                $em->flush();
                            }
                            //mise a jour de la repartition
                            $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
                            $fraisApip = $em->getRepository('ParametrageBundle:Tarification')->getMontantFraisByPole($dossierDemande->getFormeJuridique()->getId(), $dossierDemande->getTypeDossier()->getId(), $poleAPIP->getId());
                            $listFraisDossier = $em->getRepository('ParametrageBundle:Tarification')->getListeFraisAPayer($dossierDemande->getId());

                            foreach ($fraisApip as $frais) {
                                $repartitionQuittance = new RepartitionQuittance();
                                $repartitionQuittance->setMontantDu($frais->getMontant());
                                $repartitionQuittance->setMontantVerse($frais->getMontant());
                                $repartitionQuittance->setPole($frais->getPole());
                                $repartitionQuittance->setDossierDemande($dossierDemande);
                                $repartitionQuittance->setDatePaiement($dateReceptionPaiement);
                                $repartitionQuittance->setEntreprise($user->getEntreprise());
                                $repartitionQuittance->setModePaiement($form->get('modePaiement')->getData());
                                $em->persist($repartitionQuittance);
                            }
                            foreach ($listFraisDossier as $frais) {
                                $repartitionQuittance = new RepartitionQuittance();
                                $repartitionQuittance->setMontantDu($frais->getMontant());
                                $repartitionQuittance->setMontantVerse($frais->getMontant());
                                $repartitionQuittance->setPole($frais->getPole());
                                $repartitionQuittance->setDossierDemande($dossierDemande);
                                $repartitionQuittance->setDatePaiement($dateReceptionPaiement);
                                $repartitionQuittance->setEntreprise($user->getEntreprise());
                                $repartitionQuittance->setModePaiement($form->get('modePaiement')->getData());
                                $em->persist($repartitionQuittance);
                            }
                            $em->flush();
                            //fin mise a jour repartition facturation
                            //Mise à Jour Document Collected
                            $statutEnDelivre = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(2);
                            $date = new \DateTime();
                            $documentSuivant = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findPoleSuivant($documentCaisse->getOrdre(), $dossierDemande->getId());
                            if ($documentCaisse) {
                                //echo "<script>console.log( 'document caisse' );</script>";
                                $documentCaisse->setStatutTraitement($statutEnDelivre);

                                if ($documentCaisse->getDateDelivrance() == null) {
                                    $documentCaisse->setDateDelivrance($date);
                                } else {
                                    $documentCaisse->setDateDerniereModification($date);
                                }
                                $em->persist($documentCaisse);
                                $em->flush();

                                //save quittance
                                $this->sauvergarderQuittanceDelivre($idq);
                                //fin save
                                //mise a jour pole suivant sera ajouter au uniquement en cas de user notaire
                                //$poleNotaire = $em->getRepository('ParametrageBundle:Pole')->getPoleByName("Notaire");
                                $userDeCreation = $dossierDemande->getUtilisateur();
                                $poleUser = $userDeCreation->getPole();
                                if ($poleUser != $poleAPIP) {
                                    if ($documentSuivant) {
                                        $statutEncours = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(1);
                                        $polesSuivants = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findPolesSuivants($documentSuivant[0]->getOrdre(), $dossierDemande->getId());
                                        if ($polesSuivants) {
                                            //$message = $this->get('translator')->trans('message_dossier_recu');
                                            foreach ($polesSuivants as $poleSuivant) {
                                                $poleSuivant->setStatutTraitement($statutEncours);
                                                if ($poleSuivant->getDateSoumission() == null) {
                                                    $poleSuivant->setDateSoumission(new \Datetime());
                                                }
                                                $em->persist($poleSuivant);
                                            }
                                        }
                                    } else {
                                        //Signifie que la caisse est le dernier pole
                                        $dossierDemande->setStatut(2);
                                        $dossierDemande->setDateDelivrance($date);
                                    }
                                } else {
                                    //cas apip, la redirection se fait uniquement pour le profil depot+saisi
                                    $userProfile = $userDeCreation->getProfile();
                                    if ($userProfile->getDescription() != "saisi" && $userProfile->getDescription() != "dépot") {
                                        if ($documentSuivant) {
                                            $statutEncours = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(1);
                                            $polesSuivants = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findPolesSuivants($documentSuivant[0]->getOrdre(), $dossierDemande->getId());
                                            if ($polesSuivants) {
                                                //$message = $this->get('translator')->trans('message_dossier_recu');
                                                foreach ($polesSuivants as $poleSuivant) {
                                                    $poleSuivant->setStatutTraitement($statutEncours);
                                                    if ($poleSuivant->getDateSoumission() == null) {
                                                        $poleSuivant->setDateSoumission(new \Datetime());
                                                    }
                                                    $em->persist($poleSuivant);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            //Fin mise a jour
                            $em->flush();
                            $translated = $this->get('translator')->trans("message_facture_payee");
                            $this->get('session')->getFlashBag()->add('info', $translated);
                            return $this->redirectToRoute('reporting_quittance');
                        }
                    }
                }
            }
            return $this->render('BanquemondialeBundle:Default:Quittance/layout/new.html.twig', array('form' => $form->createView(), 'nomCommercial' => $nomCommercial
            , 'message' => $message, 'quittance' => $quittance, 'definedFormeJuridiqueTraduction' => $definedFormeJuridiqueTraduction
            , 'montant' => $montant, 'dd' => $dossierDemande));
        }
    }
    public function enregistrerAction()
    {

        $message = '';
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idl = $langue->getId();
        $quittance = new Quittance();

        $form = $this->createForm(new QuittanceType(array('langue' => $langue, 'formeJTraduit' => null)), $quittance);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $request->request->all()['quittance'];

                $actualDate = new \DateTime();
                $quittance->setUtilisateur($user);

                $em->persist($quittance);
                $em->flush();

                $translated = $this->get('translator')->trans("message_quittance_ajouter_succes");
                $this->get('session')->getFlashBag()->add('info', $translated);

                return $this->redirectToRoute('creer_quittance');
            } else {
                $data = $request->request->all()['quittance'];
                $formeJuridiqueTraduction = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->find($data['formeJuridique']);
                return $this->render('BanquemondialeBundle:Default:Quittance/layout/new.html.twig', array('form' => $form->createView(), 'message' => $message, 'quittance' => $quittance, 'definedFormeJuridiqueTraduction' => $formeJuridiqueTraduction));
            }
        }

        $form = $this->createForm(new QuittanceType(array('langue' => $langue, 'formeJTraduit' => null)), $quittance);
        return $this->render('BanquemondialeBundle:Default:Quittance/layout/new.html.twig', array('form' => $form->createView(), 'message' => $message, 'quittance' => $quittance, 'definedFormeJuridiqueTraduction' => null));
    }

    public function reportQuittanceAction($idq)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idl = $langue->getId();
        if ($user->getEntreprise()->getIsSiege() == true) {
            $quittance = $em->getRepository('BanquemondialeBundle:Quittance')->findOneBy(array('id' => $idq));
        } else {
            $quittance = $em->getRepository('BanquemondialeBundle:Quittance')->findOneBy(array('id' => $idq, 'utilisateur' => $user));
        }

        $utilisateur = $user->getPrenom() . ' ' . $user->getNom();
        //die(dump($utilisateur));
        if ($quittance) {
            $definedFormeJuridiqueTraduction = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('formeJuridique' => $quittance->getFormeJuridique(), 'langue' => $langue));

            $definedModePaiementTraduction = $em->getRepository('BanquemondialeBundle:ModePaiementTraduction')->findOneBy(array('modePaiement' => $quittance->getModePaiement(), 'langue' => $langue));
            $definedNatureRecette = $em->getRepository('BanquemondialeBundle:NatureRecette')->find($quittance->getNatureRecette());
            $natureRecette = $definedNatureRecette->getLibelle();
            $montant = $quittance->getMontantTotalFacture();
            //$montantLettre=$this->chifre_en_lettre($montant);
            //$lettre=new ChiffreEnLettre(); 
            $montantLettre = $this->Conversion($montant);
            //die(dump($montantLettre));

            $html = $this->renderView('BanquemondialeBundle:Default:Quittance/layout/visualiser.html.twig', array(
                'quittance' => $quittance, 'definedFormeJuridiqueTraduction' => $definedFormeJuridiqueTraduction,
                'definedModePaiementTraduction' => $definedModePaiementTraduction, 'montantLettre' => $montantLettre,
                'definedNatureRecette' => $natureRecette, 'user' => $utilisateur));

            $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
            $html2pdf->pdf->SetDisplayMode('real');
            $html2pdf->writeHTML($html);
            $html2pdf->Output('quittance.pdf');
            exit;
        } else {
            exit;
        }
    }

    public function sauvergarderQuittanceDelivre($idq)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idl = $langue->getId();
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $quittance = $em->getRepository('BanquemondialeBundle:Quittance')->findOneBy(array('id' => $idq, 'utilisateur' => $user));
        $utilisateur = $user->getPrenom() . ' ' . $user->getNom();
        //die(dump($utilisateur));
        if ($quittance) {
            $definedFormeJuridiqueTraduction = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('formeJuridique' => $quittance->getFormeJuridique(), 'langue' => $langue));

            $definedModePaiementTraduction = $em->getRepository('BanquemondialeBundle:ModePaiementTraduction')->findOneBy(array('modePaiement' => $quittance->getModePaiement(), 'langue' => $langue));
            $definedNatureRecette = $em->getRepository('BanquemondialeBundle:NatureRecette')->find($quittance->getNatureRecette());
            $natureRecette = $definedNatureRecette->getLibelle();
            $montant = $quittance->getMontantTotalFacture();
            //$montantLettre=$this->chifre_en_lettre($montant);
            //$lettre=new ChiffreEnLettre(); 
            $montantLettre = $this->Conversion($montant);
            $date = new \DateTime();
            $html = $this->renderView('BanquemondialeBundle:Default:Quittance/layout/visualiser.html.twig', array(
                'quittance' => $quittance, 'definedFormeJuridiqueTraduction' => $definedFormeJuridiqueTraduction,
                'definedModePaiementTraduction' => $definedModePaiementTraduction, 'montantLettre' => $montantLettre,
                'definedNatureRecette' => $natureRecette, 'user' => $utilisateur));
            $dossierDemande = $quittance->getDossierDemande();
            $leFormulaire_a_delive = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->findOneBy(array('pole' => $pole->getId()));

            $nomFichier = "formulaire" . $dossierDemande->getId() . "_" . $leFormulaire_a_delive->getId() . ".pdf";

            $formDelivre = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande));
            if (!$formDelivre) {
                $formulaireDelivre = new FormulaireDelivre();
                $formulaireDelivre->setPole($pole);
                $formulaireDelivre->setDossierDemande($dossierDemande);
                if ($formulaireDelivre->getDateCreation() == null) {
                    $formulaireDelivre->setDateCreation($date);
                }

                $formulaireDelivre->setNomFichier($nomFichier);
                $formulaireDelivre->setLibelleFormulaireDelivre($leFormulaire_a_delive);
                $em->persist($formulaireDelivre);
            } else {
                $formDelivre->setDateCreation($date);
                $em->persist($formDelivre);
            }
            $temp = $cheminDownload . $dossierDemande->getId();
            if (!is_dir($temp)) {
                mkdir($temp);
            }
            $em->flush();
            $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
            $html2pdf->pdf->SetDisplayMode('real');
            $html2pdf->writeHTML($html);
            $html2pdf->Output($cheminDownload . $dossierDemande->getId() . '\\' . $nomFichier, 'F');
        }
    }

    public function reportAttestationPayementAction($idq)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idl = $langue->getId();
        $quittance = $em->getRepository('BanquemondialeBundle:Quittance')->findOneBy(array('id' => $idq, 'utilisateur' => $user));
        $utilisateur = strtoupper($user->getNom()) . ' ' . $user->getPrenom();
        //die(dump($utilisateur));
        if ($quittance) {
            $definedFormeJuridiqueTraduction = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('formeJuridique' => $quittance->getFormeJuridique(), 'langue' => $langue));

            $definedModePaiementTraduction = $em->getRepository('BanquemondialeBundle:ModePaiementTraduction')->findOneBy(array('modePaiement' => $quittance->getModePaiement(), 'langue' => $langue));
            $definedNatureRecette = $em->getRepository('BanquemondialeBundle:NatureRecette')->find($quittance->getNatureRecette());
            $natureRecette = $definedNatureRecette->getLibelle();
            $montant = $quittance->getMontantTotalFacture();
            $representant = $em->getRepository('BanquemondialeBundle:Representant')->findOneByDossierDemande($quittance->getDossierDemande());
            //$montantLettre=$this->chifre_en_lettre($montant);
            //$lettre=new ChiffreEnLettre(); 
            $montantLettre = $this->Conversion($montant);
            //die(dump($montantLettre));

            $html = $this->renderView('BanquemondialeBundle:Default:Quittance/layout/visualiserAttestationPayement.html.twig', array(
                'quittance' => $quittance, 'definedFormeJuridiqueTraduction' => $definedFormeJuridiqueTraduction,
                'definedModePaiementTraduction' => $definedModePaiementTraduction, 'montantLettre' => $montantLettre,
                'definedNatureRecette' => $natureRecette, 'user' => $utilisateur, 'representant' => $representant));

            $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
            $html2pdf->pdf->SetDisplayMode('real');
            $html2pdf->writeHTML($html);
            $html2pdf->Output('quittance.pdf');
            exit;
        }
    }

    public function brouillardCaisseAction()
    {

        $poleChoisi=0;
        $formeJuridique=0;
        $modePaiement=0;
        $message = '';
        $montantTotal = 0;
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $entreprise = $user->getEntreprise()->getId();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $poles = $em->getRepository('ParametrageBundle:Pole')->getPolesQuittance();
        $form = $this->createForm(new RepartitionQuittanceSearchType(array('langue' => $langue)));
        $form->bind($request);
        $datedebut = $date = date_format(new \DateTime(),'Y-m-d');
        $datefin = $date = date_format(new \DateTime(),'Y-m-d');
        $nomCaisse = null;

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['repartitionType'];
            if (!empty( $data['debutPeriode'])) $datedebut = $data['debutPeriode'];
           if (!empty($data['finPeriode'])) $datefin = $data['finPeriode'];
            if (!empty( $data['pole'])) $poleChoisi = $data['pole'];
            if (!empty($data['formeJuridique']))$formeJuridique = $data['formeJuridique'];
            if (!empty($data['modePaiement'])) {
                $modePaiement = $data['modePaiement'];
            }
            if (array_key_exists("entreprise", $data)) {
                if (!empty($data['entreprise'])) $entreprise = $data['entreprise'];
                $caisse = $em->getRepository('BanquemondialeBundle:Entreprise')->find($entreprise);
                if (!empty($caisse)) {
                    $nomCaisse = $caisse->getDenomination();
                }
            }
            $totaux = $em->getRepository('BanquemondialeBundle:RepartitionQuittance')->findRepartitionQuittanceByParametres($datedebut, $datefin, $entreprise, $poleChoisi, $formeJuridique,$modePaiement);
            $repartitions = $em->getRepository('BanquemondialeBundle:RepartitionQuittance')->findBrouillardByParametres($datedebut, $datefin, $entreprise, null, $formeJuridique, $idLangue,$modePaiement);
            if (!empty($poleChoisi)) {
                $poles = $em->getRepository('ParametrageBundle:Pole')->find($poleChoisi);
                $repartitions = $em->getRepository('BanquemondialeBundle:RepartitionQuittance')->findBrouillardPoleByParameters($datedebut, $datefin, $entreprise, $poleChoisi, $formeJuridique, $idLangue);

                foreach ($repartitions as $repartition) {
                    $montant = $repartition['montant'];
                    $montantTotal = $montantTotal + $montant;
                }
            }
            else {

                foreach ($totaux as $total) {
                    $montant = $total['montant'];
                    $montantTotal = $montantTotal + $montant;
                }
            }
         //   die(dump("DateDebu=$datedebut  ; DateFin=$datefin ; entreprise= $entreprise ; poleChoisi= $poleChoisi ; formeJuridique=  $formeJuridique ; modePaiement= $modePaiement ;idLangue= $idLangue"));

            return $this->render('BanquemondialeBundle:Default:Quittance/layout/brouillard.html.twig', array(
                'message' => $message,
                'repartitions' => $repartitions,
                'montantTotal' => $montantTotal,
                'poles' => $poles,
                'totaux' => $totaux,
                'poleChoisi' => $poleChoisi,
                'dateDebut' => $datedebut,
                'dateFin' => $datefin,
                'nomCaisse' => $nomCaisse,
                'form' => $form->createView(),

                'formeJuridique'=>$formeJuridique,
                'modePaiement'=>$modePaiement,
                'entreprise'=>$entreprise,
                'idLangue'=>$idLangue,
            ));
        }

        $actualDate = new \DateTime();
        $actualDate = $actualDate->format('Y-m-d H:i:s');
        $repartitions = $em->getRepository('BanquemondialeBundle:RepartitionQuittance')->findBrouillardByParametres($actualDate, $actualDate, $entreprise, $poleChoisi, $formeJuridique, $idLangue);
        $totaux = $em->getRepository('BanquemondialeBundle:RepartitionQuittance')->findRepartitionQuittanceByParametres($actualDate, $actualDate, $entreprise, $poleChoisi, $formeJuridique);
        foreach ($totaux as $total) {
            $montant = $total['montant'];
            $montantTotal = $montantTotal + $montant;
        }
     //   die(dump($montantTotal));
      // die(dump("DateDebu=$datedebut  ; DateFin=$datefin; entreprise= $entreprise ; poleChoisi= $poleChoisi ; formeJuridique=  $formeJuridique ; modePaiement= $modePaiement ;idLangue= $idLangue"));

        return $this->render('BanquemondialeBundle:Default:Quittance/layout/brouillard.html.twig', array(
            'message' => $message,
            'repartitions' => $repartitions,
            'montantTotal' => $montantTotal,
            'poles' => $poles,
            'poleChoisi' => $poleChoisi,
            'totaux' => $totaux,
            'dateDebut' => $datedebut,
            'dateFin' => $datefin,
            'nomCaisse' => $nomCaisse,
            'form' => $form->createView(),

            'formeJuridique' => $formeJuridique,
            'modePaiement' => $modePaiement,
            'entreprise' => $entreprise,
            'idLangue'=>$idLangue,


        ));
    }



    public function repartitionEncaissementAction()
    {

        $message = '';
        $montantTotal = 0;
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();

        $form = $this->createForm(new RepartitionQuittanceSearchType(array('langue' => $langue)));
        $form->bind($request);

        $entreprise = $user->getEntreprise()->getId();


        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['repartitionType'];

            $datedebut = $data['debutPeriode'];
            $datefin = $data['finPeriode'];
            $modePaiement = $data['modePaiement'];

            if (array_key_exists("entreprise", $data)) {
                $entreprise = $data['entreprise'];
            }

            $repartitions = $em->getRepository('BanquemondialeBundle:RepartitionQuittance')->findRepartitionQuittanceByParametres($datedebut, $datefin, $entreprise,$modePaiement);

            foreach ($repartitions as $repartition) {
                $montant = $repartition['montant'];
                $montantTotal = $montantTotal + $montant;
            }

            return $this->render('BanquemondialeBundle:Default:Quittance/layout/encaissement.html.twig', array(
                    'message' => $message,
                    'repartitions' => $repartitions,
                    'montantTotal' => $montantTotal,
                    'form' => $form->createView()
                )
            );
        }

        $data = null;
        $repartitions = $em->getRepository('BanquemondialeBundle:RepartitionQuittance')->findRepartitionQuittanceByParametres(null, null, $entreprise);

        foreach ($repartitions as $repartition) {
            $montant = $repartition['montant'];
            $montantTotal = $montantTotal + $montant;
        }

        return $this->render('BanquemondialeBundle:Default:Quittance/layout/encaissement.html.twig', array('message' => $message, 'repartitions' => $repartitions, 'montantTotal' => $montantTotal, 'form' => $form->createView()));
    }
    public function caissePoleAction()
    {

        $message = '';
        $montantTotal = 0;
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();

        $repartitions = $em->getRepository('BanquemondialeBundle:RepartitionQuittance')->findBrouillardPoleByParameters(null, null, null, $pole, null, $idLangue);
        //die(dump($repartitions));
        $form = $this->createForm(new RepartitionQuittanceSearchType(array('langue' => $langue)));

        $form->bind($request);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['repartitionType'];

            $datedebut = $data['debutPeriode'];
            $datefin = $data['finPeriode'];
            $formeJuridique = $data['formeJuridique'];

            $repartitions = $em->getRepository('BanquemondialeBundle:RepartitionQuittance')->findBrouillardPoleByParameters($datedebut, $datefin, null, $pole, $formeJuridique, $idLangue);
        }

        foreach ($repartitions as $repartition) {
            $montant = $repartition['montant'];
            $montantTotal = $montantTotal + $montant;
        }

        return $this->render('BanquemondialeBundle:Default:Quittance/layout/brouillardPole.html.twig', array('repartitions' => $repartitions, 'montantTotal' => $montantTotal, 'form' => $form->createView()));
    }
    //NE GERE PAS TOUT (les pluriels...)
    #Variables
    public $leChiffreSaisi;
    public $enLettre = '';
    public $chiffre = array(1 => "un", 2 => "deux", 3 => "trois", 4 => "quatre", 5 => "cinq", 6 => "six", 7 => "sept", 8 => "huit", 9 => "neuf", 10 => "dix", 11 => "onze", 12 => "douze", 13 => "treize", 14 => "quatorze", 15 => "quinze", 16 => "seize", 17 => "dix-sept", 18 => "dix-huit", 19 => "dix-neuf", 20 => "vingt", 30 => "trente", 40 => "quarante", 50 => "cinquante", 60 => "soixante", 70 => "soixante-dix", 80 => "quatre-vingt", 90 => "quatre-vingt-dix");

    #Fonction de conversion appelÃ©e dans la feuille principale

    function Conversion($sasie)
    {
        $this->enLettre = '';
        $sasie = trim($sasie);

        #suppression des espaces qui pourraient exister dans la saisie
        $nombre = '';
        $laSsasie = explode(' ', $sasie);
        foreach ($laSsasie as $partie)
            $nombre .= $partie;

        #suppression des zÃ©ros qui prÃ©cÃ©deraient la saisie
        $nb = strlen($nombre);
        for ($i = 0; $i <= $nb;) {
            if (substr($nombre, $i, 1) == 0) {
                $nombre = substr($nombre, $i + 1);
                $nb = $nb - 1;
            } elseif (substr($nombre, $i, 1) <> 0) {
                $nombre = substr($nombre, $i);
                break;
            }
        }
        #echo $nombre;
        #$this->SupZero($nombre);
        #le nombre de caract que comporte le nombre saisi de sa forme sans espace et sans 0 au dÃ©but
        $nb = strlen($nombre);
        #echo $nb.'<br/ >';
        #$this->leChiffreSaisi=$nombre;
        #conversion du chiffre saisi en lettre selon les cas
        switch ($nb) {
            case 0:
                $this->enLettre = 'zéro';
            case 1:
                if ($nombre == 0) {
                    $this->enLettre = 'zéro';
                    break;
                } elseif ($nombre <> 0) {
                    $this->Unite($nombre);
                    break;
                }

            case 2:
                $unite = substr($nombre, 1);
                $dizaine = substr($nombre, 0, 1);
                $this->Dizaine(0, $nombre, $unite, $dizaine);
                break;

            case 3:
                $unite = substr($nombre, 2);
                $dizaine = substr($nombre, 1, 1);
                $centaine = substr($nombre, 0, 1);
                $this->Centaine(0, $nombre, $unite, $dizaine, $centaine);
                break;

            #cas des milles
            case ($nb > 3 and $nb <= 6):
                $unite = substr($nombre, $nb - 1);
                $dizaine = substr($nombre, ($nb - 2), 1);
                $centaine = substr($nombre, ($nb - 3), 1);
                $mille = substr($nombre, 0, ($nb - 3));
                $this->Mille($nombre, $unite, $dizaine, $centaine, $mille);
                break;

            #cas des millions
            case ($nb > 6 and $nb <= 9):
                $unite = substr($nombre, $nb - 1);
                $dizaine = substr($nombre, ($nb - 2), 1);
                $centaine = substr($nombre, ($nb - 3), 1);
                $mille = substr($nombre, -6);
                $million = substr($nombre, 0, $nb - 6);
                $this->Million($nombre, $unite, $dizaine, $centaine, $mille, $million);
                break;

            #cas des milliards
            /* case ($nb>9 and $nb<=12):
              $unite=substr($nombre,$nb-1);
              $dizaine=substr($nombre,($nb-2),1);
              $centaine=substr($nombre,($nb-3),1);
              $mille=substr($nombre,-6);
              $million=substr($nombre,-9);
              $milliard=substr($nombre,0,$nb-9);
              Milliard($nombre,$unite,$dizaine,$centaine,$mille,$million,$milliard);
              break; */
        }
        if (!empty($this->enLettre))
            return $this->enLettre;
    }

    #Gestion des miiliards
    /*
      function Milliard($nombre,$unite,$dizaine,$centaine,$mille,$million,$milliard)
      {

      }
     */

    #Gestion des millions

    function Million($nombre, $unite, $dizaine, $centaine, $mille, $million)
    {
        #si les mille comportent un seul chiffre
        #$cent represente les 3 premiers chiffres du chiffre ex: 321 dans 12502321
        #$mille represente les 3 chiffres qui suivent les cents ex: 502 dans 12502321
        #reste represente les 6 premiers chiffres du chiffre ex: 502321 dans 12502321

        $cent = substr($nombre, -3);
        $reste = substr($nombre, -6);

        if (strlen($million) == 1) {
            $mille = substr($nombre, 1, 3);
            $this->enLettre .= $this->chiffre[$million];
            if ($million == 1) {
                $this->enLettre .= ' million ';
            } else {
                $this->enLettre .= ' millions ';
            }
        } elseif (strlen($million) == 2) {
            $mille = substr($nombre, 2, 3);
            $nombre = substr($nombre, 0, 2);
            //echo $nombre;
            $this->Dizaine(0, $nombre, $unite, $dizaine);
            $this->enLettre .= 'millions ';
        } elseif (strlen($million) == 3) {
            $mille = substr($nombre, 3, 3);
            $nombre = substr($nombre, 0, 3);
            $this->Centaine(0, $nombre, $unite, $dizaine, $centaine);
            $this->enLettre .= 'millions ';
        }

        #recuperation des cens dans nombre
        #suppression des zÃ©ros qui prÃ©cÃ©deraient le $reste
        $nb = strlen($reste);
        for ($i = 0; $i <= $nb;) {
            if (substr($reste, $i, 1) == 0) {
                $reste = substr($reste, $i + 1);
                $nb = $nb - 1;
            } elseif (substr($reste, $i, 1) <> 0) {
                $reste = substr($reste, $i);
                break;
            }
        }
        $nb = strlen($reste);
        #si tous les chiffres apres les milions =000000 on affiche x million
        if ($nb == 0)
            ;
        else {
            #Gestion des milles
            #suppression des zÃ©ros qui prÃ©cÃ©deraient les milles dans $mille
            $nb = strlen($mille);
            for ($i = 0; $i <= $nb;) {
                if (substr($mille, $i, 1) == 0) {
                    $mille = substr($mille, $i + 1);
                    $nb = $nb - 1;
                } elseif (substr($mille, $i, 1) <> 0) {
                    $mille = substr($mille, $i);
                    break;
                }
            }
            #le nombre de caract que comporte le nombre saisi de sa forme sans espace et sans 0 au dÃ©but
            $nb = strlen($mille);
            #echo '<br />nb='.$nb.'<br />';
            if ($nb == 0)
                ;
            #AffichageResultat($enLettre);
            elseif ($nb == 1) {
                if ($mille == 1)
                    $this->enLettre .= 'mille ';
                else {
                    $this->Unite($mille);
                    $this->enLettre .= 'mille ';
                }
            } elseif ($nb == 2) {
                $this->Dizaine(1, $mille, $unite, $dizaine);
                $this->enLettre .= 'mille ';
            } elseif ($nb == 3) {
                $this->Centaine(1, $mille, $unite, $dizaine, $centaine);
                $this->enLettre .= 'mille ';
            }
            #Gestion des cents
            #suppression des zÃ©ros qui prÃ©cÃ©deraient les cents dans $cent
            $nb = strlen($cent);
            for ($i = 0; $i <= $nb;) {
                if (substr($cent, $i, 1) == 0) {
                    $cent = substr($cent, $i + 1);
                    $nb = $nb - 1;
                } elseif (substr($cent, $i, 1) <> 0) {
                    $cent = substr($cent, $i);
                    break;
                }
            }
            #le nombre de caract que comporte le nombre saisi de sa forme sans espace et sans 0 au dÃ©but
            $nb = strlen($cent);
            #echo '<br />nb='.$nb.'<br />';
            if ($nb == 0)
                ;
            #AffichageResultat($enLettre);
            elseif ($nb == 1)
                $this->Unite($cent);
            elseif ($nb == 2)
                $this->Dizaine(0, $cent, $unite, $dizaine);
            elseif ($nb == 3)
                $this->Centaine(0, $cent, $unite, $dizaine, $centaine);
        }
    }

    #Gestion des milles

    function Mille($nombre, $unite, $dizaine, $centaine, $mille)
    {
        #si les mille comportent un seul chiffre
        #$cent represente les 3 premiers chiffres du chiffre ex: 321 dans 12321
        if (strlen($mille) == 1) {
            $cent = substr($nombre, 1);
            #si ce chiffre=1
            if ($mille == 1)
                $this->enLettre .= '';
            #si ce chiffre<>1
            elseif ($mille <> 1)
                $this->enLettre .= $this->chiffre[$mille];
        } elseif (strlen($mille) > 1) {
            if (strlen($mille) == 2) {
                $cent = substr($nombre, 2);
                $nombre = substr($nombre, 0, 2);
                #echo $nombre;
                $this->Dizaine(1, $nombre, $unite, $dizaine);
            }
            if (strlen($mille) == 3) {
                $cent = substr($nombre, 3);
                $nombre = substr($nombre, 0, 3);
                #echo $nombre;
                $this->Centaine(1, $nombre, $unite, $dizaine, $centaine);
            }
        }

        $this->enLettre .= 'mille ';
        #recuperation des cens dans nombre
        #suppression des zÃ©ros qui prÃ©cÃ©deraient la saisie
        $nb = strlen($cent);
        for ($i = 0; $i <= $nb;) {
            if (substr($cent, $i, 1) == 0) {
                $cent = substr($cent, $i + 1);
                $nb = $nb - 1;
            } elseif (substr($cent, $i, 1) <> 0) {
                $cent = substr($cent, $i);
                break;
            }
        }
        #le nombre de caract que comporte le nombre saisi de sa forme sans espace et sans 0 au dÃ©but
        $nb = strlen($cent);
        #echo '<br />nb='.$nb.'<br />';
        if ($nb == 0)
            ; //AffichageResultat($enLettre);
        elseif ($nb == 1)
            $this->Unite($cent);
        elseif ($nb == 2)
            $this->Dizaine(0, $cent, $unite, $dizaine);
        elseif ($nb == 3)
            $this->Centaine(0, $cent, $unite, $dizaine, $centaine);
    }

    #Gestion des centaines

    function Centaine($inmillier, $nombre, $unite, $dizaine, $centaine)
    {

        $unite = substr($nombre, 2);
        $dizaine = substr($nombre, 1, 1);
        $centaine = substr($nombre, 0, 1);
        #comme 700
        if ($unite == 0 and $dizaine == 0) {
            if ($centaine == 1)
                $this->enLettre .= 'cent';
            elseif ($centaine <> 1) {
                if ($inmillier == 0)
                    $this->enLettre .= ($this->chiffre[$centaine] . ' cents') . ' ';
                if ($inmillier == 1)
                    $this->enLettre .= ($this->chiffre[$centaine] . ' cent') . ' ';
            }
        } #comme 705
        elseif ($unite <> 0 and $dizaine == 0) {
            if ($centaine == 1)
                $this->enLettre .= ('cent ' . $this->chiffre[$unite]) . ' ';
            elseif ($centaine <> 1)
                $this->enLettre .= ($this->chiffre[$centaine] . ' cent ' . $this->chiffre[$unite]) . ' ';
        } //comme 750
        elseif ($unite == 0 and $dizaine <> 0) {
            #recupÃ©ration des dizaines
            $nombre = substr($nombre, 1);
            //echo '<br />nombre='.$nombre.'<br />';
            if ($centaine == 1) {
                $this->enLettre .= 'cent ';
                $this->Dizaine(0, $nombre, $unite, $dizaine) . ' ';
            } elseif ($centaine <> 1) {
                $this->enLettre .= $this->chiffre[$centaine] . ' cent ';
                $this->Dizaine(0, $nombre, $unite, $dizaine) . ' ';
            }
        } #comme 695
        elseif ($unite <> 0 and $dizaine <> 0) {
            $nombre = substr($nombre, 1);

            if ($centaine == 1) {
                $this->enLettre .= 'cent ';
                $this->Dizaine(0, $nombre, $unite, $dizaine) . ' ';
            } elseif ($centaine <> 1) {
                $this->enLettre .= ($this->chiffre[$centaine] . ' cent ');
                $this->Dizaine(0, $nombre, $unite, $dizaine) . ' ';
            }
        }
    }

    #Gestion des dizaines

    function Dizaine($inmillier, $nombre, $unite, $dizaine)
    {
        $unite = substr($nombre, 1);
        $dizaine = substr($nombre, 0, 1);

        #comme 70
        if ($unite == 0) {
            $val = $dizaine . '0';
            $this->enLettre .= $this->chiffre[$val];
            if ($inmillier == 0 && $val == 80) {
                $this->enLettre .= 's ';
            }
            $this->enLettre .= ' ';
        } #comme 71
        elseif ($unite <> 0)
            #dizaine different de 9
            if ($dizaine <> 9 and $dizaine <> 7) {
                if ($dizaine == 1) {
                    $val = $dizaine . $unite;
                    $this->enLettre .= $this->chiffre[$val] . ' ';
                } else {
                    $val = $dizaine . '0';
                    if ($unite == 1 && $dizaine <> 8) {
                        $this->enLettre .= ($this->chiffre[$val] . ' et ' . $this->chiffre[$unite]) . ' ';
                    } else {
                        $this->enLettre .= ($this->chiffre[$val] . '-' . $this->chiffre[$unite]) . ' ';
                    }
                }
            } #dizaine =9
            elseif ($dizaine == 9)
                $this->enLettre .= ($this->chiffre[80] . '-' . $this->chiffre['1' . $unite]) . ' ';
            elseif ($dizaine == 7) {
                if ($unite == 1) {
                    $this->enLettre .= ($this->chiffre[60] . ' et ' . $this->chiffre['1' . $unite]) . ' ';
                } else {
                    $this->enLettre .= ($this->chiffre[60] . '-' . $this->chiffre['1' . $unite]) . ' ';
                }
            }
    }

    #Gestion des unitÃ©s

    function Unite($unite)
    {
        $this->enLettre .= ($this->chiffre[$unite]) . ' ';
    }

    //}
}
