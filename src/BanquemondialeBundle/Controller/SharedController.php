<?php
namespace BanquemondialeBundle\Controller;
use BanquemondialeBundle\Entity\CollectionPieceJointe;
use BanquemondialeBundle\Entity\DocumentCollected;
use BanquemondialeBundle\Entity\DossierDemande;
use BanquemondialeBundle\Entity\Quittance;
use BanquemondialeBundle\Entity\Rccm;
use BanquemondialeBundle\Entity\Representant;
use BanquemondialeBundle\Form\CapitalSocialType;
use BanquemondialeBundle\Form\DossierChefGreffeSearchType;
use BanquemondialeBundle\Form\DossierDemandeDepotType;
use BanquemondialeBundle\Form\DossierDemandeSearchType;
use BanquemondialeBundle\Form\DossierDemandeType;
use BanquemondialeBundle\Form\DossierPoleSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
//use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Description of SharedController
 *
 * @author fgueye
 */
class SharedController extends Controller {
    public function indexAction() {
        $message = '';
        $creationdemande = new DossierDemande();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $secondVerification = null;
        $thirdVerification=null;
        $isNomCommercialReserved=false;
        $user = $this->container->get('security.context')->getToken()->getUser();
        //$idLangue=$em->getRepository('BanquemondialeBundle:Langue')->findById($codLang);
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idl = $langue->getId();
        $pays = $em->getRepository('BanquemondialeBundle:Pays')->findOneBy(array('residence' => true));
        $form = $this->createForm(new DossierDemandeType(array('idP' => $user->getPole()->getId(), 'sgleP' => $user->getPole()->getSigle(), 'langue' => $langue, 'lpays' => $pays, 
            'typOpTraduit' => null, 'formeJTraduit' => null, 'secteurTraduit' => null, 'secteurTraduit2' => null, 'secteurTraduit3' => null,
            'categorieTraduit' => null, 'paysTraduit' => null,'pref'=>null,'sousP'=>null)), $creationdemande);
        if ($request->getMethod() == 'POST') {
            $denominationSociale=$request->request->get('banquemondialebundle_dossierDemande')['denominationSociale'];
           // die(dump($denominationSociale));
            $firstVefication = $this->get('monservices')->verificationNomCommercial($request,$denominationSociale);
            $thirdVerification = $this->get('monservices')->verificationNomCommercialDossierDemande($request,$denominationSociale);
            $fouthVerification = $this->get('monservices')->verificationNomCommercialArchiveNomCommerciale($request,$denominationSociale);
            if ($firstVefication == true||$thirdVerification==true||$fouthVerification==true) {
                $secondVerification = true;
                $message='Désolé ce nom commercial est déjà  en utilisation';
                $this->get('session')->getFlashBag()->add('error', $message);
                return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/index.html.twig', array('form' => $form->createView(), 'message' => '','isNomCommercialReserved'=>$isNomCommercialReserved));

            }
            else {
                $secondVerification = $this->get('monservices')->verificationNomCommercialReservation($request,$denominationSociale);
                if ($secondVerification) {
                    $isNomCommercialReserved=true;
                    $message='Désolé ce nom commercial est déjà  réserve';
                    $this->get('session')->getFlashBag()->add('error', $message);
                    $reservation=$em->getRepository('DefaultBundle\Entity\reservation')->findOneBy(['nomCommercial'=>$denominationSociale,'statut'=>true]);
                    return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/index.html.twig', array('form' => $form->createView(), 'message' => '','isNomCommercialReserved'=>$isNomCommercialReserved,'reservation'=>$reservation));
                }
            }

            $form->bind($request);
            $denomExist = $em->getRepository('ParametrageBundle:ArchiveNomCommerciaux')->findByDenominationSociale($creationdemande->getDenominationSociale());
            if ($denomExist) {
                $translated = $this->get('translator')->trans('denomination_exist');
                $form->get('denominationSociale')->addError(new FormError($translated));
            }
            $nomComExist = $em->getRepository('ParametrageBundle:ArchiveNomCommerciaux')->findByDenominationSociale($creationdemande->getNomCommercial());
            if ($nomComExist) {
                $translated = $this->get('translator')->trans('nom_commercial_exist');
                $form->get('nomCommercial')->addError(new FormError($translated));
            }
            if ($form->isValid()) {
                $creationdemande->setUtilisateur($user);
                $creationdemande->setDateCreation(new  \DateTime());
                $em->persist($creationdemande);
                $em->flush();
                return new RedirectResponse($this->container->get('router')->generate('representant_listerrepresentant', array('id' => 0, 'idd' => $creationdemande->getId())));
            }
        }
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/index.html.twig', array('form' => $form->createView(), 'message' => $message,'isNomCommercialReserved'=>$isNomCommercialReserved));
    }
    public function depotAction() {
        $message = '';
        $creationdemande = new DossierDemande();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $idAguipe = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle('AGUIPE');

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idl = $langue->getId();
        $date = new \DateTime();
        $secondVerification = null;
        $thirdVerification=null;
        $isNomCommercialReserved=false;
        $form = $this->createForm(new DossierDemandeDepotType(array('langue' => $langue, 'typOpTraduit' => null, 'formeJTraduit' => null)), $creationdemande);
        if ($request->getMethod() == 'POST') {
            $denominationSociale=$request->request->get('banquemondialebundle_dossierDemandeDepot')['denominationSociale'];
            $firstVefication = $this->get('monservices')->verificationNomCommercial($request,$denominationSociale);
            $thirdVerification = $this->get('monservices')->verificationNomCommercialDossierDemande($request,$denominationSociale);
            $fouthVerification = $this->get('monservices')->verificationNomCommercialArchiveNomCommerciale($request,$denominationSociale);

            if ($firstVefication == true||$thirdVerification==true||$fouthVerification==true) {
                $secondVerification = true;
                $message='Désolé ce nom commercial est déjà  en utilisation';
                $this->get('session')->getFlashBag()->add('error', $message);
                return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/depot.html.twig', array('form' => $form->createView(), 'message' => '','isNomCommercialReserved'=>$isNomCommercialReserved));
            }
            else {
                $secondVerification = $this->get('monservices')->verificationNomCommercialReservation($request,$denominationSociale);
                if ($secondVerification) {
                    $isNomCommercialReserved=true;
                    $message='Désolé ce nom commercial est déjà  réserve';
                    $this->get('session')->getFlashBag()->add('error', $message);
                    $reservation=$em->getRepository('DefaultBundle\Entity\reservation')->findOneBy(['nomCommercial'=>$denominationSociale,'statut'=>true]);
                    return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/depot.html.twig', array('form' => $form->createView(), 'message' => '','isNomCommercialReserved'=>$isNomCommercialReserved,'reservation'=>$reservation));
                }
            }
            $profilSaisi = $em->getRepository('UtilisateursBundle:Profile')->findOneByDescription('saisi');
            if ($profilSaisi) {
                $firstUserAgentSaisi = $em->getRepository('UtilisateursBundle:Utilisateurs')->findOneBy(array('profile' => $profilSaisi->getId(), 'pole' => $user->getPole()->getId(), 'entreprise' => $user->getEntreprise()->getId()));
                if (!$firstUserAgentSaisi) {
                    $message = $this->get('translator')->trans("aucun_agent_saisi_defini_pour_structure");

                    return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/depot.html.twig', array('form' => $form->createView(), 'message' => $message));
                }
            }
            $isAguipe = $request->get('isAguipe');
            $form->bind($request);
            if ($form->isValid()) {
                $creationdemande->setUtilisateurDepot($user);
                $creationdemande->setStatut(-1);
                $creationdemande->setEnActivite(false);
                $creationdemande->setDateCreation($date);
                $pays = $em->getRepository('BanquemondialeBundle:Pays')->findOneByResidence(true);
                $creationdemande->setPays($pays);
                $em->persist($creationdemande);
                $em->flush();
                $numeroDossier = 'GU-CE-' . strtoupper($creationdemande->getPays()->getCode()) . sprintf("%08d", $creationdemande->getId());
                $creationdemande->setNumeroDossier($numeroDossier);
                $em->flush();
                //gerer le dirigeant
                $representant = new Representant();
                $representant->setNom($request->get('nom'));
                $representant->setPrenom($request->get('prenom'));
                // $representant->setDateDeNaissance(new \DateTime($request->get('dateNaissance')));
                $representant->setDateDeNaissance(new \DateTime(date_format(new \DateTime($request->get('dateNaissance')), 'd-m-Y')));
                $representant->setAdresse($request->get('adresse'));
                $representant->setTelephone($request->get('telephone'));
                $representant->setDossierDemande($creationdemande);
                $representant->setGp(true);
                $em->persist($representant);
                $email = $request->get('banquemondialebundle_dossierDemandeDepot')['email'];
                $phoneNumber = $request->get('telephone');
                $em->flush();
                //fin gestion dirigeant
                //gerer document collected
                $idDossier = $creationdemande->getId();
                //die(dump($idDossier));
                $typeOperation = $creationdemande->getTypeOperation();
                $idTypeOp = $typeOperation->getId();
                $formeJuridique = $creationdemande->getFormeJuridique();
                $idFormeJ = $formeJuridique->getId();
                //die(dump($idFormeJ));
                $listDocumentForCollected = $em->getRepository('BanquemondialeBundle:Circuit')->findByTypeOpAndFormeJurique($idTypeOp, $idFormeJ, $creationdemande->getTypeDossier()->getId());
                foreach ($listDocumentForCollected as $collected) {
                    if (($idAguipe == $collected->getPole()) && ($isAguipe == null)) {

                    } else {
                        $docCollected = new DocumentCollected();
                        $docCollected->setPole($collected->getPole());
                        $docCollected->setOrdre($collected->getOrdre());
                        $docCollected->setDossierDemande($creationdemande);
                        $em->persist($docCollected);
                        $em->flush();
                    }
                }
                //mettre le dossier en cors caisse
                $poleCaisse = $em->getRepository('ParametrageBundle:Pole')->getPoleCaisse();
                $documentCaisse = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('dossierDemande' => $idDossier, 'pole' => $poleCaisse->getId()));
                $statutEncours = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(1);
                //die(dump($documentCaisse));
                if ($documentCaisse) {
                    $documentCaisse->setStatutTraitement($statutEncours);
                    $documentCaisse->setDateSoumission($date);
                    $em->persist($documentCaisse);
                    $em->flush();
                    $this->ajoutQuittance($idDossier);
                }
                $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
                $cheminUpload = $chemin->getNom();
                $temp = $cheminUpload . $idDossier . '\\';
                if (!is_dir($temp)) {
                    mkdir($temp);
                }
                $translated =  $this->get('translator')->trans('depot.message_ajouter');
                $this->get('session')->getFlashBag()->add('info', $translated);
                //$documentCaisse->set
                //fin mise a jour caisse

                //// Envoi du SMS////////////////
                $this->get('monServices')->SmsOrange($this->get('monServices')->formatPhoneNumber($phoneNumber), $representant, 'depot');
                //// Envoi de l'Email ////////////////
                $this->get('monServices')->EnvoiMessage($representant, $email, 'depot');
                return new RedirectResponse($this->container->get('router')->generate('lister_depot', array('idd' => 0)));
                //return new RedirectResponse($this->container->get('router')->generate('representant_listerrepresentant', array('id' => 0, 'idd' => $creationdemande->getId())));
            }
        }
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/depot.html.twig', array('form' => $form->createView(), 'message' => $message,'isNomCommercialReserved'=>$isNomCommercialReserved));
        // return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/depot.html.twig', array('form' => $form->createView(), 'message' => $message));
    }
    public function editDossierAction($idd) {
        $em = $this->getDoctrine()->getManager();
        //$user = $this->container->get('security.context')->getToken()->getUser();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $profilName = "";
        if ($user->getProfile()) {
            $profil = $user->getProfile()->getDescription();
            $profilName = $profil;
        }


        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $creationdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $ssRequis = false;
        if ($profilName == "saisi") {
            $typeDossier = $creationdemande->getTypeDossier()->getLibelle();
            if ($typeDossier == "Notaire") {
                $ssRequis = true;
            }
        }
        $isAguipe = true;
        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findBySigle("AGUIPE");
        $docAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
        if ($docAguipe) {
            $isAguipe = true;
        } else {
            $isAguipe = false;
        }
        // $user = $this->container->get('security.context')->getToken()->getUser();
        //vérification du droit d'accès au dossier

        if ($creationdemande == null or ( $creationdemande->getStatut() != null && ($creationdemande->getStatut() != 3 )) or $creationdemande->getUtilisateur() != $user) {
            $translated = $this->get('translator')->trans('acces_non_autorise');
            $this->get('session')->getFlashBag()->add('error', $translated);
            return $this->redirectToRoute('administration');
        }
        $tyOpeTradut = $em->getRepository('BanquemondialeBundle:TypeOperationTraduction')->findOneBy(array('langue' => $langue, 'typeOperation' => $creationdemande->getTypeOperation()->getId()));
        $formeJTraduit = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('langue' => $langue, 'formeJuridique' => $creationdemande->getFormeJuridique()->getId()));
        $secteurTraduit = null;
        
          //$categorieT1=null;
          $idc=null;
        if ($creationdemande->getSecteurActivite()) {
            $secteurTraduit = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue, 'secteurActivite' => $creationdemande->getSecteurActivite()->getId()));          
        }
        $categorieT=null;
        if ($creationdemande->getCategorie()) {
            $categorieT = $em->getRepository('ParametrageBundle:CategorieActiviteTraduction')->findOneBy(array('langue' => $langue, 'categorieActivite' => $creationdemande->getCategorie()));
            
        }
        //die(dump($secteurTraduit));
       //$creationdemande->setCategorie($categorieT1);
        $secteurTraduit2 = null;
        if ($creationdemande->getActiviteSecondaire()) {
            $secteurTraduit2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue, 'secteurActivite' => $creationdemande->getActiviteSecondaire()->getId()));
            
        }
        $secteurTraduit3 = null;
        if ($creationdemande->getActiviteSecondaire2()) {
            $secteurTraduit3 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue, 'secteurActivite' => $creationdemande->getActiviteSecondaire2()->getId()));
        }
        $paysTraduit = null;
        if ($creationdemande->getPays()) {
            $paysTraduit = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('langue' => $langue, 'pays' => $creationdemande->getPays()->getId()));
        }
        $pref=$creationdemande->getPrefecture();
        $sp=$creationdemande->getSousPrefecture();
        //$qrtier=$creationdemande->getQuartierCodifie();
       // $qrtier2=$em->getRepository('BanquemondialeBundle:Quartier')->findOneBy(array('id'=>$qrtier->getId(),'sousPrefecture'=>$sp->getId()));
        //die(dump($qrtier2));
        $pays = $em->getRepository('BanquemondialeBundle:Pays')->findOneBy(array('residence' => true));
        $form = $this->createForm(new DossierDemandeType(array('idP' => $user->getPole()->getId(), 'sgleP' => $user->getPole()->getSigle(), 'langue' => $langue,
            'lpays' => $pays, 'typOpTraduit' => $tyOpeTradut, 'formeJTraduit' => $formeJTraduit, 'secteurTraduit' => $secteurTraduit, 'secteurTraduit2' => $secteurTraduit2, 'secteurTraduit3' => $secteurTraduit3, 'categorieTraduit' => $categorieT,
            'paysTraduit' => $paysTraduit,'pref'=>$pref,'sousP'=>$sp)), $creationdemande);
        $formeJuridiqueId = $creationdemande->getFormeJuridique()->getId();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            //die(dump($creationdemande));
            if ($form->isSubmitted()) {
                //$creationdemande->setUtilisateur($user);
                /* if(profilName=="saisi")
                  {
                  $creationdemande->set
                  } */
                $em->persist($creationdemande);
                $em->flush();
//die(dump($formeJuridiqueId));
                $rteSuivant = $this->getNextEtapeRoute($formeJuridiqueId, $isAguipe);

                return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('id' => 0, 'idd' => $creationdemande->getId())));
            } else {
                //die(dump($form->getErrorsAsString()));
            }
        }
       // die(dump($form->get('formeJuridique')->getData()->getId()));
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/editer.html.twig', array('form' => $form->createView(), 'idd' => $idd,
                    'dd' => $creationdemande, 'profilName' => $profilName, 'ssRequis' => $ssRequis,'selectFormJurique'=>$form->get('formeJuridique')->getData()->getId()));
    }
    public function listDossierEnCoursAction($idd = null) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $profilName = "";
        if ($user->getProfile()) {
            $profil = $user->getProfile()->getDescription();
            $profilName = $profil;
        }



        $data = NULL;
        $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDemandeDossierByParametres(null, $idLangue, $user->getId(), 25);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['dossierEncours'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDemandeDossierByParametres($data, $idLangue, $user->getId(), null);
        }

        //$form = $this->container->get('form.factory')->create(new DossierDemandeSearchType(), array('langue' => $langue));
        $form = $this->createForm(new DossierDemandeSearchType(array('langue' => $langue)));

        $form->bind($request);

        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/dossierEncours.html.twig', array('form' => $form->createView(),
                    'listerdemande' => $listerdemande, 'langue' => $idLangue, 'profilName' => $profilName));
    }
    public function listerDossierDiasporaAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $profilName = "";
        if ($user->getProfile()) {
            $profil = $user->getProfile()->getDescription();
            $profilName = $profil;
        }
        $data = NULL;
     //   $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDemandeDiasporaByParametres(null, $idLangue, 25);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['dossierEncours'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDemandeDiasporaByParametres($data, $idLangue);
        }

        //$form = $this->container->get('form.factory')->create(new DossierDemandeSearchType(), array('langue' => $langue));
        $form = $this->createForm(new DossierDemandeSearchType(array('langue' => $langue)));

        $form->bind($request);

        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/dossierDiaspora.html.twig', array('form' => $form->createView(),
                    'listerdemande' => $listerdemande, 'langue' => $idLangue, 'profilName' => $profilName));
    }
    public function listDossierDepotAction($idd = null) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $dateJour=0;//date_format(new \DateTime(),'Y-m-d');
        $data=[
            'numeroDossier'=>0,
            'denominationSociale'=>0,
            'dateCreationDebut'=>$dateJour,
            'dateCreationFin'=>$dateJour,
            'formeJuridique'=>0
        ];
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDemandeDossierDeposesByParametres(null, $idLangue, $user->getId(), 25, -1);
        if ($request->getMethod() == 'POST') {
            $dataTemp = $request->request->all()['dossierEncours'];
            $data=[
                'numeroDossier'=>empty($dataTemp['numeroDossier'])?0:$dataTemp['numeroDossier'],
                'denominationSociale'=>empty($dataTemp['denominationSociale'])?0:$dataTemp['denominationSociale'],
                'dateCreationDebut'=>empty($dataTemp['dateCreationDebut'])?$dateJour:$dataTemp['dateCreationDebut'],
                'dateCreationFin'=>empty($dataTemp['dateCreationFin'])?$dateJour:$dataTemp['dateCreationFin'],
                'formeJuridique'=>empty($dataTemp['formeJuridique'])?0:$dataTemp['formeJuridique']
            ];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDemandeDossierDeposesByParametres($data, $idLangue, $user->getId(), null, -1);
        }
        //$form = $this->container->get('form.factory')->create(new DossierDemandeSearchType(), array('langue' => $langue));
        $form = $this->createForm(new DossierDemandeSearchType(array('langue' => $langue)));
        $form->bind($request);
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/dossierDepot.html.twig', array(
            'form' => $form->createView(),
            'listerdemande' => $listerdemande, 'langue' => $idLangue,
            'data'=>$data
        ));
    }
    public function listDossierDepotModificationAction($idd = null) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $data = NULL;
        $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDemandeDossierDepotByParametres(null, $idLangue, $user->getId(), 25, -2);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['dossierEncours'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDemandeDossierDepotByParametres($data, $idLangue, $user->getId(), null, -2);
        }

        //$form = $this->container->get('form.factory')->create(new DossierDemandeSearchType(), array('langue' => $langue));
        $form = $this->createForm(new DossierDemandeSearchType(array('langue' => $langue)));

        $form->bind($request);

        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/dossierDepotModification.html.twig', array('form' => $form->createView(), 'listerdemande' => $listerdemande, 'langue' => $idLangue));
    }
    public function listDossierRetraitAction($idd = null) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $idS = $user->getEntreprise()->getId();
        $em = $this->getDoctrine()->getManager();

        $data = $this->getRequest()->request->get('data');
        $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $pole = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle("GF");
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }

        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPoleRetrait($user, null, $idCodeLangue, $idPole, 25, 2, $idS);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['dossiersPole'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPoleRetrait($user, $data, $idCodeLangue, $idPole, null, 2, $idS);
        }

        $form = $this->createForm(new DossierPoleSearchType(array('langue' => $langue)));

        $form->bind($request);


        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/dossierRetrait.html.twig', array('form' => $form->createView(), 'listerdemande' => $listerdemande, 'langue' => $idCodeLangue, 'idp' => $idPole, 'idS' => 2, 'statutRetrait' => 1));
    }
    public function listDossierRetraitPartielAction($idd = null) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $idS = $user->getEntreprise()->getId();
        $em = $this->getDoctrine()->getManager();

        $data = $this->getRequest()->request->get('data');
        $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $pole = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle("GF");
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }

        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPartielPoleRetrait($user, null, $idCodeLangue, $idPole, 25, 2, $idS);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['dossiersPole'];
           // var_dump($data);die();
            $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPartielPoleRetrait($user, $data, $idCodeLangue, $idPole, null, 2, $idS);
        }

        $form = $this->createForm(new DossierPoleSearchType(array('langue' => $langue)));

        $form->bind($request);

        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/dossierRetraitPartiel.html.twig', array('form' => $form->createView(), 'listerdemande' => $listerdemande, 'langue' => $idCodeLangue, 'idp' => $idPole, 'idS' => 2, 'statutRetrait' => 2));
    }
    public function listDossierRetirerAction($idd = null) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $idS = $user->getEntreprise()->getId();
        $em = $this->getDoctrine()->getManager();

        $dateJour=0;
        $data=[
            'numeroDossier'=>0,
            'denominationSociale'=>0,
            'dateCreationDebut'=>$dateJour,
            'dateCreationFin'=>$dateJour,
            'formeJuridique'=>0,
            'typeDossier'=>0,
            'entreprise'=>0,
            'gerant'=>0,
        ];
        $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $pole = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle("GF");
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }

        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPoleRetire($user, null, $idCodeLangue, $idPole, 25, 2, $idS);
        //die(dump($idPole));
        if ($request->getMethod() == 'POST') {
            $dataTemp = $request->request->all()['dossiersPole'];
            $data=[
                'numeroDossier'=>empty($dataTemp['numeroDossier'])?0:$dataTemp['numeroDossier'],
                'denominationSociale'=>empty($dataTemp['denominationSociale'])?0:$dataTemp['denominationSociale'],
                'dateCreationDebut'=>empty($dataTemp['dateCreationDebut'])?$dateJour:$dataTemp['dateCreationDebut'],
                'dateCreationFin'=>empty($dataTemp['dateCreationFin'])?$dateJour:$dataTemp['dateCreationFin'],
                'formeJuridique'=>empty($dataTemp['formeJuridique'])?0:$dataTemp['formeJuridique'],
                'typeDossier'=>empty($dataTemp['typeDossier'])?0:$dataTemp['typeDossier'],
                'entreprise'=>empty($dataTemp['entreprise'])?0:$dataTemp['entreprise'],
                'gerant'=>empty($dataTemp['gerant'])?0:$dataTemp['gerant'],
            ];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPoleRetire($user, $data, $idCodeLangue, $idPole, null, 2, $idS);
        }

        $form = $this->createForm(new DossierPoleSearchType(array('langue' => $langue)));

        $form->bind($request);

        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/dossierRetirer.html.twig', array(
            'form' => $form->createView(),
            'listerdemande' => $listerdemande,
            'langue' => $idCodeLangue,
            'idp' => $idPole, 'idS' => 2,
            'statutRetrait' => 3,
            'data'=>$data
        ));
    }
    public function editDepotAction($idd) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $message = "";
        $motif = "";
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $user = $this->container->get('security.context')->getToken()->getUser();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $typeOpTraduit = $em->getRepository('BanquemondialeBundle:TypeOperationTraduction')->findOneBy(array('langue' => $langue, 'typeOperation' => $dossierDemande->getTypeOperation()->getId()));
        $formeJTraduit = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('langue' => $langue, 'formeJuridique' => $dossierDemande->getFormeJuridique()->getId()));

        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle('AGUIPE');
        $representant = $em->getRepository('BanquemondialeBundle:Representant')->findOneBy(array('dossierDemande' => $idd));
        $existDocumentAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
        $aguipeExit = false;
        if ($existDocumentAguipe) {
            $aguipeExit = true;			
        }
		
					
					
        $poleCaisse = $em->getRepository('ParametrageBundle:Pole')->getPoleCaisse();
        $documentCaisse = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('dossierDemande' => $idd, 'pole' => $poleCaisse->getId()));

        $date = new \DateTime();
        $form = $this->createForm(new DossierDemandeDepotType(array('langue' => $langue, 'typOpTraduit' => $typeOpTraduit, 'formeJTraduit' => $formeJTraduit)), $dossierDemande);
        if ($request->getMethod() == 'POST') {
            $isAguipe = $request->get('isAguipe');
            $form->bind($request);
            if ($form->isValid()) {

                //$dossierDemande->setUtilisateur($user);
                $dossierDemande->setUtilisateurDepot($user);
                $dossierDemande->setStatut(-1);
                $pays = $em->getRepository('BanquemondialeBundle:Pays')->findOneByResidence(true);
                $dossierDemande->setPays($pays);
                $em->persist($dossierDemande);
                $em->flush();
                //gerer le dirigeant
                $representant = $em->getRepository('BanquemondialeBundle:Representant')->findOneBy(array('dossierDemande' => $idd));
                if ($representant) {
                    $representant->setNom($request->get('nom'));
                    $representant->setPrenom($request->get('prenom'));
                    $representant->setDateDeNaissance(new \DateTime($request->get('dateNaissance')));
                    $representant->setAdresse($request->get('adresse'));
                    $representant->setTelephone($request->get('telephone'));
                    $representant->setDossierDemande($dossierDemande);
                    $em->persist($representant);
                    $em->flush();
                }
                //fin gestion dirigeant
                //gerer document collected
                //die(dump($idDossier));
                $typeOperation = $dossierDemande->getTypeOperation();
                $idTypeOp = $typeOperation->getId();
                $formeJuridique = $dossierDemande->getFormeJuridique();
                $idFormeJ = $formeJuridique->getId();
                //die(dump($isAguipe));
				
				if(!$isAguipe && $existDocumentAguipe)
				{
					$em->remove($existDocumentAguipe);
                    $em->flush();
				}
				else if($isAguipe && !$existDocumentAguipe)
				{
					$ordreAguipe = $em->getRepository('BanquemondialeBundle:Circuit')->findOrdreAguipe($idTypeOp, $idFormeJ, $dossierDemande->getTypeDossier()->getId());
					$docCollected = new DocumentCollected();
					$docCollected->setPole($poleAguipe);
					$docCollected->setOrdre($ordreAguipe->getOrdre());
					$docCollected->setDossierDemande($dossierDemande);
					$em->persist($docCollected);
					$em->flush();
				}
				
				
                //reinitialisation du circuit
                $listeDocCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findByDossierDemande($idd);
                foreach ($listeDocCollected as $doc) {
                    $doc->setStatutTraitement(NULL);
                    $em->persist($doc);
                    $em->flush();
                }

                //}
				//mettre le dossier en cours caisse
                $statutEncours = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(1);
                //die(dump($documentCaisse));
                if ($documentCaisse) {
                    $documentCaisse->setStatutTraitement($statutEncours);
                    if ($documentCaisse->getDateSoumission() == null) {
                        $documentCaisse->setDateSoumission($date);
                    }


                    $em->persist($documentCaisse);
                    $em->flush();

                    $this->ajoutQuittance($idd);
                }
				//fin mise a jour caisse
                return new RedirectResponse($this->container->get('router')->generate('lister_depot_Modification', array('idd' => 0)));
            }
        }
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/editDepot.html.twig', array('form' => $form->createView(), 'message' => $message,
                    'representant' => $representant, 'aguipeExit' => $aguipeExit, 'docCaisse' => $documentCaisse, 'dossierDemande' => $dossierDemande));
    }
    public function suivreDossierAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        //$listDossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findBy(array('statut' => ' not null', 'utilisateur' => $user->getId()), array('id' => 'desc'));
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $listDossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDossierSuiviByParametres(null, $langue->getId(), $user->getId(), 25, -2);
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['dossierEncours'];
            $listDossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDossierSuiviByParametres($data, $langue->getId(), $user->getId(), null, -2);
        }

        $form = $this->createForm(new DossierDemandeSearchType(array('langue' => $langue)));

        $form->bind($request);


        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/suivreDossier.html.twig', array('listDossier' => $listDossier, 'form' => $form->createView()));
    }
    public function documentCollectedAction($idd = null) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $demande = $em->find('BanquemondialeBundle:DossierDemande', $idd);
        $isAguipe = true;
        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findBySigle("AGUIPE");
        $docAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
        if ($docAguipe) {
            $isAguipe = true;
        } else {
            $isAguipe = false;
        }
        $idFormeJ = $demande->getFormeJuridique()->getId();
        $idTypeOp = $demande->getTypeOperation()->getId();
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang)->getId();
        $listDocumentForCollected = $em->getRepository('BanquemondialeBundle:Circuit')->findDocToBeColled($idTypeOp, $idFormeJ, $demande->getTypeDossier()->getId());
        //Controle su le bouton enregistrer
        $profilName = null;
        $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        if ($user->getProfile()) {
            $profil = $user->getProfile()->getDescription();

            if ($pole == $poleAPIP) {
                if (strpos($profil, 'saisi') !== false) {
                    $profilName = $profil;
                }
            }
        }
//fin

        if (isset($idd)) {  // Recherche des données du dossiers via l'id du Dossier                       
            if ($request->getMethod() == 'POST') {
                if ($profilName) {

                    $rteSuivant = $this->getNextEtapeRoute($idFormeJ, $isAguipe);
                    return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idd)));
                }
                //die(dump($_POST));
                foreach ($_POST['idPole'] as $key => $idpole) {
                    $ordre = $_POST['ordre'][$key];
                    $pole = $em->getRepository('ParametrageBundle:Pole')->find($idpole);
                    if (!isset($_POST['checkbox'][$key])) {
                        $docCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('dossierDemande' => $idd, 'pole' => $idpole));
                        if ($docCollected) {
                            $em->remove($docCollected);
                            $em->flush();
                        }
                    } else {
                        $circuitSave = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('dossierDemande' => $idd, 'pole' => $idpole));
                        if (!$circuitSave) {
                            $docCollected = new DocumentCollected();
                            $docCollected->setPole($pole);
                            $docCollected->setOrdre($ordre);
                            $docCollected->setDossierDemande($demande);
                            $em->persist($docCollected);
                            $em->flush();
                        } else {
                            $circuitSave->setPole($pole);
                            $circuitSave->setOrdre($ordre);
                            $circuitSave->setDossierDemande($demande);
                            $em->persist($circuitSave);
                            $em->flush();
                        }
                    }
                }

                $rteSuivant = $this->getNextEtapeRoute($idFormeJ, $isAguipe);
                return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idd)));

                //return new RedirectResponse($this->container->get('router')->generate('pieceJointe', array('idd' => $idd)));
            }
            $listPoleCocher = $em->getRepository('BanquemondialeBundle:DocumentCollected')->getListePoleCocher($idd);
            $nbreC = Count($listPoleCocher);

            return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/collectDocument.html.twig', array('listDocument' => $listDocumentForCollected, 'idd' => $idd, 'listPoleCocher' => $listPoleCocher, 'nbreCocher' => $nbreC, 'profilName' => $profilName));
        }
    }
    public function retraitDossierAction($idd = null) {
        $postUrl='retraitDossier';
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $demande = $em->find('BanquemondialeBundle:DossierDemande', $idd);
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang)->getId();
        $listDocumentForCollected = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findByDossierDemande($idd);
        $listDocumentNotCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDocumentsNonRetires($idd);
        $representant = $em->getRepository('BanquemondialeBundle:Representant')->findByDossierDemande($demande);
        //Controle su le bouton enregistrer
        $user = $this->container->get('security.context')->getToken()->getUser();
        $telephone = $request->get('telephone');
//fin
//die(dump($listDocumentForCollected));
        if (isset($idd)) {
            if ($request->getMethod() == 'POST') {
                $phoneNumber = str_replace('-', '', substr($telephone, -12));
                //die(dump($_POST));
                foreach ($_POST['idPole'] as $key => $idpole) {
                    $numero = $_POST['numero'][$key];
                    $pole = $em->getRepository('ParametrageBundle:Pole')->find($idpole);
                    if (isset($_POST['checkbox'][$key])) {
                        $docsCollected = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findBy(array('dossierDemande' => $idd, 'pole' => $idpole));
                        //die(dump($docsCollected));
                        if ($docsCollected) {
                            foreach ($docsCollected as $docCollected) {
                                if (!$docCollected->getDateRetrait()) {
                                    $docCollected->setBeneficiaire($_POST['beneficiaire']);
                                    $docCollected->setTelephone($_POST['telephone']);
                                    $docCollected->setUtilisateurRetrait($user);
                                    $docCollected->setEstRetire(true);
                                    $docCollected->setDateRetrait(new \DateTime());
                                    $em->persist($docCollected);
                                    $em->flush();
                                }
                            }
                        }
                        //> Mise a jour statut Retrait l'ors d'un retrait
                        $demande->setStatusRetrait(true);
                        $em->persist($demande);
                        $em->flush();
                        $this->get('suivistatutdossierservice')->getAndsetStatRetrait('set',$idd,null,null);
                    }
                }
                //// Envoi du SMS////////////////
                $this->get('monServices')->SmsOrange($phoneNumber, $representant[0], 'retrait');
                //// Envoi de l'Email ////////////////
                ///
//                if (!empty($representant[0]->getEmail())) {
//                    $this->get('monServices')->EnvoiMessage($representant[0], $representant[0]->getEmail(), 'retrait');
//                }
				$listDocumentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDocumentsRetires($idd);
				$listDocumentNotCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDocumentsCreesNonRetires($idd);
				//die(dump(count($listDocumentCollected)." et ".count($listDocumentNotCollected)));
				if(count($listDocumentCollected) > 0 and count($listDocumentNotCollected) > 0)
				{
					$translated =  $this->get('translator')->trans("documents_partiellement_retire");
					$this->get('session')->getFlashBag()->add('info', $translated);
					return $this->redirectToRoute('lister_retrait_partiel');//listDossierRetraitPartielAction
				}
				else if(count($listDocumentCollected) > 0 and count($listDocumentNotCollected) == 0)				
				{
					$translated =  $this->get('translator')->trans("documents_completement_retire");
					$this->get('session')->getFlashBag()->add('info', $translated);
					return $this->redirectToRoute('lister_retirer');//listDossierRetirerAction
				}
				else{
					$translated =  $this->get('translator')->trans("quittance_retire");
					$this->get('session')->getFlashBag()->add('info', $translated);
					return $this->redirectToRoute('lister_retrait');//listDossierRetirerAction
				}
            }
            //$listDocDelivres=$em->getRepository('BanquemondialeBundle:FormulaireDelivre')->getListFormulaireDelivre($idd, $langue);
            return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/retraitDossier.html.twig', array(
                'listDocument' => $listDocumentForCollected,
                'listDocumentNotCollected' => $listDocumentNotCollected,
                'idd' => $idd,
                'statutRetrait' => 1,
                'postUrl'=>$postUrl
            ));
        }
    }
    public function retraitDossierPartielAction($idd = null) {
        $postUrl='retraitDossierPartiel';
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $demande = $em->find('BanquemondialeBundle:DossierDemande', $idd);
        //var_dump($demande->getStatusRetrait());die();
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang)->getId();
        $listDocumentForCollected = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findByDossierDemande($idd);
        $listDocumentNotCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDocumentsNonRetires($idd);
        //Controle su le bouton enregistrer
        $user = $this->container->get('security.context')->getToken()->getUser();
		//fin
		//die(dump($listDocumentForCollected));
        if (isset($idd)) {
            if ($request->getMethod() == 'POST') {
              //  var_dump($demande);	die();
                //die(dump($_POST));
                foreach ($_POST['idPole'] as $key => $idpole) {
                    $numero = $_POST['numero'][$key];
                    $pole = $em->getRepository('ParametrageBundle:Pole')->find($idpole);
                    if (isset($_POST['checkbox'][$key])) {
                        $docsCollected = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findBy(array('dossierDemande' => $idd, 'pole' => $idpole));
                        //die(dump($docsCollected));
                        if ($docsCollected) {

                            foreach ($docsCollected as $docCollected) {
                                if (!$docCollected->getDateRetrait()) {
                                    $docCollected->setBeneficiaire($_POST['beneficiaire']);
                                    $docCollected->setTelephone($_POST['telephone']);
                                    $docCollected->setUtilisateurRetrait($user);
                                    $docCollected->setEstRetire(true);
                                    $docCollected->setDateRetrait(new \DateTime());
                                    $em->persist($docCollected);
                                    $em->flush();
                                }
                            }
                        }

                    }

                }
				$listDocumentNotCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDocumentsCreesNonRetires($idd);
				//die(dump(count($listDocumentCollected)." et ".count($listDocumentNotCollected)));
                $demande->setStatusRetrait(true);
                $em->persist($demande);
                $em->flush();
				if(count($listDocumentNotCollected) > 0)
				{
					$translated =  $this->get('translator')->trans("documents_partiellement_retire");
					$this->get('session')->getFlashBag()->add('info', $translated);
					return $this->redirectToRoute('lister_retrait_partiel');//listDossierRetraitPartielAction
				}
				else if(count($listDocumentNotCollected) == 0)				
				{
					$translated =  $this->get('translator')->trans("documents_completement_retire");
					$this->get('session')->getFlashBag()->add('info', $translated);
					return $this->redirectToRoute('lister_retirer');//listDossierRetirerAction
				}
				else{
					$translated =  $this->get('translator')->trans("documents_partiellement_retire");
					$this->get('session')->getFlashBag()->add('info', $translated);
					return $this->redirectToRoute('lister_retrait_partiel');//listDossierRetirerAction
				}
            }
            //$listDocDelivres=$em->getRepository('BanquemondialeBundle:FormulaireDelivre')->getListFormulaireDelivre($idd, $langue);
            return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/retraitDossier.html.twig', array(
                'listDocument' => $listDocumentForCollected,
                'listDocumentNotCollected' => $listDocumentNotCollected,
                'idd' => $idd, 'statutRetrait' => 2,
                'postUrl'=>$postUrl
            ));
        }
    }
    public function retirerDossierAction($idd = null) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $demande = $em->find('BanquemondialeBundle:DossierDemande', $idd);
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang)->getId();
        $listDocumentForCollected = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findByDossierDemande($idd);
        $listDocumentNotCollected = null; //$em->getRepository('BanquemondialeBundle:DocumentCollected')->findDocumentsNonRetires($idd);
        //Controle su le bouton enregistrer
        $user = $this->container->get('security.context')->getToken()->getUser();
//fin

        if (isset($idd)) {
			/*
            if ($request->getMethod() == 'POST') {

                //die(dump($_POST));
                foreach ($_POST['idPole'] as $key => $idpole) {
                    $numero = $_POST['numero'][$key];
                    $pole = $em->getRepository('ParametrageBundle:Pole')->find($idpole);
                    if (isset($_POST['checkbox'][$key])) {
                        $docsCollected = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findBy(array('dossierDemande' => $idd, 'pole' => $idpole));
                        //die(dump($docsCollected));
                        if ($docsCollected) {

                            foreach ($docsCollected as $docCollected) {
                                if (!$docCollected->getDateRetrait()) {
                                    $docCollected->setBeneficiaire($_POST['beneficiaire']);
                                    $docCollected->setTelephone($_POST['telephone']);
                                    $docCollected->setUtilisateurRetrait($user);
                                    $docCollected->setEstRetire(true);
                                    $docCollected->setDateRetrait(new \DateTime());
                                    $em->persist($docCollected);
                                    $em->flush();
                                }
                            }
                        }
                    }
                }
            }
			
			*/
            //$listDocDelivres=$em->getRepository('BanquemondialeBundle:FormulaireDelivre')->getListFormulaireDelivre($idd, $langue);
            return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/retirerDossier.html.twig', array('listDocument' => $listDocumentForCollected, 'listDocumentNotCollected' => $listDocumentNotCollected, 'idd' => $idd));
        }
    }
    public function capitalSocialAction($idd = null) {
        $message = '';
        $em = $this->getDoctrine()->getManager();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $numeroDossier = $dossierDemande->getNumeroDossier();
        $isAguipe = true;
        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findBySigle("AGUIPE");
        $docAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
        if ($docAguipe) {
            $isAguipe = true;
        } else {
            $isAguipe = false;
        }
        $formeJuridiqueId = $dossierDemande->getFormeJuridique()->getId();
        if (isset($idd)) {  // Recherche des données du dossiers via l'id du Dossier
            $demande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);

            if (!$demande) {

                $message = $this->get('translator')->trans("message_aucun_element_trouve");
            }
        } else {
            $demande = new DossierDemande();
        }
        $form = $this->createForm(new CapitalSocialType(), $demande);
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                //$demande->setCapitalSocial($demande->getNombreAction() * $demande->getValeurNominale());
                if ($demande->getCapitalSocial() == $demande->getApportNumeraire() + $demande->getApportNature()) {
                    $em->persist($demande);
                    $em->flush();
                    $message = $this->get('translator')->trans("message_capital_ajouter_succes");
                    $rteSuivant = $this->getNextEtapeRoute($formeJuridiqueId, $isAguipe);
                    return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idd, 'numeroDossier' => $numeroDossier)));
                } else {
                    $message = $this->get('translator')->trans("message_capital_different_numeraire_nature");
                }
            } else {
                $message = $this->get('translator')->trans($form->getErrorsAsString());
            }
        }

        $formeJTraduit = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('langue' => $langue, 'formeJuridique' => $demande->getFormeJuridique()->getId()));
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/capitalSocial.html.twig', array('form' => $form->createView(), 'message' => $message, 'demande' => $demande, 'formeJTraduit' => $formeJTraduit, 'numeroDossier' => $numeroDossier));
    }
    public function supprimerPieceAction($nomF, $idd) {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $pieceASupprimer = $em->getRepository('BanquemondialeBundle:CollectionPieceJointe')->findOneBy(array('dossierDemande' => $idd, 'pieceName' => $nomF));
        if ($pieceASupprimer) {


            $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
            $cheminUpload = $chemin->getNom();
            $temp = $cheminUpload;
            $temp = $temp . $idd . '\\';
            $temp = $temp . basename($nomF);
            // die(dump($temp));
            unlink($temp);
            if (!file_exists($temp)) {
                $em->remove($pieceASupprimer);
                $em->flush();
            }
            $objet = $this->get('translator')->trans('succes_suppression_piece');
            $this->get('session')->getFlashBag()->add('info', $objet);
        }
        return new RedirectResponse($this->container->get('router')->generate('pieceJointe', array('idd' => $idd)));
    }
    public function pieceJointeAction($idd = null) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $dossierDemande = $em->find('BanquemondialeBundle:DossierDemande', $idd);

        $idFormeJ = $dossierDemande->getFormeJuridique()->getId();
        $idTypeOp = $dossierDemande->getTypeOperation()->getId();
        $codLang = $request->getLocale();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang)->getId();
        $listPieceEntreprise = $em->getRepository('BanquemondialeBundle:PieceJointe')->findByPieceEntreprise($idTypeOp, $idFormeJ, $langue);

        $user = $this->container->get('security.context')->getToken()->getUser();
        $profilName = "";
        if ($user->getProfile()) {
            $profil = $user->getProfile()->getDescription();
            $profilName = $profil;
        }
        $message = "";
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminUpload = $chemin->getNom(); //a modifier par la requete vers la bdd
        if ($listPieceEntreprise) {
            foreach ($listPieceEntreprise as &$pieceEntreprise) {
                $fichierJoint = $em->getRepository('BanquemondialeBundle:CollectionPieceJointe')->findOneBy(array('dossierDemande' => $dossierDemande, 'document' => $pieceEntreprise['idDoc'], 'fonction' => NULL));

                if ($fichierJoint) {
                    $pieceEntreprise['nomPiece'] = $fichierJoint->getPieceName();
                }
            }
        }
        $listPieceAdmin = $em->getRepository('BanquemondialeBundle:PieceJointe')->findPieceAssocie($idd, $idTypeOp, $idFormeJ, $langue);
        if ($listPieceAdmin) {
            foreach ($listPieceAdmin as &$pieceAdmin) {
                $fichierJoint = $em->getRepository('BanquemondialeBundle:CollectionPieceJointe')->findOneBy(array('dossierDemande' => $dossierDemande, 'document' => $pieceAdmin['idDoc'], 'representant' => $pieceAdmin['idAssocie']));

                if ($fichierJoint) {
                    $pieceAdmin['nomPiece'] = $fichierJoint->getPieceName();
                }
            }
        }

        $statutDossier = $dossierDemande->getStatut();

        $tailleMaximum = 50 * 1024 * 1024;
        $extensions = array('.pdf');

        if ($request->getMethod() == 'POST') {
            if ($request->request->has('textAreaModifier')) {
                $motif = $request->get('textAreaModifier');
                if (strlen($motif) <= 255) {
                    $dossierDemande->setMotif($motif);
                    $dossierDemande->setStatut(-2);
                    $em->persist($dossierDemande);
                    $em->flush();
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $quittance=$em->getRepository('BanquemondialeBundle\Entity\Quittance')->findOneBy(['dossierDemande'=>$idd]);
                   // die(dump($quittance));
                    $this->get('monservices')->updatePaiementOrangeWhenUpdateDossier($quittance->getId());
                    $notif = $this->container->get('utilisateurs.notification');
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $message2 = $this->get('translator')->trans('par_la_saisi');
                    //$nomDuPole = $this->get('translator')->trans($pole->getNom());
                    //envoi de la notification
                    $objet = $this->get('translator')->trans('demande_modification_dossier_envoye');
                    $notif->notifier($dossierDemande->getNumeroDossier() . ' ' . $message . ' ' . $message2, $dossierDemande->getUtilisateur(), $objet);
                    if ($dossierDemande->getUtilisateurDepot()) {
                        $notif->notifier($dossierDemande->getNumeroDossier() . ' ' . $message . ' ' . $message2, $dossierDemande->getUtilisateurDepot(), $objet);
                    }
                    //$translated =  $this->get('translator')->trans('depot.message_ajouter');
                    $this->get('session')->getFlashBag()->add('info', $objet);
                    //$documentCaisse->set                
                    //fin mise a jour caisse
                    return new RedirectResponse($this->container->get('router')->generate('suivreDossier', array('idd' => 0)));
                }
            }
            else {
                if ($_FILES) {
                    //verification taille
                    foreach ($_FILES['file']['tmp_name'] as $key => $tmp_name) {
                        $fileSize = $_FILES['file']['size'][$key];

                        if ($fileSize > $tailleMaximum) {
                            $message = $this->get('translator')->trans('message_fichier_trop_volumineux');
                            return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/pieceJointe.html.twig', array('listPieceEntreprise' => $listPieceEntreprise, 'listPieceAdmin' => $listPieceAdmin, 'idd' => $idd, 'dd' => $dossierDemande, 'message' => $message, 'statutDossier' => $statutDossier, 'cheminUpload' => $cheminUpload, 'profilName' => $profilName));
                        }
                    }
                    //verification type fichier par nom
                    foreach ($_FILES['file']['name'] as $key => $name) {
                        if ($_FILES['file']['name'][$key] != NULL) {
                            if (!empty($_FILES['file']['name'][$key]) && !(mb_strtolower(strrchr($_FILES['file']['name'][$key], '.')) == ".pdf")) {
                                $message = $this->get('translator')->trans('message_fichier_format_non_pdf');
                                return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/pieceJointe.html.twig', array('listPieceEntreprise' => $listPieceEntreprise, 'listPieceAdmin' => $listPieceAdmin, 'idd' => $idd, 'dd' => $dossierDemande, 'message' => $message, 'statutDossier' => $statutDossier, 'cheminUpload' => $cheminUpload, 'profilName' => $profilName));
                            }
                        }
                    }
                    //verification type fichier par navigateur info
                    //verification format
                    foreach ($_FILES['file']['type'] as $key => $type) {
                        if ($_FILES['file']['type'][$key] != NULL) {
                            if (!empty($_FILES['file']['type'][$key]) && !($_FILES['file']['type'][$key] == "application/pdf")) {
                                $message = $this->get('translator')->trans('message_fichier_format_non_pdf');
                                return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/pieceJointe.html.twig', array('listPieceEntreprise' => $listPieceEntreprise, 'listPieceAdmin' => $listPieceAdmin, 'idd' => $idd, 'dd' => $dossierDemande, 'message' => $message, 'statutDossier' => $statutDossier, 'cheminUpload' => $cheminUpload, 'profilName' => $profilName));
                            }
                        }
                    }

                    $count = 0;

                    foreach ($_FILES['file']['name'] as $filename) {
                        $fonction = NULL;
                        $collectionPieceJointeDB = NULL;

                        $temp = $cheminUpload;
                        $tmp = $_FILES['file']['tmp_name'][$count];
                        $count = $count + 1;

                        $idDoc = $request->request->get('idDoc' . $count);
                        $idFct = $request->request->get('idFct' . $count);
                        $idAssocie = $request->request->get('idAssocie' . $count);
                        $temp = $temp . $idd . '\\';
                        if (!is_dir($temp) && $tmp) {
                            mkdir($temp);
                        }
                        if ($idAssocie) {
                            $filename = $idd . '_' . $idDoc . '_' . $idAssocie . '.pdf';
                        } else {
                            $filename = $idd . '_' . $idDoc . '.pdf';
                        }
                        $temp = $temp . basename($filename);
                        move_uploaded_file($tmp, $temp);

                        if ($tmp) {
                            $collectionPieceJointe = new CollectionPieceJointe();
                            $collectionPieceJointe->setDossierDemande($dossierDemande);
                            $date = new \DateTime();
                            $collectionPieceJointe->setDateUpload($date);
                            $collectionPieceJointe->setPieceName($filename);
                            $collectionPieceJointe->setDocument($em->find('BanquemondialeBundle:Document', $idDoc));

                            if ($idFct) {
                                $fonction = $em->find('BanquemondialeBundle:Fonction', $idFct);
                                $collectionPieceJointe->setFonction($fonction);
                            }

                            if ($idAssocie) {
                                $representant = $em->find('BanquemondialeBundle:Representant', $idAssocie);
                                $collectionPieceJointe->setRepresentant($representant);
                            }

                            $document = $em->find('BanquemondialeBundle:Document', $idDoc);



                            if ($fonction && $document) {
                                $collectionPieceJointeDB = $em->getRepository('BanquemondialeBundle:CollectionPieceJointe')->findOneBy(array('dossierDemande' => $dossierDemande, 'document' => $document, 'fonction' => $fonction, 'representant' => $idAssocie));
                            } else if (!$fonction && $document) {
                                $collectionPieceJointeDB = $em->getRepository('BanquemondialeBundle:CollectionPieceJointe')->findOneBy(array('dossierDemande' => $dossierDemande, 'document' => $document));
                            }


                            if ($collectionPieceJointeDB) {
                                $collectionPieceJointeDB->getId();
                                $collectionPieceJointeDB->setDateUpload($date);
                                $collectionPieceJointeDB->setPieceName($filename);
                                $em->persist($collectionPieceJointeDB);
                            }

                            if (!$collectionPieceJointeDB) {
                                $em->persist($collectionPieceJointe);
                            }


                            $em->flush();
                        }

                        $temp = '';
                        $tmp = '';
                    }
                }
                $this->get('suivistatutdossierservice')->getAndsetStatSaisie('set',$dossierDemande->getId(),null,null);
                return new RedirectResponse($this->container->get('router')->generate('pieceJointe', array('idd' => $idd)));
            }
        }
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/pieceJointe.html.twig',
            array('listPieceEntreprise' => $listPieceEntreprise, 'idd' => $idd,
                'dd' => $dossierDemande,
                'message' => $message,
                'listPieceAdmin' => $listPieceAdmin,
                'statutDossier' => $statutDossier,
                'cheminUpload' => $cheminUpload,
                'profilName' => $profilName));
    }
    public function fraisDossierAction($idd = null) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $dossierDemande = $em->find('BanquemondialeBundle:DossierDemande', $idd);
        $formeJuridiqueId = $dossierDemande->getFormeJuridique()->getId();
        $isAguipe = true;
        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle("AGUIPE");
        $docAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
        if ($docAguipe) {
            $isAguipe = true;
        } else {
            $isAguipe = false;
        }
        $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
        //die(dump($dossierDemande->getTypeDossier()->getId()));
        //die(dump($dossierDemande->getFormeJuridique()->getId())); 
        $fraisApip = $em->getRepository('ParametrageBundle:Tarification')->getMontantFraisByPole($dossierDemande->getFormeJuridique()->getId(), $dossierDemande->getTypeDossier()->getId(), $poleAPIP->getId());

//$listFraisDossier = $em->getRepository('ParametrageBundle:Tarification')->findBy(array('typeOperation' => $idTypeOp, 'formeJuridique' => $idFormeJ));
        $listFraisDossier = $em->getRepository('ParametrageBundle:Tarification')->getListeFraisAPayer($idd);

        $total = 0;
        $translated = "";
        foreach ($listFraisDossier as $frais) {
            $montant = $frais->getMontant();
            $total = $total + $montant;
        }
        foreach ($fraisApip as $frais) {
            $montant = $frais->getMontant();
            $total = $total + $montant;
        }
        if ($request->getMethod() == 'POST') {
            $rteSuivant = $this->getNextEtapeRoute($formeJuridiqueId, $isAguipe);
            return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idd)));
        }
        //die(dump($listFraisDossier)); 
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/fraisDossier.html.twig', array('listFraisDossier' => $listFraisDossier, 'fraisAPIP' => $fraisApip, 'total' => $total, 'dd' => $dossierDemande));
    }
    public function setListDocCollected($idd) {
        $em = $this->getDoctrine()->getManager();
        $dossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $docCollectes = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findByDossierDemande($idd);

        if (!$docCollectes) {
            $listDocTobeCollected = $em->getRepository('BanquemondialeBundle:Circuit')->findBy(array('typeOperation' => $dossier->getTypeOperation(), 'formeJuridique' => $dossier->getFormeJuridique()));
            foreach ($listDocTobeCollected as $collected) {
                $docCollected = new DocumentCollected();
                $docCollected->setPole($collected->getPole());
                $docCollected->setOrdre($collected->getOrdre());
                $docCollected->setDossierDemande($dossier);
                $em->persist($docCollected);
                $em->flush();
            }
        }
        //$lesDocCollecte = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findByDossierDemande($idd);
        //return $lesDocCollecte;
    }
    public function uploadAction($idd, Request $request) {
        $request = $this->get('request');
        $extensions = array('.pdf');
        $em = $this->getDoctrine()->getManager();
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminUpload = $chemin->getNom();
        if ($request->getMethod() == 'POST') {

            $count = 0;
            foreach ($_FILES['file']['name'] as $filename) {
                //die(1);
                $extension = strrchr($_FILES['avatar']['name'], '.');
                if (in_array($extension, $extensions)) { //Si l'extension n'est pas dans le tableau
                    $temp = $cheminUpload;

                    $tmp = $_FILES['file']['tmp_name'][$count];
                    $count = $count + 1;

                    $dossier = $request->request->get('numeroDossier' . $count);
                    $temp = $temp . $dossier . '\\';
                    if (!is_dir($temp) && $tmp) {
                        mkdir($temp);
                    }
                    $temp = $temp . basename($filename);
                    move_uploaded_file($tmp, $temp);
                    $temp = '';
                    $tmp = '';
                }
            }
        }
        return new RedirectResponse($this->container->get('router')->generate('pieceJointe', array('idd' => $idd)));
    }
    public function creationDossierPoleAction($idd) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $user = $this->container->get('security.context')->getToken()->getUser();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $creationdossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $idFormeJ = $creationdossier->getFormeJuridique()->getId();
        $pole = $user->getPole();
        /* if ($pole != null) {
          $documents = $creationdossier->getDocumentCollectedPole($pole, $user);
          if (sizeof($documents) != 0) {
          foreach ($documents as $doc) {
          $doc->setUtilisateur($user);
          $em->persist($doc);
          }
          $em->flush();
          } else {
          $translated = $this->get('translator')->trans('acces_inautorise');
          $this->get('session')->getFlashBag()->add('error', $translated);
          return $this->redirectToRoute('administration');
          }
          } */
        $definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $creationdossier->getPays(), 'langue' => $langue));
        $definedTypeOperation = $em->getRepository('BanquemondialeBundle:TypeOperationTraduction')->findOneBy(array('typeOperation' => $creationdossier->getTypeOperation(), 'langue' => $langue));
        $definedFormeJuridiqueTraduction = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('formeJuridique' => $creationdossier->getFormeJuridique(), 'langue' => $langue));
        $secteurTraduit = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue, 'secteurActivite' => $creationdossier->getSecteurActivite()->getId()));
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $nextRte = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextEtapePoleRoute($currentRoute, $idFormeJ);
        if (!$nextRte) {
            $nextRte = 'traiterDossier';
        }
        $routeAvant = $em->getRepository('ParametrageBundle:EtapeCreation')->getEtapePoleRouteBefore($currentRoute, $idFormeJ);
        $motif = $creationdossier->getMotifModification($pole);

        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/creationDossierPole.html.twig', array(
                    'creationdossier' => $creationdossier, 'definedPaysTraduction' => $definedPaysTraduction,
                    'definedTypeOperation' => $definedTypeOperation,
                    'definedFormeJuridiqueTraduction' => $definedFormeJuridiqueTraduction, 'idd' => $idd, 'secteurActivite' => $secteurTraduit, 'rteSuivant' => $nextRte, 'routeAvant' => $routeAvant, 'motif' => $motif
        ));
    }
    public function listDossierPoleAction($data = null, $idS = 1) {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();
        $dateJour=0;
        $data=[
            'numeroDossier'=>0,
            'denominationSociale'=>0,
            'dateCreationDebut'=>$dateJour,
            'dateCreationFin'=>$dateJour,
            'dateDelivranceDebut'=>$dateJour,
            'dateDelivranceFin'=>$dateJour,
            'formeJuridique'=>0,
            'typeDossier'=>0,
            'entreprise'=>0,
            'gerant'=>0,
        ];
        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPole($user, null, $idCodeLangue, $idPole, 25, $idS);
        if ($request->getMethod() == 'POST') {
            $dataTemp = $request->request->all()['dossiersPole'];
            $data=[
                'numeroDossier'=>empty($dataTemp['numeroDossier'])?0:$dataTemp['numeroDossier'],
                'denominationSociale'=>empty($dataTemp['denominationSociale'])?0:$dataTemp['denominationSociale'],
                'dateCreationDebut'=>empty($dataTemp['dateCreationDebut'])?$dateJour:$dataTemp['dateCreationDebut'],
                'dateCreationFin'=>empty($dataTemp['dateCreationFin'])?$dateJour:$dataTemp['dateCreationFin'],
                'dateDelivranceDebut'=>empty($dataTemp['dateDelivranceDebut'])?$dateJour:$dataTemp['dateDelivranceDebut'],
                'dateDelivranceFin'=>empty($dataTemp['dateDelivranceFin'])?$dateJour:$dataTemp['dateDelivranceFin'],
                'formeJuridique'=>empty($dataTemp['formeJuridique'])?0:$dataTemp['formeJuridique'],
                'typeDossier'=>empty($dataTemp['typeDossier'])?0:$dataTemp['typeDossier'],
                'entreprise'=>empty($dataTemp['entreprise'])?0:$dataTemp['entreprise'],
                'gerant'=>empty($dataTemp['gerant'])?0:$dataTemp['gerant'],
            ];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPole($user, $data, $idCodeLangue, $idPole, null, $idS);
        }

        $form = $this->createForm(new DossierPoleSearchType(array('langue' => $langue)));

        $form->bind($request);

        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/dossierPole.html.twig', array(
            'form' => $form->createView(),
            'listerdemande' => $listerdemande,
            'langue' => $idCodeLangue,
            'idp' => $idPole,
            'idS' => $idS,
            'pole' => $pole,
            'data' => $data,
        ));
    }
	public function listDossierAValiderChefGreffeAction($data = null, $idS = 1) {

        $user = $this->container->get('security.context')->getToken()->getUser();

        $pole = $user->getPole();

        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }

        $em = $this->getDoctrine()->getManager();
        $data = $this->getRequest()->request->get('data');
        $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierChefGreffe($user, null, $idCodeLangue, $idPole, 25, null);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['dossiersChefGreffe'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierChefGreffe($user, $data, $idCodeLangue, $idPole, null, null);
        }

        $form = $this->createForm(new DossierChefGreffeSearchType(array('langue' => $langue)));

        $form->bind($request);

        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/dossierAValiderChefGreffe.html.twig', array('form' => $form->createView(), 'listerdemande' => $listerdemande, 'langue' => $idCodeLangue, 'idp' => $idPole, 'idS' => $idS, 'pole' => $pole));
    }
	public function listDossierValideChefGreffeAction($data = null, $idS = 1) {

        $user = $this->container->get('security.context')->getToken()->getUser();

        $pole = $user->getPole();

        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }

        $em = $this->getDoctrine()->getManager();
        $data = $this->getRequest()->request->get('data');
        $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierChefGreffe($user, null, $idCodeLangue, $idPole, 25, 2);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['dossiersChefGreffe'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierChefGreffe($user, $data, $idCodeLangue, $idPole, null, 2);
        }

        $form = $this->createForm(new DossierChefGreffeSearchType(array('langue' => $langue)));

        $form->bind($request);

        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/dossierValideChefGreffe.html.twig', array('form' => $form->createView(), 'listerdemande' => $listerdemande, 'langue' => $idCodeLangue, 'idp' => $idPole, 'idS' => $idS, 'pole' => $pole));
    }
    public function fraisDossierPoleAction(Request $request, $idd = null) {
        $em = $this->getDoctrine()->getManager();
        //$request = $this->get('request');
        $dossierDemande = $em->find('BanquemondialeBundle:DossierDemande', $idd);
        $idFormeJ = $dossierDemande->getFormeJuridique()->getId();
        //$idTypeOp = $dossierDemande->getTypeOperation()->getId();
        //$listFraisDossier = $em->getRepository('ParametrageBundle:Tarification')->findBy(array('typeOperation' => $idTypeOp, 'formeJuridique' => $idFormeJ));
        $listFraisDossier = $em->getRepository('ParametrageBundle:Tarification')->getListeFraisAPayer($idd);
        $total = 0;
        foreach ($listFraisDossier as $frais) {
            $montant = $frais->getMontant();
            $total = $total + $montant;
        }
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
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/fraisDossierPole.html.twig', array('listFraisDossier' => $listFraisDossier, 'total' => $total, 'dd' => $dossierDemande, 'rteSuivant' => $nextRte, 'routeAvant' => $routeAvant));
    }
    public function viewPdfAction($idd, $pdfName) {

        $em = $this->getDoctrine()->getManager();
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminUpload = $chemin->getNom(); //a modifier par la requete vers la bdd
        $path = $cheminUpload . $idd . "\\" . $pdfName;
        $response = new BinaryFileResponse($path);

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Cache-Control', 'no-store');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->setSharedMaxAge(10);
        $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE, //ResponseHeaderBag::DISPOSITION_ATTACHMENT pour download DISPOSITION_INLINE pour render
                $pdfName
        );


        return $response;
    }
    public function visualiserAllPieceJoinedAction(Request $request, $idd = null) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $idFormeJ = $dossierDemande->getFormeJuridique()->getId();
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang)->getId();
        $listPieceEntreprise = $em->getRepository('BanquemondialeBundle:CollectionPieceJointe')->findAllPieceJoined($idd, $langue);

        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminUpload = $chemin->getNom(); //a modifier par la requete vers la bdd        
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $nextRte = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextEtapePoleRoute($currentRoute, $idFormeJ);

        if (!$nextRte) {
            $nextRte = 'validerDossierDiaspora';
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
        return $this->render('ParametrageBundle:ParameterPole:pieceJointePole.html.twig', array('listPieceEntreprise' => $listPieceEntreprise, 'idd' => $idd, 'dd' => $dossierDemande, 'cheminUpload' => $cheminUpload, 'rteSuivant' => $nextRte, 'routeAvant' => $routeAvant));
    }
    public function situationTraitementPoleAction($idd) {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $user = $this->container->get('security.context')->getToken()->getUser();
        /* $userPole = $user->getPole();
          /* $isDossierDiaspora
          if ($userPole->getSigle() === "DP") {

          } */
        /* if ($dossierDemande && $dossierDemande->getUtilisateur() && $dossierDemande->getUtilisateur()->getPole()) {
          $poleOfUserDossier = $dossierDemande->getUtilisateur()->getPole();
          if ($poleOfUserDossier->getSigle() != "DP" && $poleOfUserDossier->getSigle() != "INV") {
          $translated = $this->get('translator')->trans('acces_inautorise');
          $this->get('session')->getFlashBag()->add('error', $translated);
          return $this->redirectToRoute('administration');
          }
         */
        /* if ($dossierDemande == null or $dossierDemande->getUtilisateur() != $user) {

          } */
        $listDocumentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDocumentCollectedByDossierDemande($dossierDemande, $langue);
        $listPieceEntreprise = $em->getRepository('BanquemondialeBundle:CollectionPieceJointe')->findAllPieceJoined($idd, $langue);
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/situationTraitementPole.html.twig', array('listDocumentCollected' => $listDocumentCollected, 'idd' => $idd, 'dd' => $dossierDemande, 'listPieceEntreprise' => $listPieceEntreprise));
    }
    public function traiterDemandeModificationAction($idd) {
        $em = $this->getDoctrine()->getManager();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        if ($dossierDemande) {

            //$dossierDemande->setStatut(4);
            $em->persist($dossierDemande);
            $em->flush();
        }

        return new RedirectResponse($this->container->get('router')->generate('editDossier', array('idd' => $idd)));
    }
    public function etapeBrouillonAction($idd = null) {
        $em = $this->getDoctrine()->getManager();
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');

        $isAguipe = true;
        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findBySigle("AGUIPE");
        $docAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
        if ($docAguipe) {
            $isAguipe = true;
        } else {
            $isAguipe = false;
        }
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $idF = 0;
        if ($dossierDemande) {
            $formeJuridique = $dossierDemande->getFormeJuridique();
            if ($formeJuridique) {
                $idF = $formeJuridique->getId();
            }
        }
        //$listEtapes = $em->getRepository('ParametrageBundle:EtapeCreation')->findBy(array(), array('ordre' => 'asc'));
        $listEtapes = $em->getRepository('ParametrageBundle:EtapeCreation')->getListeEtapeBrouillonByLangue($langue, $isAguipe, $idF);
        if ($listEtapes) {

            $listEtapes = $em->getRepository('ParametrageBundle:EtapeCreation')->getListeEtapeBrouillonByLangue($langue, $isAguipe, $idF);
        }

        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/etapeCreation.html.twig', array('idd' => $idd, 'listeEtapes' => $listEtapes, 'rte' => $currentRoute));
    }
    public function etapeCreationPoleAction($idd = null, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $idF = 0;
        if ($dossierDemande) {
            $formeJuridique = $dossierDemande->getFormeJuridique();
            if ($formeJuridique) {
                $idF = $formeJuridique->getId();
            }
        }
        $listEtapes = $em->getRepository('ParametrageBundle:EtapeCreation')->getListeEtapePoleByLangue($langue, $idF);
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/etapeCreationPole.html.twig', array('idd' => $idd, 'listeEtapes' => $listEtapes, 'rte' => $currentRoute));
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
            //die(dump($rteSuivant));
        }
        return $rteSuivant;
    }
    public function reportFormulaireDelivreAction($idp, $idd, $numero) {
        $em = $this->getDoctrine()->getManager();

        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $em->getRepository('ParametrageBundle:Pole')->find($idp);
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminUpload = $chemin->getNom();
        $formulaireDelivre = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossierDemande, 'numero' => $numero));

        if ($formulaireDelivre) {
            $nom = $pole->getNom();
            $path = $cheminUpload . "formulairesDelivres\\" . $formulaireDelivre->getNomFichier();
            $response = new BinaryFileResponse($path);

            $response->headers->set('Content-Type', 'application/pdf');
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, "formulaire_" . $nom . ".pdf");

            return $response;
        }
        $response = "";
        return $response;
    }
    function sendSMS($langue, $sms, $telephone) {
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
    function sendMail($sujet, $texte, $utilisateur) {
        $em = $this->getDoctrine()->getManager();

        $messagerie = $em->getRepository('ParametrageBundle:Messagerie')->find(1);
        $transport = \Swift_SmtpTransport::newInstance($messagerie->getMailerHost(), $messagerie->getMailerPort())
                        ->setUsername($messagerie->getMailerUser())
                        ->setPassword($messagerie->getMailerPassword())->setEncryption($messagerie->getEncryption());
        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance($transport);

        $translated =  $this->get('translator')->trans("utilisateur.confirmation_incription");

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
    public function resumeDossierAction($idd) {
        $em = $this->getDoctrine()->getManager();
        //$user = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);

        $tyOpeTradut = $em->getRepository('BanquemondialeBundle:TypeOperationTraduction')->findOneBy(array('langue' => $langue, 'typeOperation' => $dossierDemande->getTypeOperation()->getId()));
        $formeJTraduit = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('langue' => $langue, 'formeJuridique' => $dossierDemande->getFormeJuridique()->getId()));
        $secteurTraduit = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue, 'secteurActivite' => $dossierDemande->getSecteurActivite()->getId()));
        $paysTraduit = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('langue' => $langue, 'pays' => $dossierDemande->getPays()->getId()));
        $pays = $em->getRepository('BanquemondialeBundle:Pays')->findOneBy(array('residence' => true));

        $listerRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findBy(array('dossierDemande' => $idd));
        $listeradmin = $em->getRepository('BanquemondialeBundle:Administrateur')->findByDossierDemande($idd);
        $listerassocie = $em->getRepository('BanquemondialeBundle:Associe')->rechercheByDossierDemande($idd);
        $listCommissaireDossier = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findDossierDemandeCommissaire($idd, $langue);
        $fonctionTraduit = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findByLangue($langue->getId());

        $listerpieceJointe = $em->getRepository('BanquemondialeBundle:CollectionPieceJointe')->findAllPieceJoined($dossierDemande, $langue);
        $originePm = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($dossierDemande);

        $idFormeJ = $dossierDemande->getFormeJuridique()->getId();
        $idTypeOp = $dossierDemande->getTypeOperation()->getId();
        $cnss = $em->getRepository('BanquemondialeBundle:Cnss')->findOneByDossierDemande($idd);

        $isAguipe = true;
        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle("AGUIPE");
        $docAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
        if ($docAguipe) {
            $isAguipe = true;
        } else {
            $isAguipe = false;
        }
        $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
        //die(dump($dossierDemande->getTypeDossier()->getId()));
        //die(dump($dossierDemande->getFormeJuridique()->getId())); 
        $fraisApip = $em->getRepository('ParametrageBundle:Tarification')->getMontantFraisByPole($dossierDemande->getFormeJuridique()->getId(), $dossierDemande->getTypeDossier()->getId(), $poleAPIP->getId());

//$listFraisDossier = $em->getRepository('ParametrageBundle:Tarification')->findBy(array('typeOperation' => $idTypeOp, 'formeJuridique' => $idFormeJ));
        $listFraisDossier = $em->getRepository('ParametrageBundle:Tarification')->getListeFraisAPayer($idd);

        $total = 0;
        $translated = "";
        foreach ($listFraisDossier as $frais) {
            $montant = $frais->getMontant();
            $total = $total + $montant;
        }
        foreach ($fraisApip as $frais) {
            $montant = $frais->getMontant();
            $total = $total + $montant;
        }




        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/resume.html.twig', array('idd' => $idd,
                    'dossierDemande' => $dossierDemande, 'listerRepresentant' => $listerRepresentant,
                    'listeradmin' => $listeradmin, 'listerassocie' => $listerassocie, 'fonctionTraduit' => $fonctionTraduit,
                    'listCommissaireDossier' => $listCommissaireDossier, 'listerpieceJointe' => $listerpieceJointe,
                    'listFraisDossier' => $listFraisDossier, 'fraisAPIP' => $fraisApip, 'total' => $total,
                    'originePM' => $originePm, 'cnss' => $cnss));
    }
    public function visualiserP1Action($idd) {
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

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $representant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $representantEI = $em->getRepository('BanquemondialeBundle:Representant')->find($representant[0]);

        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));
        $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire(), 'langue' => 1));
        $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire2(), 'langue' => 1));



        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }
        $listeTypeFormalite = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->findBy(array('typeFormulaire' => "P1"));
        $origine = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($idd);
        $listeTypeOrigine = $em->getRepository('ParametrageBundle:TypeOrigine')->findBySiPersonnePhysique(true);
        $activiteAnterieure = $em->getRepository('BanquemondialeBundle:ActiviteAnterieure')->findOneByDossierDemande($idd);
        $personneEngageurs = $em->getRepository('BanquemondialeBundle:PersonneEngageur')->getPersEngageursByDossierDemande($idd, $langue->getId());
        //die(dump($representant));
        $conjoints = $em->getRepository('BanquemondialeBundle:Conjoint')->findByRepresentant($representantEI);
        //*

        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $parametrageSignature = $em->getRepository('DefaultBundle:ReglageActivation')->findFirst();
        if ($parametrageSignature) {            
            $libelleSignatureGreffe = $parametrageSignature->getLibelleSignatureGreffe();
        }
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserP1.html.twig', array('idd' => $idd, 'dd' => $dossierDemande, 'rep' => $representant[0], 'listeTypeOrigine' => $listeTypeOrigine, 'origine' => $origine,
            'pole' => $pole, 'listeTypeFormalite' => $listeTypeFormalite, 'dateRccm' => $dateRccm,
            'rccm' => $rccm, 'conjoints' => $conjoints,
            'activiteAnterieure' => $activiteAnterieure, 'personneEngageurs' => $personneEngageurs, 'activitePrincipale' => $activitePrincipale,
            'activiteSecondaire' => $activiteSecondaire,'libelleSignatureGreffe' => $libelleSignatureGreffe, 'activiteSecondaire2' => $activiteSecondaire2));
        $nomFichier = "formulaire" . $idd . "_" . $pole->getId() . ".pdf";
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);

        $html2pdf->Output("formulaireP1.pdf");
        exit;
    }
	public function visualiserChefGreffeP1Action($idd) {
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
		if($parametrageSignature)
		{
			$afficherSignature = $parametrageSignature->getIsSignatureVisible();
			$afficherQRCodeGreffe = $parametrageSignature->getIsQRVisible();
			$libelleSignatureGreffe = $parametrageSignature->getLibelleSignatureGreffe();
		}

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $representant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());
        $representantEI = $em->getRepository('BanquemondialeBundle:Representant')->find($representant[0]);

        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));
        $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire(), 'langue' => 1));
        $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire2(), 'langue' => 1));



        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }
        $listeTypeFormalite = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->findBy(array('typeFormulaire' => "P1"));
        $origine = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneByDossierDemande($idd);
        $listeTypeOrigine = $em->getRepository('ParametrageBundle:TypeOrigine')->findBySiPersonnePhysique(true);
        $activiteAnterieure = $em->getRepository('BanquemondialeBundle:ActiviteAnterieure')->findOneByDossierDemande($idd);
        $personneEngageurs = $em->getRepository('BanquemondialeBundle:PersonneEngageur')->getPersEngageursByDossierDemande($idd, $langue->getId());
        //die(dump($representant));
        $conjoints = $em->getRepository('BanquemondialeBundle:Conjoint')->findByRepresentant($representantEI);
        //*

        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserChefGreffeP1.html.twig', array('idd' => $idd, 'dd' => $dossierDemande, 'rep' => $representant[0], 'listeTypeOrigine' => $listeTypeOrigine, 'origine' => $origine,
            'pole' => $pole, 'listeTypeFormalite' => $listeTypeFormalite, 'dateRccm' => $dateRccm,
            'rccm' => $rccm, 'conjoints' => $conjoints,
            'activiteAnterieure' => $activiteAnterieure, 'personneEngageurs' => $personneEngageurs, 'activitePrincipale' => $activitePrincipale,
            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2,'afficherSignature'=> $afficherSignature, 
			'afficherQRCodeGreffe'=>$afficherQRCodeGreffe, 'libelleSignatureGreffe' => $libelleSignatureGreffe,'documentValide'=> false));
        $nomFichier = "formulaire" . $idd . "_" . $pole->getId() . ".pdf";
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr',true, 'UTF-8', array(5, 5, 5, 0));
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);

        $html2pdf->Output("formulaireP1.pdf");
        exit;
    }
    public function soumettreDepotAction($idd) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $dossierDemande = $em->find('BanquemondialeBundle:DossierDemande', $idd);
        $user = $dossierDemande->getUtilisateur();
        //die(dump($user->getNom()));
        $idFormeJ = $dossierDemande->getFormeJuridique()->getId();
        $idTypeOp = $dossierDemande->getTypeOperation()->getId();
        //$codLang = $request->getLocale();
        //$langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang)->getId();
        $listPieceEntreprise = $em->getRepository('BanquemondialeBundle:PieceJointe')->findByPieceEntreprise($idTypeOp, $idFormeJ, $langue->getId());
        $listPieceAdmin = $em->getRepository('BanquemondialeBundle:PieceJointe')->findPieceAssocie($idd, $idTypeOp, $idFormeJ, $langue->getId());
        //$typeDossier = $dossierDemande->getTypeDossier();   a rajouter si on met type de dossier dans dossier demande
        $nbCommissaireRequis = 0;

        //die("test");
        //verification Piece Jointes
        $listPieceJointes = $em->getRepository('BanquemondialeBundle:CollectionPieceJointe')->findBy(array('dossierDemande' => $idd));
        $nbCommissaire = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->countCommissaire($idd);
        $regleCommissaireRequis = $em->getRepository('ParametrageBundle:Regle')->findOneBy(array('formeJuridique' => $idFormeJ));
        if ($regleCommissaireRequis) {
            $nbCommissaireRequis = $regleCommissaireRequis->getCommissaireRequis();
        }
        //liste pour verification sur l'existence de conjoint pour un representant marié
        $listeRepresentants = $em->getRepository('BanquemondialeBundle:Representant')->findByDossierDemande($dossierDemande);

        $nbre = sizeof($listPieceEntreprise) + sizeof($listPieceAdmin);
        //die(dump($nbre));
        if (sizeof($listPieceJointes) == $nbre) {
            if ($nbCommissaireRequis) {
                if (!$nbCommissaire || $nbCommissaire < 1) {
                    $translated = $this->get('translator')->trans("message_commissaire_au_moins");
                    $this->get('session')->getFlashBag()->add('info', $translated);
                    return $this->redirectToRoute('ajoutCommissaire', array('idd' => $idd));
                }
            }

            //restriction sur un representant marie sans conjoint inscrit
            if ($listeRepresentants) {
                foreach ($listeRepresentants as $representant) {
                    $idSitua = $representant->getSituationMatrimoniale();

                    if ($idSitua && $idSitua->getCode() == 'M') {
                        $listeConjointsRepresentant = $em->getRepository('BanquemondialeBundle:Conjoint')->findByRepresentant($representant);
                        if (!$listeConjointsRepresentant) {
                            $translated = $this->get('translator')->trans("representant_marie_sans_conjoint");
                            $this->get('session')->getFlashBag()->add('info', $translated);
                            return $this->redirectToRoute('representant_conjoints', array('id' => 0, 'idr' => $representant->getId()));
                        }
                    }
                }
            }

            //verification nombre employe aguipe
            $ficheEntreprise = $em->getRepository('BanquemondialeBundle:Aguipe')->findOneByDossierDemande($idd);
            if ($ficheEntreprise && $dossierDemande->getNombreSalariePrevu() != $ficheEntreprise->getNombreEmployeGuineen() + $ficheEntreprise->getNombreEmployeEtranger()) {
                //die(dump($ficheEntreprise));
                $translated = $this->get('translator')->trans("message_effectif_guineen_etranger_non_coherent");
                $this->get('session')->getFlashBag()->add('info', $translated);
                return $this->redirectToRoute('createfiche_entreprise', array('idd' => $idd));
            }


            //verification nombre employe cnss
            $cnss = $em->getRepository('BanquemondialeBundle:Cnss')->findOneByDossierDemande($idd);
            if ($cnss && $dossierDemande->getNombreSalariePrevu() != $cnss->getEffectifHomme() + $cnss->getEffectifFemme()) {
                $translated = $this->get('translator')->trans("message_effectif_homme_femme_non_coherent");
                $this->get('session')->getFlashBag()->add('info', $translated);
                return $this->redirectToRoute('path_cnss', array('idd' => $idd));
            }

            //Au moins une donnée
            $numeroDossier = 'GU-CE-' . strtoupper($dossierDemande->getPays()->getCode()) . sprintf("%08d", $idd);
            $date = new \DateTime();
            if ($dossierDemande->getStatut() == 3) {
                $dossierDemande->setStatut(1);
                $em->persist($dossierDemande);
            } else {
                $dossierDemande->setStatut(1);
               if(empty( $dossierDemande->getDateCreation())){
                   $dossierDemande->setDateCreation($date);
               }
                //$dossierDemande->setDateValidation($date);               
                //$dossierDemande->setStatutValidation(1);
                //$dossierDemande->setUtilisateurValidation($user);
                $dossierDemande->setNumeroDossier($numeroDossier);

                $em->persist($dossierDemande);
            }

            $em->flush();
            $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
            $docForValidationAPIP = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('dossierDemande' => $idd, 'pole' => $poleAPIP->getId()));
            if (!$docForValidationAPIP) {
                $docCollected = new DocumentCollected();
                $docCollected->setPole($poleAPIP);
                $docCollected->setOrdre(0);
                $docCollected->setDossierDemande($dossierDemande);
                $docCollected->setDateSoumission($date);
                $statutEncours = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(1);
                $docCollected->setStatutTraitement($statutEncours);
                $em->persist($docCollected);
                $em->flush();
            }

            $message = $this->get('translator')->trans('demande_soumise_message');
            //Envoie d'une notification
            $objet = $this->get('translator')->trans('demande_soumise_objet');
            $notif = $this->container->get('utilisateurs.notification');
            $notif->notifier($dossierDemande->getNumeroDossier() . ' ' . $message, $dossierDemande->getUtilisateur(), $objet);

            $sujet = $this->get('translator')->trans('sujet_notification_email');
            $texte = $this->get('translator')->trans('texte_notification_email_demande_soumise') . " " . $numeroDossier;
            try {
                $this->sendMail($sujet, $texte, $dossierDemande->getUtilisateur());
            } catch (\Exception $e) {
                $translated =  $this->get('translator')->trans("error_send_mail");
                $this->get('session')->getFlashBag()->add('error', $translated);
                $this->get('logger')->error($e->getMessage());
            }

            $idd = $dossierDemande->getId();
            $this->setListDocCollected($idd);

            $translated = $this->get('translator')->trans('message_soumission_succes_dossier_numero') . " " . $numeroDossier;


            //echo "<script>alert('Votre dossier N° '+$idd+' a été soumis avec succes')</script>";
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirectToRoute('suivreDossier');
            //return new RedirectResponse($this->container->get('router')->generate('', array('idd' => $idd)));
        } else {

            $translated = $this->get('translator')->trans("message_joindre_toute_les_pieces");
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirectToRoute('pieceJointe', array('idd' => $idd));
        }

        //Fin
    }
    public function soumettreDossierAction($idd = null) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $dossierDemande = $em->find('BanquemondialeBundle:DossierDemande', $idd);
        $user = $dossierDemande->getUtilisateur();
        $userPole = $user->getPole();


        $idFormeJ = $dossierDemande->getFormeJuridique()->getId();
        $idTypeOp = $dossierDemande->getTypeOperation()->getId();
        //$codLang = $request->getLocale();
        //$langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang)->getId();
        $listPieceEntreprise = $em->getRepository('BanquemondialeBundle:PieceJointe')->findByPieceEntreprise($idTypeOp, $idFormeJ, $langue->getId());
        $listPieceAdmin = $em->getRepository('BanquemondialeBundle:PieceJointe')->findPieceAssocie($idd, $idTypeOp, $idFormeJ, $langue->getId());
        //$typeDossier = $dossierDemande->getTypeDossier();   a rajouter si on met type de dossier dans dossier demande
        $nbCommissaireRequis = 0;
        $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findBySigle("AGUIPE");
        $fraisApip = $em->getRepository('ParametrageBundle:Tarification')->getMontantFraisByPole($dossierDemande->getFormeJuridique()->getId(), $dossierDemande->getTypeDossier()->getId(), $poleAPIP->getId());

        $listFraisDossier = $em->getRepository('ParametrageBundle:Tarification')->getListeFraisAPayer($idd);
        $listeRepresentants = $em->getRepository('BanquemondialeBundle:Representant')->findByDossierDemande($dossierDemande);

        $total = 0;
        $translated = "";
        foreach ($fraisApip as $frais) {
            $montant = $frais->getMontant();
            $total = $total + $montant;
        }
        foreach ($listFraisDossier as $frais) {
            $montant = $frais->getMontant();
            $total = $total + $montant;
        }

        if ($request->getMethod() == 'POST') {
            //die("test");
            //verification Piece Jointes
            if ($userPole->getSigle() === "DP" || $userPole->getSigle() === "INV") {

                return $this->redirectToRoute('soumettre_depot', array('idd' => $idd));
            }
            //Obligation de saisir au moins un dirigeant
            if (!$listeRepresentants) {
                $translated = $this->get('translator')->trans("etape_requise");
                $this->get('session')->getFlashBag()->add('info', $translated);
                return $this->redirectToRoute('representant_listerrepresentantpole', array('idd' => $idd));
            }
            //Verification saisi onglet CNSS( etape requise)
            $cnss = $em->getRepository('BanquemondialeBundle:Cnss')->findOneBy(array('dossierDemande' => $idd));
            if (!$cnss) {
                $translated = $this->get('translator')->trans("etape_requise");
                $this->get('session')->getFlashBag()->add('info', $translated);
                return $this->redirectToRoute('path_cnss', array('idd' => $idd));
            }
            $docAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
            if ($docAguipe) {
                $ficheEntreprise = $em->getRepository('BanquemondialeBundle:Aguipe')->findOneByDossierDemande($idd);
                if (!$ficheEntreprise) {
                    $translated = $this->get('translator')->trans("etape_requise");
                    $this->get('session')->getFlashBag()->add('info', $translated);
                    return $this->redirectToRoute('createfiche_entreprise', array('idd' => $idd));
                }
                //verification nombre employe aguipe

                if ($ficheEntreprise && $dossierDemande->getNombreSalariePrevu() != $ficheEntreprise->getNombreEmployeGuineen() + $ficheEntreprise->getNombreEmployeEtranger()) {
                    //die(dump($ficheEntreprise));
                    $translated = $this->get('translator')->trans("message_effectif_guineen_etranger_non_coherent");
                    $this->get('session')->getFlashBag()->add('info', $translated);
                    return $this->redirectToRoute('createfiche_entreprise', array('idd' => $idd));
                }
            }


            $listPieceJointes = $em->getRepository('BanquemondialeBundle:CollectionPieceJointe')->findBy(array('dossierDemande' => $idd));
            $nbCommissaire = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->countCommissaire($idd);
            $regleCommissaireRequis = $em->getRepository('ParametrageBundle:Regle')->findOneBy(array('formeJuridique' => $idFormeJ));
            if ($regleCommissaireRequis) {
                $nbCommissaireRequis = $regleCommissaireRequis->getCommissaireRequis();
            }
            //liste pour verification sur l'existence de conjoint pour un representant marié

            $nbre = sizeof($listPieceEntreprise) + sizeof($listPieceAdmin);
            //die(dump($nbre));
            //if (sizeof($listPieceJointes) == $nbre) {
            if (sizeof($listPieceJointes) > 0) {
                if ($nbCommissaireRequis) {
                    if (!$nbCommissaire || $nbCommissaire < 1) {
                        $translated = $this->get('translator')->trans("message_commissaire_au_moins");
                        $this->get('session')->getFlashBag()->add('info', $translated);
                        return $this->redirectToRoute('ajoutCommissaire', array('idd' => $idd));
                    }
                }

                //restriction sur un representant marie sans conjoint inscrit
                if ($listeRepresentants) {
                    foreach ($listeRepresentants as $representant) {
                        $idSitua = $representant->getSituationMatrimoniale();
                        if ($idSitua && $idSitua->getCode() == 'M') {
                            $listeConjointsRepresentant = $em->getRepository('BanquemondialeBundle:Conjoint')->findByRepresentant($representant);
                        }
                    }
                }



                //verification nombre employe cnss

                if ($cnss && $dossierDemande->getNombreSalariePrevu() != $cnss->getEffectifHomme() + $cnss->getEffectifFemme()) {
                    $translated = $this->get('translator')->trans("message_effectif_homme_femme_non_coherent");
                    $this->get('session')->getFlashBag()->add('info', $translated);
                    return $this->redirectToRoute('path_cnss', array('idd' => $idd));
                }

                //Au moins une donnée
                $numeroDossier = 'GU-CE-' . strtoupper($dossierDemande->getPays()->getCode()) . sprintf("%08d", $idd);
                $date = new \DateTime();
                if ($dossierDemande->getStatut() == 3) {
                    $dossierDemande->setStatut(1);
                    $em->persist($dossierDemande);
                } else {
                    $dossierDemande->setStatut(1);
                    $dossierDemande->setNumeroDossier($numeroDossier);
                    if (empty($dossierDemande->getDateCreation())){
                        $dossierDemande->setDateCreation($date);
                    }
                    $this->get('suivistatutdossierservice')->getAndsetStatSaisie('set',$idd,null,null);
                    $em->persist($dossierDemande);
                }
                $em->flush();

                $message = $this->get('translator')->trans('demande_soumise_message');
                //Envoie d'une notification
                $objet = $this->get('translator')->trans('demande_soumise_objet');
                $notif = $this->container->get('utilisateurs.notification');
                $notif->notifier($dossierDemande->getNumeroDossier() . ' ' . $message, $dossierDemande->getUtilisateur(), $objet);

                $sujet = $this->get('translator')->trans('sujet_notification_email');
                $texte = $this->get('translator')->trans('envoi_email_soumission_saisie',array('%denominationSociale%' => $dossierDemande->getDenominationSociale()));

                
			    if($dossierDemande->getEmailPromoteur())
			    {
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
                 
                $indicateur = $em->getRepository('ParametrageBundle:Chemins')->find(1);
                $telephone = '' . $indicateur->getNom() . '' . $dossierDemande->getUtilisateur()->getTelephone();



                if ($request->getLocale() == 'fr') {
                    $sms = 'Votre+dossier+' . $dossierDemande->getNumeroDossier() . '+a+été+soumis.';
                } else if ($request->getLocale() == 'en') {
                    $sms = 'Your+request+' . $dossierDemande->getNumeroDossier() . '+has+been+submited.';
                } else {
                    $sms = 'Your+request+' . $dossierDemande->getNumeroDossier() . '+has+been+submited.';
                }

                //$retour = $this->sendSMS($request->getLocale(), $sms, $telephone);

                $idd = $dossierDemande->getId();
                $this->setListDocCollected($idd);
                $listDemandeEnmodification = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd, 'statutTraitement' => 3));

                if (!$listDemandeEnmodification) {
                    $profil = $dossierDemande->getUtilisateur()->getProfile();
                    $premiersPolesTraitant = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findFirstsPoles($idd);

                    if ($profil) {
                        $descrip = $profil->getDescription();
                        if (strpos($descrip, 'saisi') !== false) {

                            $premiersPolesTraitant = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findFirstPoleTraitant($idd);
                            //die(dump($premiersPolesTraitant));
                        }
                    }

                    if ($premiersPolesTraitant) {
                        $statutEncours = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(1);
                        foreach ($premiersPolesTraitant as $poleTraitant) {
                            $poleTraitant->setStatutTraitement($statutEncours);
                            //Conservation premiere date de soumission pole
                            $date = $poleTraitant->getDateSoumission() ? $poleTraitant->getDateSoumission() : $date;
                            $poleTraitant->setDateSoumission($date);
                            $em->persist($poleTraitant);

                            $message = $this->get('translator')->trans('message_dossier_recu');

                            $objet = $this->get('translator')->trans('reception_dossier_objet');
                            $notif = $this->container->get('utilisateurs.notification');
                            $notif->notifier($message . ' ' . $dossierDemande->getNumeroDossier(), $poleTraitant->getPole()->getUtilisateur()->get(0), $objet);
                        }

                        $em->flush();
                        $this->get('suivistatutdossierservice')->getAndsetStatSaisie('set',$idd,null,null);
                        $translated = $this->get('translator')->trans('message_soumission_succes_dossier_numero') . " " . $numeroDossier;
                    }

                    //generation d'une facturation et repartition facturation pour brouillard de caisse et autres
                    if ($profil) {
                        $descrip = $profil->getDescription();
                        if (strpos($descrip, 'saisi') !== false) {
                            
                        } else {
                            $this->ajoutQuittance($idd);
                        }
                    }

                    //fin generation d'une facturation
                }
                else {
                    $profil = $dossierDemande->getUtilisateur()->getProfile();
                    if ($profil) {
                        $descrip = $profil->getDescription();
                        if (strpos($descrip, 'saisi') !== false) {
                            
                        } else {
                            $this->ajoutQuittance($idd);
                        }
                    }
                    foreach ($listDemandeEnmodification as $docEnModifcation) {
                        $statutEncours = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(1);
                        $docEnModifcation->setStatutTraitement($statutEncours);
                        $date = $docEnModifcation->getDateSoumission() ? $docEnModifcation->getDateSoumission() : $date;
                        $docEnModifcation->setDateSoumission($date);
                        $em->persist($docEnModifcation);

                        $message = $this->get('translator')->trans('message_dossier_recu');

                        $objet = $this->get('translator')->trans('reception_dossier_objet');
                        $notif = $this->container->get('utilisateurs.notification');
                        $notif->notifier($message . ' ' . $dossierDemande->getNumeroDossier(), $docEnModifcation->getPole()->getUtilisateur()->get(0), $objet);
                    }
                    $this->get('suivistatutdossierservice')->getAndsetStatSaisie('set',$idd,null,null);
                    $em->flush();
                    $translated = $this->get('translator')->trans('message_soumission_succes_dossier_numero') . " " . $numeroDossier;
                }
                //echo "<script>alert('Votre dossier N° '+$idd+' a été soumis avec succes')</script>";
                $this->get('session')->getFlashBag()->add('info', $translated);

                return $this->redirectToRoute('suivreDossier');
                //return new RedirectResponse($this->container->get('router')->generate('', array('idd' => $idd)));
            } else {
                // if ($nbCommissaire == 0) {
                // $translated = $this->get('translator')->trans("message_commissaire_au_moins");
                //$this->get('session')->getFlashBag()->add('info', $translated);
                //return $this->redirectToRoute('ajoutCommissaire', array('idd' => $idd));
                // }
                $translated = $this->get('translator')->trans("message_joindre_au_moins_une_piece");
                //$translated = $this->get('translator')->trans("message_joindre_toute_les_pieces");
                $this->get('session')->getFlashBag()->add('info', $translated);

                return $this->redirectToRoute('pieceJointe', array('idd' => $idd));
            }
            //Fin
        }

        return $this->redirectToRoute('pieceJointe', array('idd' => $idd));
    }
    public function ajoutQuittance($idd) {
        $em = $this->getDoctrine()->getManager();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $numeroDossier = 'GU-CE-' . strtoupper($dossierDemande->getPays()->getCode()) . sprintf("%08d", $idd);
        $date = new \DateTime();
        $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
        $fraisApip = $em->getRepository('ParametrageBundle:Tarification')->getMontantFraisByPole($dossierDemande->getFormeJuridique()->getId(), $dossierDemande->getTypeDossier()->getId(), $poleAPIP->getId());
        $listFraisDossier = $em->getRepository('ParametrageBundle:Tarification')->getListeFraisAPayer($idd);
        $total = 0;
        foreach ($listFraisDossier as $frais) {
            $montant = $frais->getMontant();
            $total = $total + $montant;
        }
        foreach ($fraisApip as $frais) {
            $montant = $frais->getMontant();
            $total = $total + $montant;
        }

        $user = $this->container->get('security.context')->getToken()->getUser();
        $quittance = $em->getRepository('BanquemondialeBundle:Quittance')->findOneBy(array('dossierDemande' => $idd));
        if (!$quittance) {
            $quittance = new Quittance();
            $quittance->setNumeroDossier($numeroDossier);
            $quittance->setDenominationSociale($dossierDemande->getDenominationSociale());
            $quittance->setFormeJuridique($dossierDemande->getFormeJuridique());
            $quittance->setMontantTotalFacture($total);
            $quittance->setMontantRestant($total);
            $quittance->setMontantVerse(null);
            if ($quittance->getDateFacturation() == null) {
                $quittance->setDateFacturation($date);
            }

            //$quittance->setDatePaiement(null);
            $quittance->setIsPaid(false);
            $quittance->setTranche(1);
            $quittance->setNumeroQuittance(null);
            $quittance->setDossierDemande($dossierDemande);
            $quittance->setModePaiement(null);
            $quittance->setUtilisateur(null);

            $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
            if ($user->getPole() && $user->getPole()->getId() == $poleAPIP->getId()) {
                $quittance->setSousPrefecture($user->getEntreprise()->getSousPrefecture());
            } else {
                $ApipSiege = $em->getRepository("BanquemondialeBundle:Entreprise")->findOneBy(array('isSiege' => true, 'pole' => $poleAPIP->getId()));
                $quittance->setSousPrefecture($ApipSiege->getSousPrefecture());
            }
            /* if ($user->getProfile() && $user->getProfile()->getDescription() == "dépot") {
              $quittance->setSousPrefecture($user->getEntreprise()->getSousPrefecture());
              } else {
              $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
              $ApipSiege = $em->getRepository("BanquemondialeBundle:Entreprise")->findOneBy(array('isSiege' => true, 'pole' => $poleAPIP->getId()));
              $quittance->setSousPrefecture($ApipSiege->getSousPrefecture());
              } */

            $em->persist($quittance);
            $em->flush();
        } else {
            $quittance->setMontantTotalFacture($total);
            $quittance->setMontantRestant($total);
            $quittance->setDenominationSociale($dossierDemande->getDenominationSociale());
            $quittance->setFormeJuridique($dossierDemande->getFormeJuridique());
            //retour dossier
            //$quittance->setDatePaiement(null);
            $quittance->setStatut(1);
            $quittance->setIsPaid(false);
            /* if ($user->getProfile() && $user->getProfile()->getDescription() == "dépot") {
              $quittance->setSousPrefecture($user->getEntreprise()->getSousPrefecture());
              } else {
              $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
              $ApipSiege = $em->getRepository("BanquemondialeBundle:Entreprise")->findOneBy(array('isSiege' => true, 'pole' => $poleAPIP->getId()));
              $quittance->setSousPrefecture($ApipSiege->getSousPrefecture());
              } */
            $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
            if ($user->getPole() && $user->getPole()->getId() == $poleAPIP->getId()) {
                $quittance->setSousPrefecture($user->getEntreprise()->getSousPrefecture());
            } else {
                $ApipSiege = $em->getRepository("BanquemondialeBundle:Entreprise")->findOneBy(array('isSiege' => true, 'pole' => $poleAPIP->getId()));
                $quittance->setSousPrefecture($ApipSiege->getSousPrefecture());
            }

            $em->persist($quittance);
            $em->flush();
        }
    }
    public function chargerSecteurActiviteAction(Request $request) {        
        if ($request->isXmlHttpRequest()) {
            $codLang = $request->getLocale();
            $em = $this->getDoctrine()->getManager();
            $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
            $categorie = $em->getRepository('ParametrageBundle:CategorieActiviteTraduction')->find($request->get('idC'));
            
            if (!$categorie){
                return new JsonResponse(array('error' => '1'));            
            }
            //$secteurs = $em->getRepository('BanquemondialeBundle:SecteurActivite')->findBy(array('categorieActivite' => $categorie->getCategorieActivite()));
            $secteurTraductions = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->getListSecteursAndTraductionByCategorie($langue, $categorie->getCategorieActivite());

            $retour = array();
            $retourId = array();
//die(dump($secteurTraductions));
            $i = 0;
            foreach ($secteurTraductions as $secteurTraduction) {
                //$secteursT = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $secteur, 'langue' => $langue));
                //foreach ($secteursTraduit as $secteurT) {
                
                $retour[$i] = $secteurTraduction->getLibelle();
                $retourId[$i] = $secteurTraduction->getId();
                $i++;
                //}
            }



            return new JsonResponse(array('retour' => $retour, 'retourId' => $retourId));
        } else
            return new JsonResponse();
    }
    public function formulaireG1Action($idd) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $message = '';
        $styleNumeroFormalite = null;
        $styleNumeroEntreprise = null;
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $representant = $em->getRepository('BanquemondialeBundle:Representant')->findBy(array('dossierDemande' => $idd));
        $leRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findOneBy(array('dossierDemande' => $idd));
        $associe = $em->getRepository('BanquemondialeBundle:Associe')->findBy(array('dossierDemande' => $idd));
		$administrateurs = $em->getRepository('BanquemondialeBundle:Administrateur')->findByDossierDemande($idd);
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneBy(array('dossierDemande' => $idd));
        if ($rccm) {
            $typeF = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->find($rccm->getTypeFormaliteRccm());
        } else {
            $typeF = null;
        }
        $dossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $documentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('pole' => $pole, 'dossierDemande' => $dossier));
        $motif = $documentCollected->getMotif();
        $formJ = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->getLibelleFormeJuridiqueByLanque($langue->getId(), $dossier->getFormeJuridique());
        $secAct = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossier->getSecteurActivite()));
        $listeTypF = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->findByTypeFormulaire("G1");
        $dateRccm = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }
        $lettreFormulaire = "B";
        if ($listeTypF) {
            $lettreFormulaire = $listeTypF[0]->getLettreFormulaire();
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
        if ($request->getMethod() == 'POST') {
            //die(dump($request->get('typeFormalite')));
            if ($request->request->has('textAreaModifier')) {
                $motif = $request->get('textAreaModifier');
                if (strlen($motif) <= 255) {
                    $documentCollected->setMotif($motif);
                    $statutTraitementModifier = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(3);
                    $documentCollected->setStatutTraitement($statutTraitementModifier);
                    $em->persist($documentCollected);

                    $dossier->setStatut(3);
                    $em->persist($dossier);
                    $em->flush();
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                }
            } else {
                $complementNumRccm = $request->get('complementNumRccm');
                $rccmFormalite = $debutNumRccmFormalite . $request->get('rccmFormalite');
                $rccmEntreprise = $complementNumRccm . $request->get('rccmEntreprise');  //die(dump($request));

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

                    return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/formulaireG1.html.twig', array('idd' => $idd, 'representant' => $representant, 'dossier' => $dossier, 'formeJ' => $formJ, 'secAct' => $secAct, 'associe' => $associe, 'administrateurs'=> $administrateurs, 'rccm' => $rccm, 'typeF' => $typeF,
                                'leRepresentant' => $leRepresentant, "formatRccm" => $debutNumRccm, "numSequentiel" => $numSequentiel, 'numSequentielEntreprise' => $numSequentielEntreprise, 'message' => $message, 'listeTypF' => $listeTypF, 'motif' => $motif, 'styleNumeroFormalite' => $styleNumeroFormalite, 'styleNumeroEntreprise' => $styleNumeroEntreprise,
                                'statutTraitrement' => $documentCollected->getStatutTraitement()));
                }

                if ($rccm) {
                    $typeF = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->find($request->get('typeFormalite'));
                    $rccm->setTypeFormaliteRccm($typeF);
                    $rccm->setNumRccmEntreprise($rccmEntreprise);
                    $rccm->setNumRccmFormalite($rccmFormalite);
                    $rccm->setDossierDemande($dossier);
                    $rccm->setDate(new \DateTime());
                    $em->persist($rccm);
                    $em->flush();
                } else {

                    $rccm = new Rccm();

                    $typeF = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->find($request->get('typeFormalite'));

                    $rccm->setTypeFormaliteRccm($typeF);
                    $rccm->setNumRccmEntreprise($rccmEntreprise);
                    $rccm->setNumRccmFormalite($rccmFormalite);
                    $rccm->setDossierDemande($dossier);
                    $rccm->setDate(new \DateTime());
                    $em->persist($rccm);
                    $em->flush();
                }
            }
            $message = $this->get('translator')->trans('message.ajout_succes ');
            return new RedirectResponse($this->container->get('router')->generate('Formulaire_G1', array('idd' => $idd)));

        }
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/formulaireG1.html.twig', array('idd' => $idd, 'representant' => $representant, 'dossier' => $dossier, 'formeJ' => $formJ,
                    'secAct' => $secAct, 'associe' => $associe, 'administrateurs'=> $administrateurs, 'rccm' => $rccm, 'typeF' => $typeF,
                    'leRepresentant' => $leRepresentant, "formatRccm" => $debutNumRccm, 'debutRccmFormalite' => $debutNumRccmFormalite,
                    "numSequentiel" => $numSequentiel, 'numSequentielEntreprise' => $numSequentielEntreprise, 'message' => $message, 'listeTypF' => $listeTypF, 'motif' => $motif, 'styleNumeroFormalite' => $styleNumeroFormalite, 'styleNumeroEntreprise' => $styleNumeroEntreprise,
                    'statutTraitrement' => $documentCollected->getStatutTraitement()));
    }
    public function visualiserG1Action($idd) {
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
        $representant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($idd, $langue->getId());
        $leRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findOneBy(array('dossierDemande' => $idd));
        $associe = $em->getRepository('BanquemondialeBundle:Associe')->findBy(array('dossierDemande' => $idd));
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneBy(array('dossierDemande' => $idd));
        $typeF = null;
        if ($rccm) {
            $typeF = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->find($rccm->getTypeFormaliteRccm());
        }

        $numSequentiel = "";
        if ($rccm) {
            $tableau = explode('.', $rccm->getNumRccmFormalite());
            if ($tableau) {
                $numSequentiel = ($tableau[3]) ? "/RCCM/" . $tableau[2] . "/" . $tableau[3] : "";
            }
        }

        $dossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
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
        $formJ = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->getLibelleFormeJuridiqueByLanque($langue->getId(), $dossier->getFormeJuridique());
        $secAct = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossier->getSecteurActivite()));
        //die(dump($secAct));                   
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();

$soussigne = $dossier->getSoussigne();
        $profilCreateur = $dossier->getUtilisateur()->getProfile();
        if ($profilCreateur && $profilCreateur->getDescription() == "saisi") {
            if ($dossier->getTypeDossier()->getLibelle() == "Notaire") {
                $soussigne = $dossier->getSoussigne();
            } else {
                if ($representant) {
                    $firstRep = $representant[0];
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
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserG1.html.twig', array('idd' => $idd, 'representant' => $representant
            , 'activites' => $lesActivites, 'dossier' => $dossier, 'formeJ' => $formJ, 'secAct' => $secAct,'libelleSignatureGreffe' => $libelleSignatureGreffe, 'associe' => $associe
            , "numSequentiel" => $numSequentiel, 'rccm' => $rccm, 'typeF' => $typeF, 'leRepresentant' => $leRepresentant, 'soussigne' => $soussigne));
        $nomFichier = "formulaire" . $idd . "_" . $pole->getId() . ".pdf";
        //$nomFichier = $rccm->getNumRccmFormalite() . "_G1.pdf";
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($nomFichier);
        exit;
    }
	public function visualiserChefGreffeG1Action($idd) {
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
		if($parametrageSignature)
		{
			$afficherSignature = $parametrageSignature->getIsSignatureVisible();
			$afficherQRCodeGreffe = $parametrageSignature->getIsQRVisible();
			$libelleSignatureGreffe = $parametrageSignature->getLibelleSignatureGreffe();
		}

        $representant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($idd, $langue->getId());
        $leRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findOneBy(array('dossierDemande' => $idd));
        $associe = $em->getRepository('BanquemondialeBundle:Associe')->findBy(array('dossierDemande' => $idd));
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneBy(array('dossierDemande' => $idd));
        $typeF = null;
        if ($rccm) {
            $typeF = $em->getRepository('BanquemondialeBundle:TypeFormaliteRccm')->find($rccm->getTypeFormaliteRccm());
        }

        $numSequentiel = "";
        if ($rccm) {
            $tableau = explode('.', $rccm->getNumRccmFormalite());
            if ($tableau) {
                $numSequentiel = ($tableau[3]) ? "/RCCM/" . $tableau[2] . "/" . $tableau[3] : "";
            }
        }

        $dossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
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
        $formJ = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->getLibelleFormeJuridiqueByLanque($langue->getId(), $dossier->getFormeJuridique());
        $secAct = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('langue' => $langue->getId(), 'secteurActivite' => $dossier->getSecteurActivite()));
        //die(dump($secAct));                   
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();


        $profilCreateur = $dossier->getUtilisateur()->getProfile();
        if ($profilCreateur && $profilCreateur->getDescription() == "saisi") {
            if ($dossier->getTypeDossier()->getLibelle() == "Notaire") {
                $soussigne = $dossier->getSoussigne();
            } else {
                if ($representant) {
                    $firstRep = $representant[0];
                    //die(dump($firstRep));
                    $civilite = "M.";
                    if ($firstRep['genre'] == "Femme") {
                        $civilite = "Mme.";
                    }
                    $soussigne = $civilite . " " . $firstRep['prenom'] . " " . $firstRep['nom'] . ", " . $firstRep['libelleFonction'];
                }
            }
        }


        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserChefGreffeG1.html.twig', array('idd' => $idd, 'representant' => $representant
            , 'activites' => $lesActivites, 'dossier' => $dossier, 'formeJ' => $formJ, 'secAct' => $secAct, 'associe' => $associe,
			'afficherSignature'=> $afficherSignature, 'afficherQRCodeGreffe'=>$afficherQRCodeGreffe, 'libelleSignatureGreffe' => $libelleSignatureGreffe
            , "numSequentiel" => $numSequentiel, 'rccm' => $rccm, 'typeF' => $typeF, 'leRepresentant' => $leRepresentant, 'soussigne' => $soussigne
			, 'documentValide'=>false));
        $nomFichier = "formulaire" . $idd . "_" . $pole->getId() . ".pdf";
        //$nomFichier = $rccm->getNumRccmFormalite() . "_G1.pdf";
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($nomFichier);
        exit;
    }
}
