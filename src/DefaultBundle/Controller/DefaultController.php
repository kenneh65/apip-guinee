<?php

namespace DefaultBundle\Controller;

use BanquemondialeBundle\Entity\Administration;
use BanquemondialeBundle\Entity\Documentation;
use BanquemondialeBundle\Entity\DossierDemande;
use BanquemondialeBundle\Entity\Quittance;
use BanquemondialeBundle\Form\AnnoncePortailSearchType;
use BanquemondialeBundle\Form\AnnonceurSearchType;
use BanquemondialeBundle\Form\DossierDemandeSearchType;
use BanquemondialeBundle\Form\DossierPoleSearchType;
use BanquemondialeBundle\Form\RepartitionQuittanceSearchType;
use BanquemondialeBundle\Form\StatistiqueGrapheType;
use BanquemondialeBundle\Form\StatistiqueType;
use DefaultBundle\Entity\CaisseOrange;
use DefaultBundle\Entity\PaiementOrange;
use DefaultBundle\Entity\ReglageActivation;
use DefaultBundle\Entity\Statistique;
use DefaultBundle\Form\SimulateurSearchType;
use Endroid\QrCode\QrCode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Client;
use ParametrageBundle\Entity\News;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

// Include the requires classes of Phpword
//include qrcode

class DefaultController extends Controller
{
    /**
     * @Route("/{_locale}/kenneh/test-kenneh",name="kennehTestAction_test")
     */
    public function kennehTestAction(Request $request)
    {

        // // var_dump($paginator);die();
        ///     $em=$this->getDoctrine()->getManager();
//        $connection = $this->getDoctrine()->getConnection();
//        $RAW_QUERYlike = 'SELECT r.idDossierDemande id,r.nom FROM representant r';
//        $statement2 = $connection->prepare($RAW_QUERYlike);
//        $statement2->execute();
        //  $resultlike = $statement2->fetchAll();
        //  $ds =$em->getRepository('BanquemondialeBundle:DossierDemande')->findAll();
//        for ($i=0;$i<count($resultlike);$i++){
//            $representant = $this->get('monServices')->getRepresentanByDossier($em->getRepository('BanquemondialeBundle:DossierDemande')->find($resultlike[$i]['id']));
//            for ($j=0;$j<count($representant);$j++){
//                $entity=$em->getRepository('BanquemondialeBundle:Representant')->findOneById($representant[$j]->getId());
//                if ($j==0){
//                    $entity->setGp(1);
//                    $em->persist($entity);
//                    $em->flush();
//                }
//
//            }
//        }

        ///   $dql   = "SELECT a FROM BanquemondialeBundle:DossierDemande a";
        // Retrieve the entity manager of Doctrine
        $em = $this->getDoctrine()->getManager();
        // Get some repository of data, in our case we have an Appointments entity
        $appointmentsRepository = $em->getRepository(DossierDemande::class);
        // Find all the data on the Appointments table, filter your query as you need
        $allAppointmentsQuery = $appointmentsRepository->createQueryBuilder('p')
            ->getQuery();

        $pagination = $this->get('knp_paginator')->paginate(
            $allAppointmentsQuery,
            $request->query->get('page', 1)/*page number*/,
            25/*limit per page*/
        );
////        if($this->get('monServices')->is_connected()==true)
////        {
//          ( $this->get('monServices')->EnvoiMessage());die();
////        }
        // $this->get('monServices')->EnvoiMessage($em->getRepository('BanquemondialeBundle:Representant')->findOneById(62),'mohamed.kante@apipguinee.com');
        var_dump($this->get('monServices')->getOperationEncour()->getName());

        //('621134693', $em->getRepository('BanquemondialeBundle:Representant')->findOneById(62), 'depot');

        die();
        return $this->render('DefaultBundle:Default:testtkenneh.html.twig', ['dossiers' => $pagination]);

    }

    /**
     * @Route("/contacter",name="nous-contacter")
     */
    public function ContacterAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $messagerie = $em->getRepository('ParametrageBundle:Messagerie')->find(1);

            $transport = \Swift_SmtpTransport::newInstance($messagerie->getMailerHost(), $messagerie->getMailerPort())
                ->setUsername($messagerie->getMailerUser())
                ->setPassword($messagerie->getMailerPassword())->setEncryption($messagerie->getEncryption());

            $mailer = \Swift_Mailer::newInstance($transport);

            $message = \Swift_Message::newInstance($transport);

            $sujet = $request->get('sujet');
            $nom = $request->get('nom');
            $email = $request->get('email');
            $message1 = $request->get('message');
            $message->setSubject($sujet)
                ->setFrom(array($email => $nom))
                ->setTo($messagerie->getExpediteurEmail())
                ->setBody(
                    $this->renderView(
                        'ParametrageBundle:Parametrage:email\contact.email.twig', array('message' => $message1, 'nom' => $nom, 'email' => $email, 'message' => $message, 'sujet' => $sujet)
                    ), 'text/html'
                );
            $mailer->send($message);
            return new JsonResponse(array('resultat' => '1'));
        } else
            return new JsonResponse(array('resultat' => '0'));
    }

    public function adminExist()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository("UtilisateursBundle:Utilisateurs")->findAll();

        if (count($users) > 0)
            return true;
        else
            return false;
    }

    /**
     * @Route("/",name="racine")
     */
    public function racineAction(Request $request)
    {
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/{_locale}",name="accueil")
     */
    public function accueilAction(Request $request)
    {

        if (!$this->adminExist())
            return $this->redirectToRoute('installation');

        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($this->get('request')->getLocale());


        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);

        if ($em->getRepository('DefaultBundle:Statistique')->findByAdresseIp($request->getClientIp()) == null) {
            $statistique = new Statistique($request->getClientIp(), new \Datetime());
            $em->persist($statistique);
            $em->flush();
        }

        if (!$langue) {
            return $this->redirect($this->generateUrl('accueil', array('_locale' => "fr")));
        }

        if ($langue->getCourante() == true) {
            $guides = $em->getRepository("BanquemondialeBundle:Documentation")->getDocumentations();
        } else {
            $guides = $em->getRepository("BanquemondialeBundle:DocumentationTraduction")->getDocumentations($langue);
        }

        $sliders = $em->getRepository('ParametrageBundle:ImageSlider')->getAll();

        if ($langue->getCourante() == true) {
            $news = $em->getRepository('ParametrageBundle:News')->get3LastNews();
        } else {
            $news = $em->getRepository('ParametrageBundle:NewsTraduction')->get3LastNews($langue->getCode());
        }
        //die(dump($guides));
        return $this->render('DefaultBundle:Default:home.html.twig', array('langues' => $lgs, 'guides' => $guides, 'chemin' => $chemin->getNom() . 'Guides\\', 'sliders' => $sliders, 'news' => $news));
    }

    /**
     * @Route("/{_locale}",name="accueil1")
     */
//    public function accueil1Action() {
//        if (!$this->adminExist())
//            return $this->redirectToRoute('installation');
//
//        $em = $this->getDoctrine()->getManager();
//
//        $guides = $em->getRepository("BanquemondialeBundle:Documentation")->getDocumentations();
//        $news = $em->getRepository('ParametrageBundle:News')->getFiveLastNews();
//        return $this->render('DefaultBundle:Default:home.html.twig', array('news' => $news, 'guides' => $guides));
//    }

    /**
     * @Route("/{_locale}/guides",name="guides")
     */
    public function guidesAction(Request $request)
    {

    }

    /**
     * @Route("/{_locale}/faq",name="faq")
     */
    public function faqAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $code = $request->getLocale();

        if ($code == 'fr')
            $faqs = $em->getRepository("ParametrageBundle:FAQ")->findAll();
        else
            $faqs = $em->getRepository("ParametrageBundle:FAQTraduction")->findByLangue($em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($code));

        return $this->render('DefaultBundle:Default:faq.html.twig', array('faqs' => $faqs, 'langues' => $lgs));
    }

    /*
     * @Route("/{_locale}/annonces-legales",name="annonces_legales")
     * */

    public function annoncelegaleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $entreprises = $em->getRepository("BanquemondialeBundle:DossierDemande")->findBy(array('statut' => 2));
        $langue = $em->getRepository("BanquemondialeBundle:Langue")->findByCode($request->getLocale());
        $formesJuridiques = $em->getRepository("BanquemondialeBundle:FormeJuridiqueTraduction")->findByLangue($langue);


        return $this->render('DefaultBundle:Default:annonces-legales.html.twig', array('annonces' => $entreprises, 'formes' => $formesJuridiques, 'langues' => $lgs
        ));
    }

    /**
     * @Route("/{_locale}/guides",name="guides")
     */
    public function guideAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($request->getLocale());
        if (strtolower($langue->getCode()) == 'fr')
            $guides = $em->getRepository("BanquemondialeBundle:Documentation")->findAll();
        else
            $guides = $em->getRepository("BanquemondialeBundle:DocumentationTraduction")->findByLangue($langue);

        return $this->render('DefaultBundle:Default:guides.html.twig', array('guides' => $guides, 'langues' => $lgs, 'chemin' => ucfirst($chemin->getNom() . 'Guides\\')));
    }

    /**
     * @Route("/{_locale}/liens-utils",name="liens-utils")
     */
    public function lienUtilAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($request->getLocale());
        if (strtolower($langue->getCode()) == 'fr') {
            $liens = $em->getRepository('BanquemondialeBundle:Liensutiles')->findAll();
        } else
            $liens = $em->getRepository('BanquemondialeBundle:LiensutilesTraduction')->findByLangue($langue);

        $langue = $em->getRepository("BanquemondialeBundle:Langue")->findByCode($request->getLocale());
        return $this->render('DefaultBundle:Default:liens-utils.html.twig', array('liens' => $liens, 'langues' => $lgs
        ));
    }

    /*
     * @Route("/{_locale}/recherche-annonce",name="recherche_annonce")
     *
      public function annoncelegaleSearchAction(Request $request) {
      $em = $this->getDoctrine()->getManager();
      $dateCreation = $request->get('dateCreation');
      $forme = $request->get('forme');
      $critere = $request->get('critere');


      $entreprises = $em->getRepository("BanquemondialeBundle:DossierDemande")->findByParametres($critere, $forme, $dateCreation);
      $langue = $em->getRepository("BanquemondialeBundle:Langue")->findByCode($request->getLocale());
      $formesJuridiques = $em->getRepository("BanquemondialeBundle:FormeJuridiqueTraduction")->findByLangue($langue);
      return $this->render('DefaultBundle:Default:annonces-legales.html.twig', array('annonces' => $entreprises, 'formes' => $formesJuridiques
      ));
      }
     */

    /*
     * @Route("/{_locale}/recherche-nom",name="recherche_nc")
     *
      public function nomCommercialSearchAction(Request $request) {
      $em = $this->getDoctrine()->getManager();
      $nomCommercial = $request->get('nomCommercial');


      $entreprises = $em->getRepository("BanquemondialeBundle:DossierDemande")->findByNomCommercial($nomCommercial);
      return $this->render('DefaultBundle:Default:resultat-nom.html.twig', array('entreprises' => $entreprises,'nomCommercial'=>$nomCommercial
      ));
      }
     */

    public function menuAction($active)
    {

        //die(dump($active));
        return $this->render('DefaultBundle:Default:menu.html.twig', array('active' => $active));
    }

    public function menuAdminAction($active)
    {
        $em = $this->getDoctrine()->getManager();
        $messagerie = $em->getRepository('ParametrageBundle:Messagerie')->find(1);
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $profilName = "";
        $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
        if ($user->getProfile()) {
            $profil = $user->getProfile()->getDescription();
            if ($pole == $poleAPIP) {
                if ($profil && ($profil == " dépot" || $profil == "saisi" || $profil == "help" || $profil == "Service retrait")) {
                    $profilName = $profil;
                }
//                if (strpos($profil, 'dépot') == true) {
//                    $profilName = $profil;
//                } elseif ($profil === 'saisi') {
//                    $profilName = $profil;
//                }
            }
        }

        if (!$messagerie) {
            return $this->render('DefaultBundle:Default:menuAdmin.html.twig', array('active' => $active, 'messagerie' => false, 'profilName' => $profilName));
        }
        return $this->render('DefaultBundle:Default:menuAdmin.html.twig', array('active' => $active, 'messagerie' => true, 'profilName' => $profilName));
    }

    public function messagerieAction()
    {
        $em = $this->getDoctrine()->getManager();
        $messagerie = $em->getRepository('ParametrageBundle:Messagerie')->find(1);

        if (!$messagerie)
            return new Response('<div class="alert alert-danger">
					<h3>La configuration n\'est pas complete</h3>
					<ul>
						<li><a href = ' . $this->generateUrl('messagerie_edit') . '>Veuillez renseigner les informations sur le serveur messagerie</a></li>
					</ul>			
				</div>');
        return new Response('');
    }

    /**
     * @Route("/{_locale}/admin",name="administration")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }

        //$texte = $this->get('translator')->trans('envoi_email_soumission_saisie',array('%numeroDossier%' => $numeroDossier));
        //die(dump($texte));


        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $pole = $user->getPole();
        $profilName = "";
        $depot = false;
        $retrait = false;
        $nbreDocRetrait = 0;
        $nbreDocRetraitPartiel = 0;
        $nbreDocRetirer = 0;
        $nbreDocDepot = 0;
        $nbreDocDepotModification = 0;

        $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
        if ($user->getProfile()) {
            $profil = $user->getProfile()->getDescription();

            $profilName = $profil;
            //die(dump($profilName));
            //die(dump(strpos($profil, 'retrait')));
            if ($pole == $poleAPIP) {
                $profilName = $profil;
                if (strpos($profil, 'dépot') == true) {

                    $depot = true;

                    $listerdepot = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDemandeDossierDeposesByParametres(null, $langue->getId(), $user->getId(), null, -1);
                    $nbreDocDepot = count($listerdepot);
                    $listerdepotModification = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDemandeDossierDepotByParametres(null, $langue->getId(), $user->getId(), null, -2);
                    $nbreDocDepotModification = count($listerdepotModification);
                } else if (strpos($profil, 'retrait') > 0) {
                    $pole = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle("GF");
                    $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
                    if ($pole) {
                        $idPole = $pole->getId();
                    }
                    $retrait = true;

                    $idS = $user->getEntreprise()->getId();
                    $listerRetrait = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPoleRetrait($user, null, $langue->getId(), $idPole, null, 2, $idS);
                    $listerRetraitPartiel = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPartielPoleRetrait($user, null, $langue->getId(), $idPole, null, 2, $idS);
                    $listerRetirer = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPoleRetire($user, null, $langue->getId(), $idPole, null, 2, $idS);
                    $nbreDocRetrait = count($listerRetrait);

                    $nbreDocRetraitPartiel = count($listerRetraitPartiel);
                    //die(dump($nbreDocRetrait));
                    $nbreDocRetirer = count($listerRetirer);

                    //die(dump($nbreDocRetirer));
                }
            }
        }
        $nbreDocEnCours = 0;
        $nbreDocDelivre = 0;
        $nbreDocModifie = 0;
        $nbreDocBrouillon = 0;
        $nbreDocSuivi = 0;
        $nbrDocAValider = 0;
        $nbrDocValide = 0;

        if ($pole && $pole->getSigle() == "AL") {
            $dossierEnCours = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierAnnonceurEncours($user);
            $dossiersDelivre = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierAnnonceurDelivre($user);

            $nbreDocEnCours = count($dossierEnCours);
            $nbreDocDelivre = count($dossiersDelivre);
        } else if ($pole && $pole->getSigle() == "CT") {
            $dossierEnCours = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceEnAttenteByParametres(null, $langue->getId(), $user->getId());
            $dossiersDelivre = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceValideByParametres(null, $langue->getId(), $user->getId(), null);
            if ($user->getEntreprise()) {
                $dossierEnCours = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceEnAttenteByParametres(null, $langue->getId(), $user->getId(), null, $user->getEntreprise()->getSousPrefecture()->getId());
            }
            if ($user->getEntreprise() && $user->getEntreprise()->getIsSiege() == false) {

                $dossiersDelivre = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceValideByParametres(null, $langue->getId(), $user->getId(), null, $user->getEntreprise()->getSousPrefecture()->getId());
            }
            $nbreDocEnCours = count($dossierEnCours);
            $nbreDocDelivre = count($dossiersDelivre);
        } else if ($pole && strtolower($user->getProfile()->getDescription()) != "gec") {
            $dossierEnCours = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierEncours($user);
            $dossiersDelivre = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierDelivre($user);
            $dossiersModifie = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierModifie($user);
            $dossiersBrouillon = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDossierBrouillon($user);
            $dossiersSuivi = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDossierSuivi($user);


            $nbreDocEnCours = count($dossierEnCours);
            $nbreDocDelivre = count($dossiersDelivre);
            $nbreDocModifie = count($dossiersModifie);
            $nbreDocBrouillon = count($dossiersBrouillon);
            $nbreDocSuivi = count($dossiersSuivi);
            if ($user->getEntreprise() && $user->getProfile() && $user->getProfile()->getDescription() == "help") {
                $isSiege = $user->getEntreprise()->getIsSiege();
                $dossiershelp = $em->getRepository('BanquemondialeBundle:DossierDemande')->getNbreDossierHelpDesk($user->getEntreprise()->getId(), $isSiege);
                $nbreDocSuivi = count($dossiershelp);
            }
        } else if ($pole && strtolower($user->getProfile()->getDescription()) == "gec") {
            $dossierAValiderGreffe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierGreffeAValider($user);
            $dossierValideGreffe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierGreffeValide($user);

            $nbrDocAValider = count($dossierAValiderGreffe);
            $nbrDocValide = count($dossierValideGreffe);


        } else {
            $dossiersBrouillon = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDossierBrouillon($user);
            $dossiersSuivi = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDossierSuivi($user);

            $nbreDocBrouillon = count($dossiersBrouillon);
            $nbreDocSuivi = count($dossiersSuivi);
        }

        $statistique = 0;
        $listeNbreDossierEncoursByPole = null;
        $listeNbreDossierDelivreByPole = null;
        $listeNbreDossierEnModifByPole = null;
        $usersEnligne = null;
        $dd = new \DateTime();
        if ($user->hasRole('ROLE_SUPER_ADMIN')) {
            $statistique = sizeof($em->getRepository('DefaultBundle:Statistique')->findAll());

            $usersEnligne = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findByLastLogin($dd);

            //$usersEnligne=$em->getRepository('UtilisateursBundle:Utilisateurs')->findByLastLogin($dd);
            $listeNbreDossierEncoursByPole = $em->getRepository('BanquemondialeBundle:DocumentCollected')->listeDossiersGroupByPole(1);
            $listeNbreDossierDelivreByPole = $em->getRepository('BanquemondialeBundle:DocumentCollected')->listeDossiersGroupByPole(2);
            $listeNbreDossierEnModifByPole = $em->getRepository('BanquemondialeBundle:DocumentCollected')->listeDossiersGroupByPole(3);
        }
        if ($user->getEntreprise() && $user->getProfile() && $user->getProfile()->getDescription() == "help") {
            $listeNbreDossierEncoursByPole = $em->getRepository('BanquemondialeBundle:DocumentCollected')->listeDossiersPoleByAntenne(1, $user->getEntreprise()->getIsSiege(), $user->getEntreprise()->getId());
            $listeNbreDossierDelivreByPole = $em->getRepository('BanquemondialeBundle:DocumentCollected')->listeDossiersPoleByAntenne(2, $user->getEntreprise()->getIsSiege(), $user->getEntreprise()->getId(), $dd);
            $listeNbreDossierEnModifByPole = $em->getRepository('BanquemondialeBundle:DocumentCollected')->listeDossiersPoleByAntenne(3, $user->getEntreprise()->getIsSiege(), $user->getEntreprise()->getId());
            //  $listeNbreDossierRetraitPartiel = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPartielPoleRetrait($user, null, $langue->getId(), 2, null, 2, null);
            //  $serviceListeRetraipartiel=$this->get('monservices')->getlisteNbreDossierRetraitPartiel($user,null, 1,2, 200, 2 , null);
            //  var_dump(count($serviceListeRetraipartiel));die();
        }
        return $this->render('DefaultBundle:Default:index.html.twig', array(
            'nbreDocEnCours' => $nbreDocEnCours,
            'StatNbreDossierBYPole' => $listeNbreDossierEncoursByPole,
            'StatDossierDelivre' => $listeNbreDossierDelivreByPole,
            'StatDossierEnModif' => $listeNbreDossierEnModifByPole,
            'usersEnligne' => $usersEnligne,
            'nbreVisite' => $statistique,
            'nbreDocDelivre' => $nbreDocDelivre,
            'nbreDocModifie' => $nbreDocModifie,
            'nbreDocBrouillon' => $nbreDocBrouillon,
            'nbreDocSuivi' => $nbreDocSuivi,
            'profilName' => $profilName,
            'depot' => $depot,
            'retrait' => $retrait,
            'nbreDocRetrait' => $nbreDocRetrait,
            'nbreDocRetraitPartiel' => $nbreDocRetraitPartiel,
            'nbreDocRetirer' => $nbreDocRetirer,
            'nbreDocDepot' => $nbreDocDepot,
            'nbreDocDepotModification' => $nbreDocDepotModification,
            'nbrDocAValider' => $nbrDocAValider,
            'nbrDocValide' => $nbrDocValide,
            //'listeNbreDossierRetraitPartiel'=>count($serviceListeRetraipartiel)
        ));
    }

    /**
     * @Route("/{_locale}/detailsDossierPole/{idP}/{idS}",name="detailsDossierPole")
     *
     */
    public function detailsPoleAction($idP, $data = null, $idS = 1)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $listeDossiers = null;
        /*if($idP==3){
            $listeDossiers0 = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossiersCaisse2(null,$langue->getId(),$idP,25,$idS);
        //$listeDossiers0 = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossiersCaisse1();
        
        }*/
        if ($user->getEntreprise() && $user->getProfile() && $user->getProfile()->getDescription() == "help") {
            $listeDossiers = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPoleSuperviseur($user, null, $langue->getId(), $idP, 25, $idS, $user->getEntreprise()->getIsSiege(), $user->getEntreprise()->getId());
        } else {
            if ($idP == 3) {
                $listeDossiers = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossiersCaisse2(null, $langue->getId(), $idP, 25, $idS);

            } else {
                $listeDossiers = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPoleStat($user, null, $langue->getId(), $idP, 25, $idS);
            }
        }
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['dossiersPole'];
            if ($user->getEntreprise() && $user->getProfile() && $user->getProfile()->getDescription() == "help") {
                $listeDossiers = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPoleSuperviseur($user, $data, $langue->getId(), $idP, null, $idS, $user->getEntreprise()->getIsSiege(), $user->getEntreprise()->getId());
            } else {
                if ($idP == 3) {
                    //die(dump($data));
                    $listeDossiers = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossiersCaisse2($data, $langue->getId(), $idP, null, $idS);
                } else {
                    $listeDossiers = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPoleStat($user, $data, $langue->getId(), $idP, null, $idS);
                }
                //$listeDossiers = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPoleStat($user, $data, $langue->getId(), $idP, null, $idS);
            }
            //$nbre=count($listeDossiers);
            //die(dump($nbre));
        }


        $form = $this->createForm(new \BanquemondialeBundle\Form\DossierPoleSearchType(array('langue' => $langue)));
        $pole = $em->getRepository('ParametrageBundle:Pole')->find($idP);
        $form->bind($request);
        return $this->render('DefaultBundle:Default:detailsDossierPole.html.twig', array('pole' => $pole, 'form' => $form->createView(), 'listerdemande' => $listeDossiers, 'langue' => $langue->getId(), 'idp' => $idP, 'idS' => $idS));
    }

    /**
     * @Route("/{_locale}/contacts",name="contacts")
     */
    public function contactAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $code = $request->getLocale();
        if ($code == 'fr')
            $contacts = $em->getRepository('ParametrageBundle:Contact')->findAll();
        else
            $contacts = $em->getRepository('ParametrageBundle:ContactTraduction')->findByLangue($em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($code));

        return $this->render('DefaultBundle:Default:contacts.html.twig', array('contacts' => $contacts, 'langues' => $lgs));
    }

    /**
     * @Route("{_locale}/news",name="news")
     */
    public function newsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($this->get('request')->getLocale());
        if ($langue->getCourante() == true)
            $dql = "SELECT a FROM ParametrageBundle:News a where a.date_publication <=CURRENT_TIMESTAMP() and a.date_expiration >=CURRENT_TIMESTAMP()";
        else
            $dql = "SELECT t FROM ParametrageBundle:NewsTraduction t join  t.news a join t.langue l where a.date_publication <=CURRENT_TIMESTAMP() and l.code ='" . $request->getLocale() . "' and a.date_expiration >=CURRENT_TIMESTAMP()";

        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */ $request->query->getInt('page', 1)/* page number */, 5/* limit per page */
        );

        return $this->render('DefaultBundle:Default:news.html.twig', array('news' => $pagination, 'langues' => $lgs));
    }

    /**
     * @Route("{_locale}/forme-juridique",name="forme-juridique")
     */
    public function formesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();

        $simulation = null;
        $listePiece = null;
        $fraisApip = null;
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($this->get('request')->getLocale());

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['simulation'];
            $idFormeJuridique = $data['formeJuridique'];
            $idTypeDossier = $data['typeDossier'];
            $formeJuridique = $em->getRepository('BanquemondialeBundle:FormeJuridique')->find($idFormeJuridique);
            $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
            $fraisApip = $em->getRepository('ParametrageBundle:Tarification')->getMontantFraisByPole($idFormeJuridique, $idTypeDossier, $poleAPIP->getId());

            $simulation = $em->getRepository('BanquemondialeBundle:Circuit')->getSimulationByFormeAndTypeDossier($idFormeJuridique, $idTypeDossier, $langue);
            $listePiece = $em->getRepository('BanquemondialeBundle:PieceJointe')->findPieceByForme($idFormeJuridique, $langue);
            //die(dump(count($simulation)));
        }
        $form = $this->createForm(new SimulateurSearchType(array('langue' => $langue)));
        //die(dump($form));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:formes.html.twig', array('langues' => $lgs, 'fraisApip' => $fraisApip, 'simulation' => $simulation, 'delimitation' => count($simulation), 'listePiece' => $listePiece, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/plus/{id}",name="readmore")
     */
    public function readMoreAction(Request $request, News $news)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($this->get('request')->getLocale());

        if ($langue->getCourante() == false) {
            $news = $em->getRepository('ParametrageBundle:NewsTraduction')->findOneByNews($news);
        }

        return $this->render('DefaultBundle:Default:readmore.html.twig', array('news' => $news, 'langues' => $lgs));
    }

    /**
     * @Route("/{langue}",name="changer_langue")
     */
    public function changeLanguageAction($langue)
    {
        return $this->redirect($this->generateUrl('accueil1', array('_locale' => $langue)));
    }

    /**
     * @Route("/{_locale}/parametrage/inscription",name="installation")
     */
    public function installationAction(Request $request)
    {
        if ($this->adminExist())
            return $this->redirectToRoute('accueil');

        $em = $this->getDoctrine()->getManager();

        $administration = new Administration();

        $form = $this->createForm('BanquemondialeBundle\Form\AdministrationInstallationType', $administration, array('locale' => $request->getLocale()));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $administration->getUtilisateur()->setEnabled(true);
            $administration->getUtilisateur()->setFirstLog(false);
            $administration->getUtilisateur()->setRoles(array('ROLE_SUPER_ADMIN'));

            $em->persist($administration->getUtilisateur());
            $em->persist($administration);
            $em->flush();
            $translated = $translated = $this->get('translator')->trans("L'administration a été ajouté avec succès.");
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirectToRoute('accueil');
        }

        return $this->render('DefaultBundle:Default:install.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/charge/region", name="charge_region")
     */
    public function chargeRegionAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();

            $paysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->find($request->get('idpays'));

            $pays = $paysTraduction->getPays();

            $langue = $paysTraduction->getLangue();

            if (!$pays)
                return new JsonResponse(array('error' => '1'));

            //$regions = $em->getRepository('BanquemondialeBundle:Region')->getRegionByPays2($pays->getId());
            $regions = $em->getRepository('BanquemondialeBundle:Region')->findByPays($pays);


            $retour = array();
            $retourId = array();

            $i = 0;

            foreach ($regions as $region) {

                $retour[$i] = $region->getLibelle();
                $retourId[$i] = $region->getId();
                $i++;
            }

            return new JsonResponse(array('retour' => $retour, 'retourId' => $retourId));
        } else
            return new JsonResponse();
    }

    /**
     * @Route("/charge/departement", name="charge_departement")
     */
    public function chargeDepartementAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $region = $em->getRepository('BanquemondialeBundle:Region')->find($request->get('region'));

            if (!$region)
                return new JsonResponse(array('error' => '1'));

            $departements = $em->getRepository('BanquemondialeBundle:Departement')->findByRegion($region);

            $retour = array();
            $retourId = array();

            $i = 0;

            foreach ($departements as $departement) {
                $retour[$i] = $departement->getLibelle();
                $retourId[$i] = $departement->getId();
                $i++;
            }

            return new JsonResponse(array('retour' => $retour, 'retourId' => $retourId));
        } else
            return new JsonResponse();
    }

    /**
     * @Route("/{_locale}/annonces-legales",name="annonces_legales")
     */
    public function VisualiserAction()
    {

        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($this->get('request')->getLocale());


        $data = NULL;
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findAnnonceLegaleByParametres($data, $idCodeLangue, 25);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['dossierEncours'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findAnnonceLegaleByParametres($data, $idCodeLangue);
        }

        $form = $this->createForm(new AnnoncePortailSearchType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('BanquemondialeBundle:Default:AnnonceLegale/layout/Visualiser.html.twig', array('form' => $form->createView(), 'listerdemande' => $listerdemande, 'langue' => $idCodeLangue, 'langues' => $lgs));
    }

    /**
     * @Route("/{_locale}/reportAnnonce/{idd}",name="ReportAnnonceLegale")
     */
    public function ReportAnnonceAction($idd)
    {
        $em = $this->container->get('doctrine')->getManager();
        $codLang = 'fr';
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();
        $data['id'] = $idd;
        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findAnnonceLegaleByParametres($data, $idCodeLangue);
        //die(dump($listerdemande));


        $this->annonceLegalePdfPortailAction($listerdemande);
    }

    /**
     * @Route("/{_locale}/nom-commercial",name="recherche_nom_commercial")
     */
    public function nomCommercialAction()
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }

        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();
        $listerdemande = NULL;
        $nomCommercial = NULL;
        $nomUtilise = false;

        $filters = null;
        $start = null;
        $length = null;

        $actuels = null;
        if ($request->getMethod() == 'POST') {
            $nomCommercial = $request->request->get("nomCommercial");

            $archives = $em->getRepository('ParametrageBundle:ArchiveNomCommerciaux')->archiveNomCommercialExiste($nomCommercial);
            if (count($archives) > 0) {
                $nomUtilise = true;
            } else {
                $actuels = $em->getRepository('BanquemondialeBundle:DocumentCollected')->nomCommercialExiste($nomCommercial);
                if (count($actuels) > 0) {
                    $nomUtilise = true;
                }
            }
        }
        $form = $this->createForm(new DossierDemandeSearchType(array('langue' => $langue)));
        $form->bind($request);
        $couleurMessage = 'info';
        $message = $this->get('translator')->trans("nom_commercial_disponible");
        if ($nomUtilise == true) {
            $couleurMessage = 'error';
            $message = $this->get('translator')->trans("nom_commercial_indisponible");
        }
        $this->get('session')->getFlashBag()->add($couleurMessage, $message);

        return $this->render('BanquemondialeBundle:Default:AnnonceLegale/layout/rechercheNomCommercial.html.twig', array('langues' => $lgs, 'form' => $form->createView(), 'listerdemande' => $listerdemande, 'couleurMessage' => $couleurMessage, 'message' => $message, 'searchTerm' => $nomCommercial, 'langue' => $idCodeLangue, 'dossiersActuels' => $actuels));
    }

    /**
     * @Route("/{_locale}/nom-commercial-update",name="recherche_nom_commercial_Upadate")
     */
    public function nomCommercialUpdateAction()
    {
        //  var_dump('oj');die();
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }

        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();
        $listerdemande = NULL;
        $nomCommercial = NULL;
        $nomUtilise = false;

        $filters = null;
        $start = null;
        $length = null;
        $actuels = null;
        $resultaRecherNomCommercialUpdate = [];
        if ($request->getMethod() == 'POST') {
            $nomCommercial = $request->request->get("nomCommercial");

            $resultaRecherNomCommercialUpdate = $em->getRepository('BanquemondialeBundle:DossierDemande')->recherNomcommercialUpdate(true, $nomCommercial);
            if (count($resultaRecherNomCommercialUpdate) > 0) {
                $nomUtilise = true;
            } else {
                $actuels = $em->getRepository('BanquemondialeBundle:DocumentCollected')->nomCommercialExiste($nomCommercial);
                if (count($actuels) > 0) {
                    $nomUtilise = true;
                }
                $resultaRecherNomCommercialUpdate = $em->getRepository('BanquemondialeBundle:DossierDemande')->recherNomcommercialUpdate(false, $nomCommercial);
            }
        }
        $form = $this->createForm(new DossierDemandeSearchType(array('langue' => $langue)));
        $form->bind($request);
        $couleurMessage = 'info';
        $message = $this->get('translator')->trans("nom_commercial_disponible");
        if ($nomUtilise == true) {
            $couleurMessage = 'error';
            $message = $this->get('translator')->trans("nom_commercial_indisponible");
        }
        $this->get('session')->getFlashBag()->add($couleurMessage, $message);
        return $this->render('BanquemondialeBundle:Default:AnnonceLegale/layout/rechercheNomCommercial-update.html.twig', array(
            'langues' => $lgs,
            'form' => $form->createView(),
            'listerdemande' => $listerdemande,
            'couleurMessage' => $couleurMessage,
            'message' => $message,
            'searchTerm' => $nomCommercial,
            'langue' => $idCodeLangue,
            'dossiersActuels' => $actuels,
            'archives' => $resultaRecherNomCommercialUpdate

        ));
    }

    /**
     * @Route("/{_locale}/cartographie",name="cartographie")
     */
    public function cartographieAction()
    {

        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }

        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $investisseurs = $em->getRepository('BanquemondialeBundle:DossierDemande')->getlistInvestisseurs($langue->getId());

        return $this->render('DefaultBundle:Default:cartographie.html.twig', array('investisseurs' => $investisseurs, 'langues' => $lgs));
    }

    /**
     * @Route("/{_locale}/diaspora",name="diaspora")
     */
    public function diasporaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $code = $request->getLocale();
        /* 		
          if ($code == 'fr')
          $contacts = $em->getRepository('ParametrageBundle:Contact')->findAll();
          else
          $contacts = $em->getRepository('ParametrageBundle:ContactTraduction')->findByLangue($em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($code));
         */
        return $this->render('DefaultBundle:Default:diaspora.html.twig', array('langues' => $lgs));
    }

    /**
     * @Route("/{_locale}/statistiques-periode",name="statistiques-periode")
     */
    public function statistiquesAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $idFormeJuridique = 1;
        $datedebut = null;
        $datefin = null;

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParFormJuridiqueAll($datedebut, $datefin);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $datedebut = $data['dateCreationDebut'];
            $datefin = $data['dateCreationFin'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParFormJuridiqueAll($datedebut, $datefin);
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $this->get('translator')->trans($demande[0]) . "(" . $demande[1] . ")";
            $tabResult[$i][1] = $demande[2];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-repartition",name="statistiques-repartition")
     */
    public function statistiques2Action()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();

        $datedebut = null;
        $datefin = null;

        // die("test");

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParFormJur($datedebut, $datefin);
        $listeSecteur = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseSecteur($datedebut, $datefin, $idLangue);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            //die(dump($data));
            $datedebut = $data['dateCreationDebut'];
            $datefin = $data['dateCreationFin'];
            //die(dump($datedebut));
            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParFormJur($datedebut, $datefin);

            $listeSecteur = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseSecteur($datedebut, $datefin, $langue->getId());
        }


        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        $tabCategorie = array();

        $tabCategorie[0] = $this->get('translator')->trans("janvier");
        $tabCategorie[1] = $this->get('translator')->trans("fevrier");
        $tabCategorie[2] = $this->get('translator')->trans("mars");
        $tabCategorie[3] = $this->get('translator')->trans("avril");
        $tabCategorie[4] = $this->get('translator')->trans("mai");
        $tabCategorie[5] = $this->get('translator')->trans("juin");
        $tabCategorie[6] = $this->get('translator')->trans("juillet");
        $tabCategorie[7] = $this->get('translator')->trans("aout");
        $tabCategorie[8] = $this->get('translator')->trans("septembre");
        $tabCategorie[9] = $this->get('translator')->trans("octobre");
        $tabCategorie[10] = $this->get('translator')->trans("novembre");
        $tabCategorie[11] = $this->get('translator')->trans("decembre");
        // var_dump(json_encode($listeSecteur));
        // exit();
        return $this->render('DefaultBundle:Default:statistiques2.html.twig', array('tabCategorie' => $tabCategorie, 'tabSerie' => $listerdemande, 'datedebut' => $datedebut, 'datefin' => $datefin, 'tabSerieSecteur' => $listeSecteur, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-annuelles",name="statistiques-annuelles")
     */
    public function statistiques3Action()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $serieTitle = $this->get('translator')->trans("message_nombre_creation");

        $datedebut = null;
        $datefin = null;

        $categorie = $em->getRepository('BanquemondialeBundle:DossierDemande')->getlistAnne($datedebut, $datefin);
        $valueParAnne = $em->getRepository('BanquemondialeBundle:DossierDemande')->getValueParPays($datedebut, $datefin, $serieTitle);

        //die(dump($valueParAnne));


        $form = $this->get('form.factory')->createNamedBuilder('form_statistiques3', 'form', null, array())
            ->add('dateCreationDebut', 'text', array('max_length' => 4, 'required' => false, 'pattern' => '\d{4}', 'attr' => array('placeholder' => 'YYYY')))
            ->add('dateCreationFin', 'text', array('max_length' => 4, 'required' => false, 'pattern' => '\d{4}', 'attr' => array('placeholder' => 'YYYY')))
            ->getForm();

        if ($request->getMethod() == 'POST') {

            $form->submit($request);
            if ($form->isValid()) {
                $postData = current($request->request->all());

                $datedebut = $postData['dateCreationDebut'];
                $datefin = $postData['dateCreationFin'];
            }

            $categorie = $em->getRepository('BanquemondialeBundle:DossierDemande')->getlistAnne($datedebut, $datefin);
            $valueParAnne = $em->getRepository('BanquemondialeBundle:DossierDemande')->getValueParPays($datedebut, $datefin, $serieTitle);
        }


        return $this->render('DefaultBundle:Default:statistiques3.html.twig', array('categorie' => $categorie, 'tabResultJS' => $valueParAnne, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/menuLogo",name="menuLogo")
     */
    public function changerLogoAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $tailleMaximum = 20000000;
        $message = "";
        $maxwidth = 300;
        $maxheight = 250;

        if ($request->getMethod() == 'POST') {
            if (isset($_FILES['fileLogo'])) {
                $_FILES['fileLogo']['name'];

                if ($_FILES['fileLogo']['type'] != "image/jpeg") {
                    $message = $this->get('translator')->trans("message_fichier_non_jpg");
                    return $this->render('DefaultBundle:Default:menuLogo.html.twig', array('message' => $message));
                }

                $image_sizes = getimagesize($_FILES['fileLogo']['tmp_name']);

                if ($image_sizes[0] > $maxwidth OR $image_sizes[1] > $maxheight) {
                    $message = $this->get('translator')->trans("message_logo_mauvaise_dimensions");
                    return $this->render('DefaultBundle:Default:menuLogo.html.twig', array('message' => $message));
                }

                if ($_FILES['fileLogo']['size'] > $tailleMaximum) {
                    $message = $this->get('translator')->trans("message_fichier_trop_volumineux");
                    return $this->render('DefaultBundle:Default:menuLogo.html.twig', array('message' => $message));
                }

                //$chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
                //$cheminUpload = $chemin->getNom(); 
                $cheminUpload = "../web/uploads/";
                //die(dump($cheminUpload));
                $temp = $cheminUpload . "logo";
                if (!is_dir($temp) && $_FILES['fileLogo']['tmp_name']) {
                    mkdir($temp);
                }
                $resultat = move_uploaded_file($_FILES['fileLogo']['tmp_name'], $temp . "/logobm.jpg");
            } else
                if (isset($_FILES['fileLogo1'])) {
                    $_FILES['fileLogo1']['name'];

                    if ($_FILES['fileLogo1']['type'] != "image/jpeg") {
                        $message = $this->get('translator')->trans("message_fichier_non_jpg");
                        return $this->render('DefaultBundle:Default:menuLogo.html.twig', array('message' => $message));
                    }

                    $image_sizes = getimagesize($_FILES['fileLogo1']['tmp_name']);

                    if ($image_sizes[0] > $maxwidth OR $image_sizes[1] > $maxheight) {
                        $message = $this->get('translator')->trans("message_logo_mauvaise_dimensions");
                        return $this->render('DefaultBundle:Default:menuLogo.html.twig', array('message' => $message));
                    }

                    if ($_FILES['fileLogo1']['size'] > $tailleMaximum) {
                        $message = $this->get('translator')->trans("message_fichier_trop_volumineux");
                        return $this->render('DefaultBundle:Default:menuLogo.html.twig', array('message' => $message));
                    }

                    //$chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
                    //$cheminUpload = $chemin->getNom();
                    $cheminUpload = "../web/uploads/";
                    //die(dump($cheminUpload));
                    $temp = $cheminUpload . "logo";
                    if (!is_dir($temp) && $_FILES['fileLogo1']['tmp_name']) {
                        mkdir($temp);
                    }


                    $resultat = move_uploaded_file($_FILES['fileLogo1']['tmp_name'], $temp . "/logobm1.jpg");
                }

            if ($resultat) {
                $message = $this->get('translator')->trans("message_logo_remplace_succes");
            } else {
                $message = $this->get('translator')->trans("message_logo_remplace_erreur");
            }


            //die(dump($_FILES['fileLogo']));
        }

        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();


        return $this->render('DefaultBundle:Default:menuLogo.html.twig', array('message' => $message, 'langues' => $langues));
    }

    /**
     * @Route("/{_locale}/guide/{id}",name="visualiserGuide")
     */
    public function viewGuideAction(Request $request, Documentation $guide)
    {

        $em = $this->getDoctrine()->getManager();
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminUpload = $chemin->getNom(); //a modifier par la requete vers la bdd
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        if ($codLang == 'fr') {
            $nom = $guide->getTitre() . '.pdf';
            $path = $cheminUpload . "Guides\\" . $nom;
        } else {
            $doc = $em->getRepository('BanquemondialeBundle:DocumentationTraduction')->findOneBy(array('langue' => $langue, 'documentation' => $guide));
            $nom = $doc->getTitre() . '_other.pdf';

            $path = $cheminUpload . "Guides\\" . $nom;
        }
        $response = new BinaryFileResponse($path);


        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE, //ResponseHeaderBag::DISPOSITION_ATTACHMENT pour download DISPOSITION_INLINE pour render
            $nom
        );


        return $response;
    }

    /**
     * @Route("/exporter/excell-statistique-1",name="export_statistique_creation")
     */
    public function exportStatistiqueCreationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject($this->get('kernel')->getRootDir() . '\..\web\Export\typesEntreprisesCreation.xlsx');

        $datedebut = null;
        $datefin = null;

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParFormJuridiqueAll($datedebut, $datefin);


        $titre = $this->get('translator')->trans("nom_application");

        $phpExcelObject->getProperties()->setCreator($titre)
            ->setLastModifiedBy($titre)
            ->setTitle("")
            ->setSubject("")
            ->setDescription("Ce document contient la liste")
            ->setKeywords("office 2016 openxml php")
            ->setCategory("");

        $i = 2;
        foreach ($listerdemande as $demande) {

            $nombre = 0;
            foreach ($demande as $data) {
                $nombre += $data;
            }
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $demande[0])
                ->setCellValue('B' . $i, $demande[1]);

            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Simple');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet

        $phpExcelObject->setActiveSheetIndex(0);
        // create the writer

        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');

        // create the response

        $response = $this->get('phpexcel')->createStreamedResponse($writer);

        // adding headers		

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'statistiques-creation.xlsx'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');

        $response->headers->set('Pragma', 'public');

        $response->headers->set('Cache-Control', 'maxage=1');

        $response->headers->set('Content-Disposition', $dispositionHeader);


        return $response;
    }

    /**
     * @Route("/exporter/excell-statistique2/{debut}/{fin}",name="export_statistique2")
     */
    public function exportStatistique2Action(Request $request, $debut = null, $fin = null)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject($this->get('kernel')->getRootDir() . '\..\web\Export\typesEntreprisesFormeLegale.xlsx');

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParFormJur($debut, $fin);

        $titre = $this->get('translator')->trans("nom_application");

        $phpExcelObject->getProperties()->setCreator($titre)
            ->setLastModifiedBy($titre)
            ->setTitle("")
            ->setSubject("")
            ->setDescription("Ce document contient la liste")
            ->setKeywords("office 2016 openxml php")
            ->setCategory("");

        $i = 2;
        foreach ($listerdemande as $demande) {

            $nombre = 0;
            foreach ($demande['data'] as $data) {
                $nombre += $data;
            }
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $demande['name'])
                ->setCellValue('B' . $i, $nombre);

            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Simple');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet

        $phpExcelObject->setActiveSheetIndex(0);
        // create the writer

        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');

        // create the response

        $response = $this->get('phpexcel')->createStreamedResponse($writer);

        // adding headers		

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'statistiques-repartition-forme.xlsx'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');

        $response->headers->set('Pragma', 'public');

        $response->headers->set('Cache-Control', 'maxage=1');

        $response->headers->set('Content-Disposition', $dispositionHeader);


        return $response;
    }

    /**
     * @Route("/exporter/excell-statistique3/{debut}/{fin}",name="export_statistiqueforme")
     */
    public function exportStatistique21Action(Request $request, $debut = null, $fin = null)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject($this->get('kernel')->getRootDir() . '\..\web\Export\typesEntreprisesSecteurActivite.xlsx');

        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();

        $datedebut = null;
        $datefin = null;

        // die("test");

        $listeSecteur = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseSecteur($datedebut, $datefin, $idLangue);

        $titre = $this->get('translator')->trans("nom_application");

        $phpExcelObject->getProperties()->setCreator($titre)
            ->setLastModifiedBy($titre)
            ->setTitle("")
            ->setSubject("")
            ->setDescription("Ce document contient la liste")
            ->setKeywords("office 2016 openxml php")
            ->setCategory("");

        $i = 2;
        foreach ($listeSecteur as $secteur) {

            $nombre = 0;
            foreach ($secteur['data'] as $data) {
                $nombre += $data;
            }
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $secteur['name'])
                ->setCellValue('B' . $i, $nombre);

            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Simple');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet

        $phpExcelObject->setActiveSheetIndex(0);
        // create the writer

        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');

        // create the response

        $response = $this->get('phpexcel')->createStreamedResponse($writer);

        // adding headers		

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'statistiques-repartition-secteur.xlsx'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');

        $response->headers->set('Pragma', 'public');

        $response->headers->set('Cache-Control', 'maxage=1');

        $response->headers->set('Content-Disposition', $dispositionHeader);


        return $response;
    }

    /**
     * @Route("/{_locale}/liste-notaire",name="liste_notaire")
     */
    public function listeNotaireAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $notaire = $em->getRepository("ParametrageBundle:Pole")->getPoleByName('NOTAIRE');
        $entreprises = $em->getRepository("BanquemondialeBundle:Entreprise")->getNotaires($notaire);

        //die(dump($entreprises));
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($request->getLocale());

        return $this->render('DefaultBundle:Default:liste-notaire.html.twig', array('langues' => $lgs, 'notaires' => $entreprises
        ));
    }

    /**
     * @Route("/{_locale}/annonceur/index",name="annonceur-index")
     */
    public function annonceurIndexAction($data = null)
    {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();


        if ($pole and $pole->getSigle() != "AL") {
            return $this->redirectToRoute('accueil');
        }
        $idPole = 1;
        $idPole = $pole->getId();

        $em = $this->getDoctrine()->getManager();

        $greffe = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle("GF");
        $idGreffe = $greffe->getId();

        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }

        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $data = null;

        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierAnnonceur($user, null, $langue->getId(), $idPole, 25, 1);
        //die(dump(count($listerdemande)));
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['annonceurType'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierAnnonceur($user, $data, $langue->getId(), $idPole, null, 1);

            if (isset($_POST['exportButton'])) {
                //return $this->redirectToRoute('annonce-legale-word',array('data' => $data));
                //$this->annonceLegaleWordAction($listerdemande);
                try {
                    $statutPublie = $em->getRepository('BanquemondialeBundle:StatutTraitement')->findOneByCode("DD");
                    foreach ($listerdemande as $dossier) {
                        $documentCollected = $em->getRepository('BanquemondialeBundle:DocumentCollected')->find($dossier['idDocumentCollected']);
                        $documentCollected->setStatutTraitement($statutPublie);
                        $documentCollected->setDateDelivrance(new \Datetime());
                        $em->persist($documentCollected);
                    }

                    //$translated = $this->get('translator')->trans('annonces_exportees');
                    //$this->get('session')->getFlashBag()->add('info', $translated);
                    //die(dump($listerdemande[0]));
                    if ($listerdemande[0]['libelleTypeDossier'] == 'Notaire' && $listerdemande[0]['sigleFormeJuridique'] != 'EI') {

                        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findAnnonceLegaleAnnonceurNotaire($user, $data, $langue->getId(), $idPole, null, 1);
                        $em->flush(); //die(dump($listerdemande));
                        $this->annonceLegalePdfPortailAction($listerdemande);
                    } else {
                        $em->flush();
                        $this->annonceLegalePdfAction($listerdemande);
                    }
                } catch (Exception $e) {
                    $translated = $this->get('translator')->trans('annonces_non_exportees');
                    $this->get('session')->getFlashBag()->add('info', $translated);
                }
            }
        }

        $form = $this->createForm(new AnnonceurSearchType(array('langue' => $langue)));

        $form->bind($request);


        return $this->render('DefaultBundle:Default:annonceur-index.html.twig', array('form' => $form->createView(), 'listerdemande' => $listerdemande, 'langue' => $langue, 'idp' => $idPole, 'langues' => $lgs, 'idGreffe' => $idGreffe, 'data' => $data));
    }

    /**
     * @Route("/{_locale}/annonceur/exporte",name="annonceur-exporte")
     */
    public function annonceurExporteAction($data = null)
    {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();


        if ($pole and $pole->getSigle() != "AL") {
            return $this->redirectToRoute('accueil');
        }
        $idPole = 1;
        $idPole = $pole->getId();

        $em = $this->getDoctrine()->getManager();

        $greffe = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle("GF");
        $idGreffe = $greffe->getId();

        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }

        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $data = null;

        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierAnnonceur($user, null, $langue->getId(), $idPole, 25, 2);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['annonceurType'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierAnnonceur($user, $data, $langue->getId(), $idPole, null, 2);
        }

        $form = $this->createForm(new AnnonceurSearchType(array('langue' => $langue)));

        $form->bind($request);


        return $this->render('DefaultBundle:Default:annonceur-exporte.html.twig', array('form' => $form->createView(), 'listerdemande' => $listerdemande, 'langue' => $langue, 'idp' => $idPole, 'langues' => $lgs, 'idGreffe' => $idGreffe, 'data' => $data));
    }

    public function annonceLegaleWordAction($annonces)
    {

        //die(dump($request));
        // Create a new Word document
        $phpWord = new PhpWord();


        $fontStyleName = 'rStyle';
        $phpWord->addFontStyle($fontStyleName, array('bold' => true));

        $fontStyleTable = 'tableStyle';
        $phpWord->addFontStyle($fontStyleTable, array('bold' => true, 'allCaps' => "true"));

        $fontStyleTableTitre = 'tableTitreStyle';
        $phpWord->addFontStyle($fontStyleTableTitre, array('size' => 16, 'bold' => true, 'allCaps' => true,));

        $paragraphStyleName = 'pStyle';
        $phpWord->addParagraphStyle($paragraphStyleName, array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0));

        $underlineBoldStyleName = 'underlineBoldStyle';
        $phpWord->addFontStyle($underlineBoldStyleName, array('bold' => true, 'underline' => 'single'));


        /* Note: any element you append to a document must reside inside of a Section. */

        $section = $phpWord->addSection();

        $val = 1;


        foreach ($annonces as $annonce) {

            //die(dump($annonce));
            if (strtoupper($annonce['sigleFormeJuridique']) == "EI") {
                /* pour entreprise individuelle */
                $section->addText(sprintf("%02d", $val) . '- LE SOUSSIGNE:', $fontStyleName, $paragraphStyleName);
                $section->addText(strtoupper(htmlspecialchars($annonce['gerant'])), $fontStyleName, $paragraphStyleName);
                $section->addText('NE: Le ' . $annonce['dateNaissanceGerant']->format('d/m/Y') . ' à ' . strtoupper(htmlspecialchars($annonce['lieuNaissanceGerant'])) . ' Nationalité: ' . htmlspecialchars($annonce['paysGerant']), null, array('spaceAfter' => 10));
                $section->addText('DEMEURANT à: ' . htmlspecialchars($annonce['adresseGerant']), null, array('spaceAfter' => 10));
                $section->addText('AGISSANT EN QUALITE de: ' . htmlspecialchars($annonce['fonctionGerant']), null, array('spaceAfter' => 10));
                $section->addText('NOM DE L\'ENTREPRISE: ' . strtoupper(htmlspecialchars($annonce['denominationSociale'])), $fontStyleName, array('spaceAfter' => 10));
                $section->addText('SIGLE: ' . strtoupper(htmlspecialchars($annonce['sigle'])), null, array('spaceAfter' => 10));
                $section->addText('ACTIVITES PRINCIPALES: ' . htmlspecialchars($annonce['secteurActivite']), null, array('spaceAfter' => 10));
                $section->addText('SIEGE SOCIAL: ' . strtoupper(htmlspecialchars($annonce['adresseSiege'])), null, array('spaceAfter' => 10));
                $section->addText('TEL: ' . $annonce['telephoneGerant'] . ' / ' . $annonce['portableGerant'], null, array('spaceAfter' => 10));
                $section->addText('N°FORMALITE: ' . $annonce['numRccmFormalite'], null, array('spaceAfter' => 10));
                $section->addText('N°RCCM: ' . $annonce['numRccmEntreprise'], null, array('spaceAfter' => 240));
                /* fin pour entreprise individuelle */
                $val = $val + 1;
            } else {
                /* pour sous seing privé */
                $fmt = new \NumberFormatter('de_DE', \NumberFormatter::DECIMAL);
                $fmt->setPattern("#,##0.### GNF");
                $capitalSocialCurrency = $fmt->format($annonce['capitalSocial']);

                $fancyTableStyleName = 'Table';
                $fancyTableStyle = array('borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
                $fancyTableCellStyle = array('valign' => 'center', 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
                $cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0);


                $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle);
                $table = $section->addTable($fancyTableStyleName);
                $table->addRow();
                $cell1 = $table->addCell(9000, $fancyTableCellStyle);
                $cell1->addText(strtoupper(htmlspecialchars($annonce['denominationSociale'])), $fontStyleTableTitre, $cellHCentered);
                $cell1->addText(strtoupper(htmlspecialchars('Forme juridique: ' . $annonce['sigleFormeJuridique'])), $fontStyleTable, $cellHCentered);
                $cell1->addText('CAPITAL SOCIAL: ' . $capitalSocialCurrency, $fontStyleTable, $cellHCentered);
                $cell1->addText(strtoupper(htmlspecialchars('SIEGE SOCIAL : ' . $annonce['adresseSiege'])), $fontStyleTable, $cellHCentered);
                $cell1->addText(strtoupper(htmlspecialchars($annonce['numRccmFormalite'] . ' DU ' . $annonce['dateRccm'])), $fontStyleTable, $cellHCentered);

                $section->addText(strtoupper('Avis de constitution'), $fontStyleName, array('spaceBefore' => 200, 'spaceAfter' => 200, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'allCaps' => true));

                $section->addText(htmlspecialchars("Aux termes d’un acte sous seing privé, en date du " . $annonce['dateRccm'] . ",  il a été créé une société commerciale ayant les caractéristiques suivantes :"));


                $textRun = $section->addTextRun(array('spaceAfter' => 0));
                $textRun->addText("DENOMINATION SOCIALE:", $underlineBoldStyleName);
                $textRun->addText(strtoupper(htmlspecialchars(" " . $annonce['denominationSociale'])));

                $textRun = $section->addTextRun(array('spaceAfter' => 0));
                $textRun->addText("FORME:", $underlineBoldStyleName);
                $textRun->addText(htmlspecialchars(strtoupper(" " . $annonce['sigleFormeJuridique'])));

                $textRun = $section->addTextRun(array('spaceAfter' => 0));
                $textRun->addText("CAPITAL SOCIAL:", $underlineBoldStyleName);
                $textRun->addText(" " . $capitalSocialCurrency);

                $textRun = $section->addTextRun(array('spaceAfter' => 0));
                $textRun->addText("SIEGE SOCIAL:", $underlineBoldStyleName);
                $textRun->addText(htmlspecialchars(strtoupper(" " . $annonce['adresseSiege'])));

                $textRun = $section->addTextRun(array('spaceAfter' => 0));
                $textRun->addText("ACTIVITE PRINCIPALE:", $underlineBoldStyleName);
                $textRun->addText(strtoupper(" " . $annonce['secteurActivite'] . " " . $annonce['secteurActiviteSecondaire'] . " " . $annonce['activiteSociale']));

                $textRun = $section->addTextRun(array('spaceAfter' => 0));
                $textRun->addText("DUREE:", $underlineBoldStyleName);
                $textRun->addText(htmlspecialchars(" " . strtoupper($this->int_to_words($annonce['duree'])) . " (" . $annonce['duree'] . ") ans à partir de son immatriculation au RCCM"));

                $textRun = $section->addTextRun(array('spaceAfter' => 0));
                $textRun->addText("EXERCICE SOCIAL:", $underlineBoldStyleName);
                $textRun->addText(htmlspecialchars(" L’exercice social commence le premier Janvier et se termine le trente et un Décembre de chaque année. "));


                $textRun = $section->addTextRun(array('spaceAfter' => 0));
                $textRun->addText("GERANT:", $underlineBoldStyleName);
                $textRun->addText(htmlspecialchars(strtoupper(" " . $annonce['gerant'] . ",  demeurant à " . $annonce['villeGerant'])));

                $textRun = $section->addTextRun(array('spaceBefore' => 240, 'spaceAfter' => 0));
                $textRun->addText("FORMALITES D'IMMATRICULATION:", $underlineBoldStyleName);

                $section->addText(htmlspecialchars("La Société a été immatriculée au Registre du Commerce et du Crédit Mobilier sous le numéro : " . $annonce['numRccmEntreprise'] . " DU " . $annonce['dateRccm']), null, array('spaceBefore' => 0, 'spaceAfter' => 240));

                $section->addText(htmlspecialchars("Deux copies des statuts et une copie de la déclaration de souscription et de versement ont été déposées au Greffe du Tribunal de Première Instance de KALOUM à Conakry."), null, array('spaceAfter' => 20));

                $section->addText(htmlspecialchars("Pour extrait et mention"), array('bold' => true), array('spaceAfter' => 20, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT));
                $section->addText(htmlspecialchars($annonce['codeCivilite'] . " " . strtoupper($annonce['gerant']) . ", et par délégation Maitre Alsény FOFANA Greffier en chef"), array('bold' => true), array('spaceAfter' => 140, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT));

                //* fin pour sous seing privé */
            }
        }

        // Sauver le document en fichier OOXML
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');

        // Creer un fichier temporaire dans le system
        $fileName = 'annonces-legales.docx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Ecriture du fichier dans le dossier temporaire
        $objWriter->save($temp_file);

        header('Content-Description: Annonces legales');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename=' . $fileName . '');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($temp_file));
        ob_clean();
        flush();
        readfile($temp_file);
        exit();
    }

    public function annonceLegalePdfAction($annonces)
    {

        $typeAnnonce = null;
        $typeDossier = null;

        if ($annonces) {
            $typeDossier = $annonces[0]["libelleTypeDossier"];
        }
        //die(dump($annonces[0]));

        if ($annonces && strtoupper($annonces[0]["sigleFormeJuridique"]) == 'EI') {
            $typeAnnonce = "individuel";
        } else {
            $typeAnnonce = "commercial";
        }


        $html = $this->renderView('DefaultBundle:Default:visualiser-annonces-ei-pdf.html.twig', array('annonces' => $annonces, 'typeAnnonce' => $typeAnnonce, 'typeDossier' => $typeDossier));
        $nomFichier = "annonce.pdf"; //cofifier après rccm
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        //$html2pdf->Output($nomFichier,'D');
        $html2pdf->Output($nomFichier);
        exit;
    }

    function int_to_words($number)
    {
        switch ($number) {
            case 0:
                $word = "0";
                break;
            case 1:
                $word = "Un";
                break;
            case 2:
                $word = "Deux";
                break;
            case 3:
                $word = "Trois";
                break;
            case 4:
                $word = "Quatre";
                break;
            case 5:
                $word = "Cinq";
                break;
            case 6:
                $word = "Six";
                break;
            case 7:
                $word = "Sept";
                break;
            case 8:
                $word = "Huit";
                break;
            case 9:
                $word = "Neuf";
                break;
            case 10:
                $word = "Dix";
                break;
            case 11:
                $word = "onze";
                break;
            case 12:
                $word = "douze";
                break;
            case 13:
                $word = "treize";
                break;
            case 14:
                $word = "quatorze";
                break;
            case 15:
                $word = "quinze";
                break;
            case 16:
                $word = "seize";
                break;
            case 17:
                $word = "dix-sept";
                break;
            case 18:
                $word = "dix-huit";
                break;
            case 19:
                $word = "dix-neuf";
                break;
            case 20:
                $word = "vingt";
                break;
            case 21:
                $word = "vingt-et-un";
                break;
            case 22:
                $word = "vingt-deux";
                break;
            case 23:
                $word = "vingt-trois";
                break;
            case 24:
                $word = "vingt-quatre";
                break;
            case 25:
                $word = "vingt-cinq";
                break;
            case 26:
                $word = "vingt-six";
                break;
            case 27:
                $word = "vingt-sept";
                break;
            case 28:
                $word = "vingt-huit";
                break;
            case 29:
                $word = "vingt-neuf";
                break;
            case 30:
                $word = "trente";
                break;
            case 31:
                $word = "trente-et-un";
                break;
            case 32:
                $word = "trente-deux";
                break;
            case 33:
                $word = "trente-trois";
                break;
            case 34:
                $word = "trente-quatre";
                break;
            case 35:
                $word = "trente-cinq";
                break;
            case 36:
                $word = "trente-six";
                break;
            case 37:
                $word = "trente-sept";
                break;
            case 38:
                $word = "trente-huit";
                break;
            case 39:
                $word = "trente-neuf";
                break;
            case 40:
                $word = "quarante";
                break;
            case 41:
                $word = "quarante-et-un";
                break;
            case 42:
                $word = "quarante-deux";
                break;
            case 43:
                $word = "quarante-trois";
                break;
            case 44:
                $word = "quarante-quatre";
                break;
            case 45:
                $word = "quarante-cinq";
                break;
            case 46:
                $word = "quarante-six";
                break;
            case 47:
                $word = "quarante-sept";
                break;
            case 48:
                $word = "quarante-huit";
                break;
            case 49:
                $word = "quarante-neuf";
                break;
            case 50:
                $word = "cinquante";
                break;
            case 51:
                $word = "cinquante-et-un";
                break;
            case 52:
                $word = "cinquante-deux";
                break;
            case 53:
                $word = "cinquante-trois";
                break;
            case 54:
                $word = "cinquante-quatre";
                break;
            case 55:
                $word = "cinquante-cinq";
                break;
            case 56:
                $word = "cinquante-six";
                break;
            case 57:
                $word = "cinquante-sept";
                break;
            case 58:
                $word = "cinquante-huit";
                break;
            case 59:
                $word = "cinquante-neuf";
                break;
            case 60:
                $word = "soixante";
                break;
            case 61:
                $word = "soixante-et-un";
                break;
            case 62:
                $word = "soixante-deux";
                break;
            case 63:
                $word = "soixante-trois";
                break;
            case 64:
                $word = "soixante-quatre";
                break;
            case 65:
                $word = "soixante-cinq";
                break;
            case 66:
                $word = "soixante-six";
                break;
            case 67:
                $word = "soixante-sept";
                break;
            case 68:
                $word = "soixante-huit";
                break;
            case 69:
                $word = "soixante-neuf";
                break;
            case 70:
                $word = "soixante-dix";
                break;
            case 71:
                $word = "soixante-et-onze";
                break;
            case 72:
                $word = "soixante-douze";
                break;
            case 73:
                $word = "soixante-treize";
                break;
            case 74:
                $word = "soixante-quatorze";
                break;
            case 75:
                $word = "soixante-quinze";
                break;
            case 76:
                $word = "soixante-seize";
                break;
            case 77:
                $word = "soixante-dix-sept";
                break;
            case 78:
                $word = "soixante-dix-huit";
                break;
            case 79:
                $word = "soixante-dix-neuf";
                break;
            case 80:
                $word = "quatre-vingts";
                break;
            case 81:
                $word = "quatre-vingt-un";
                break;
            case 82:
                $word = "quatre-vingt-deux";
                break;
            case 83:
                $word = "quatre-vingt-trois";
                break;
            case 84:
                $word = "quatre-vingt-quatre";
                break;
            case 85:
                $word = "quatre-vingt-cinq";
                break;
            case 86:
                $word = "quatre-vingt-six";
                break;
            case 87:
                $word = "quatre-vingt-sept";
                break;
            case 88:
                $word = "quatre-vingt-huit";
                break;
            case 89:
                $word = "quatre-vingt-neuf";
                break;
            case 90:
                $word = "quatre-vingt-dix";
                break;
            case 91:
                $word = "quatre-vingt-onze";
                break;
            case 92:
                $word = "quatre-vingt-douze";
                break;
            case 93:
                $word = "quatre-vingt-treize";
                break;
            case 94:
                $word = "quatre-vingt-quatorze";
                break;
            case 95:
                $word = "quatre-vingt-quinze";
                break;
            case 96:
                $word = "quatre-vingt-seize";
                break;
            case 97:
                $word = "quatre-vingt-dix-sept";
                break;
            case 98:
                $word = "quatre-vingt-dix-huit";
                break;
            case 99:
                $word = "quatre-vingt-dix-neuf";
                break;
        }

        return $word;
    }

    public function annonceLegalePdfPortailAction($annonces)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $typeAnnonce = null;
        $typeDossier = null;
        $isQrCode = false;
        //die(dump($annonces));
        if (isset($annonces[0]["nif"])) {
            $textQr = "RCCM:" . $annonces[0]["numRccmEntreprise"] . ", NIF:" . $annonces[0]["nif"];
            $isQrCode = true;
        } else {
            //$textQr = "RCCM:" . $annonces[0]["numRccmEntreprise"];
            $textQr = "gagagag";
        }
        $qrCode = new QrCode($textQr);
        $qrCode->setSize(180);


        //$qrCode = $this->get('endroid.qrcode.factory')->create('QR Code', ['size' => 180]);

        $path = $this->get('kernel')->getRootDir() . '/../web/img/qrcode.png';
        // Save it to a file
        $qrCode->writeFile($path);

        if ($annonces) {
            $typeDossier = $annonces[0]["libelleTypeDossier"];
        }
        //die(dump($annonces[0]));

        if ($annonces && strtoupper($annonces[0]["sigleFormeJuridique"]) == 'EI') {
            $typeAnnonce = "individuel";
        } else {
            $typeAnnonce = "commercial";
        }
        if ($typeDossier == "Notaire") {
            //$entreprise = $em->getRepository("BanquemondialeBundle:Entreprise")->findOneByUtilisateur();
        }

        $html = $this->renderView('DefaultBundle:Default:visualiser-annonces-portail-pdf.html.twig', array('annonces' => $annonces, 'typeAnnonce' => $typeAnnonce, 'typeDossier' => $typeDossier, 'isQrCode' => $isQrCode));
        $nomFichier = "annonce.pdf"; //cofifier après rccm
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        //$html2pdf->Output($nomFichier,'D');
        $html2pdf->Output($nomFichier);
        exit;
    }

    /*
      function int_to_words($number) {
      switch($number){
      case 0:$word = "0";break;
      case 1:$word = "Un";break;
      case 2:$word = "Deux";break;
      case 3:$word = "Trois";break;
      case 4:$word = "Quatre";break;
      case 5:$word = "Cinq";break;
      case 6:$word = "Six";break;
      case 7:$word = "Sept";break;
      case 8:$word = "Huit";break;
      case 9:$word = "Neuf";break;
      case 10:$word = "Dix";break;
      case 11:$word = "onze";break;
      case 12:$word = "douze";break;
      case 13:$word = "treize";break;
      case 14:$word = "quatorze";break;
      case 15:$word = "quinze";break;
      case 16:$word = "seize";break;
      case 17:$word = "dix-sept";break;
      case 18:$word = "dix-huit";break;
      case 19:$word = "dix-neuf";break;
      case 20:$word = "vingt";break;
      case 21:$word = "vingt-et-un";break;
      case 22:$word = "vingt-deux";break;
      case 23:$word = "vingt-trois";break;
      case 24:$word = "vingt-quatre";break;
      case 25:$word = "vingt-cinq";break;
      case 26:$word = "vingt-six";break;
      case 27:$word = "vingt-sept";break;
      case 28:$word = "vingt-huit";break;
      case 29:$word = "vingt-neuf";break;
      case 30:$word = "trente";break;
      case 31:$word = "trente-et-un";break;
      case 32:$word = "trente-deux";break;
      case 33:$word = "trente-trois";break;
      case 34:$word = "trente-quatre";break;
      case 35:$word = "trente-cinq";break;
      case 36:$word = "trente-six";break;
      case 37:$word = "trente-sept";break;
      case 38:$word = "trente-huit";break;
      case 39:$word = "trente-neuf";break;
      case 40:$word = "quarante";break;
      case 41:$word = "quarante-et-un";break;
      case 42:$word = "quarante-deux";break;
      case 43:$word = "quarante-trois";break;
      case 44:$word = "quarante-quatre";break;
      case 45:$word = "quarante-cinq";break;
      case 46:$word = "quarante-six";break;
      case 47:$word = "quarante-sept";break;
      case 48:$word = "quarante-huit";break;
      case 49:$word = "quarante-neuf";break;
      case 50:$word = "cinquante";break;
      case 51:$word = "cinquante-et-un";break;
      case 52:$word = "cinquante-deux";break;
      case 53:$word = "cinquante-trois";break;
      case 54:$word = "cinquante-quatre";break;
      case 55:$word = "cinquante-cinq";break;
      case 56:$word = "cinquante-six";break;
      case 57:$word = "cinquante-sept";break;
      case 58:$word = "cinquante-huit";break;
      case 59:$word = "cinquante-neuf";break;
      case 60:$word = "soixante";break;
      case 61:$word = "soixante-et-un";break;
      case 62:$word = "soixante-deux";break;
      case 63:$word = "soixante-trois";break;
      case 64:$word = "soixante-quatre";break;
      case 65:$word = "soixante-cinq";break;
      case 66:$word = "soixante-six";break;
      case 67:$word = "soixante-sept";break;
      case 68:$word = "soixante-huit";break;
      case 69:$word = "soixante-neuf";break;
      case 70:$word = "soixante-dix";break;
      case 71:$word = "soixante-et-onze";break;
      case 72:$word = "soixante-douze";break;
      case 73:$word = "soixante-treize";break;
      case 74:$word = "soixante-quatorze";break;
      case 75:$word = "soixante-quinze";break;
      case 76:$word = "soixante-seize";break;
      case 77:$word = "soixante-dix-sept";break;
      case 78:$word = "soixante-dix-huit";break;
      case 79:$word = "soixante-dix-neuf";break;
      case 80:$word = "quatre-vingts";break;
      case 81:$word = "quatre-vingt-un";break;
      case 82:$word = "quatre-vingt-deux";break;
      case 83:$word = "quatre-vingt-trois";break;
      case 84:$word = "quatre-vingt-quatre";break;
      case 85:$word = "quatre-vingt-cinq";break;
      case 86:$word = "quatre-vingt-six";break;
      case 87:$word = "quatre-vingt-sept";break;
      case 88:$word = "quatre-vingt-huit";break;
      case 89:$word = "quatre-vingt-neuf";break;
      case 90:$word = "quatre-vingt-dix";break;
      case 91:$word = "quatre-vingt-onze";break;
      case 92:$word = "quatre-vingt-douze";break;
      case 93:$word = "quatre-vingt-treize";break;
      case 94:$word = "quatre-vingt-quatorze";break;
      case 95:$word = "quatre-vingt-quinze";break;
      case 96:$word = "quatre-vingt-seize";break;
      case 97:$word = "quatre-vingt-dix-sept";break;
      case 98:$word = "quatre-vingt-dix-huit";break;
      case 99:$word = "quatre-vingt-dix-neuf";break;
      }

      return $word;
      }
     */

    /**
     * @Route("/{_locale}/visualiserRccmAnnonceur/{idd}",name="visualiser-rccm-annonceur")
     */
    public function visualiserRccmAnnonceurAction(Request $request, DossierDemande $idd)
    {

        $em = $this->getDoctrine()->getManager();
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminUpload = $chemin->getNom(); //a modifier par la requete vers la bdd
        $greffe = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle("GF");
        $formulaireDelivre = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('dossierDemande' => $idd, 'pole' => $greffe));


        //die(dump($idd));
        $nom = $formulaireDelivre->getNomFichier();
        $path = $cheminUpload . $idd->getId() . "\\" . $nom;

        $response = new BinaryFileResponse($path);

        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE, //ResponseHeaderBag::DISPOSITION_ATTACHMENT pour download DISPOSITION_INLINE pour render
            $nom
        );


        return $response;
    }

    /**
     * @Route("/{_locale}/statistiques-periode-excel-jours",name="statistiques-periode-excel-jours")
     */
    public function statistiquesPeriodeExcelJoursAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeExcel($dateDebut, $dateFin, 'jours');

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeExcel($dateDebut, $dateFin, 'jours');
        }

        //die(dump($listerdemande));

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0] . " " . $this->get('translator')->trans($demande[1]) . " " . $demande[2];
            $tabResult[$i][1] = $demande[3];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-periode-excel-jours.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-periode-excel-mois",name="statistiques-periode-excel-mois")
     */
    public function statistiquesPeriodeExcelMoisAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = '01-' . substr($dateDebutT->format('d-m-Y'), 3, 7);
        $dateFinT = new \DateTime('now');
        $dateFin = '31-' . substr($dateFinT->format('d-m-Y'), 3, 7);

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeExcel($dateDebut, $dateFin, 'mois');

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
            $dateDebut = '01-' . $data['dateCreationDebut'];
            $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];


            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeExcel($dateDebut, $dateFin, 'mois');
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0];
            $tabResult[$i][1] = $this->get('translator')->trans($demande[1]) . "(" . $demande[2] . ")";
            $tabResult[$i][2] = $demande[3];
            $i = $i + 1;
        }

        $dateFin[0] = '0';
        $dateFin[1] = '1';


        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-periode-excel-mois.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-periode-excel-annee",name="statistiques-periode-excel-annee")
     */
    public function statistiquesPeriodeExcelAnneeAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = '01-01-' . substr($dateDebutT->format('d-m-Y'), 6, 4);
        $dateFinT = new \DateTime('now');
        $dateFin = '31-12-' . substr($dateFinT->format('d-m-Y'), 6, 4);

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeExcel($dateDebut, $dateFin);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dateDebut = '01-01-' . $data['dateCreationDebut'];
            $dateFin = '31-12-' . $data['dateCreationFin'];

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeExcel($dateDebut, $dateFin);
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0];
            $tabResult[$i][1] = $demande[2];
            $tabResult[$i][2] = $demande[3];
            $i = $i + 1;
        }

        $dateFin[0] = '0';
        $dateFin[1] = '1';

        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-periode-excel-annee.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-periode-graphe-jours",name="statistiques-periode-graphe-jours")
     */
    public function statistiquesPeriodeGrapheJoursAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        $typeGraphe = "column";
        $plageDate = [];

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeGraphe(0, $dateDebut, $dateFin, $plageDate, 'jours');

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];
            $typeGraphe = $data['typeGraphe'];
            $entreprise = $data['entreprise'];
            $entreprise = ($entreprise) ? $entreprise : 0;
            $plageDate = $this->date_range($dateDebut, $dateFin, '+1 day', 'd-m-Y');
            //die(dump($typeGraphe));
            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeGraphe($entreprise, $dateDebut, $dateFin, $plageDate, 'jours');
            //die(dump($plageDate));
        }

        //die(dump($listerdemande));

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0] . "-" . $this->get('translator')->trans($demande[1]) . "-" . $demande[2];
            $tabResult[$i][1] = $demande[3];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-periode-graphe-jours.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-periode-graphe-mois",name="statistiques-periode-graphe-mois")
     */
    public function statistiquesPeriodeGrapheMoisAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $tabResult2 = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = '01-' . substr($dateDebutT->format('d-m-Y'), 3, 7);
        $dateFinT = new \DateTime('now');
        $dateFin = '31-' . substr($dateFinT->format('d-m-Y'), 3, 7);
        $typeGraphe = "column";
        $plageDate = [];

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeGraphe($dateDebut, $dateFin, $plageDate, 'mois');
        $listerdemande2 = null;

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];

            $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
            $dateDebut = '01-' . $data['dateCreationDebut'];
            $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];

            $plageDate = $this->date_range($dateDebut, $dateFin, 'next month', 'd-m-Y');
            //die(dump($plageDate));

            $typeGraphe = $data['typeGraphe'];

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeGraphe($dateDebut, $dateFin, $plageDate, 'mois');
            $listerdemande2 = null;

            if ($typeGraphe == "line") {
                $annee1 = substr($dateDebut, 6, 4);
                $annee2 = substr($dateFin, 6, 4);

                if ($annee1 >= $annee2) {
                    $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
                    $form->bind($request);

                    $translated = $this->get('translator')->trans("veuillez choisir deux année différentes");
                    $this->get('session')->getFlashBag()->add('error', $translated);

//					die(dump($annee1));
                    return $this->render('DefaultBundle:Default:statistiques-periode-graphe-mois.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
                }

                $dateDebut1 = $dateDebut;
                $dateFin1 = '31-12-' . $annee1;

                $dateDebut2 = '01-01-' . $annee2;
                $dateFin2 = $dateFin;

                $plageDate = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];

                $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeGrapheAnnuel($dateDebut1, $dateFin1, $plageDate, 'mois');
                $listerdemande2 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeGrapheAnnuel($dateDebut2, $dateFin2, $plageDate, 'mois');

                $i = 0;
                foreach ($listerdemande2 as $demande2) {
                    //$tabResult[$i][0] = $this->get('translator')->trans($demande[1]) . " " . $demande[2];
                    $tabResult2[$i][0] = $this->chiffreToMonth($demande2[1]);
                    $tabResult2[$i][1] = $demande2[3];
                    $i = $i + 1;
                }

            }
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            //$tabResult[$i][0] = $this->get('translator')->trans($demande[1]) . " " . $demande[2];
            $tabResult[$i][0] = $this->chiffreToMonth($demande[1]) . " " . $demande[2];
            $tabResult[$i][1] = $demande[3];
            $i = $i + 1;
        }

        $dateFin[0] = '0';
        $dateFin[1] = '1';

        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-periode-graphe-mois.html.twig', array('listerdemande' => $listerdemande, 'listerdemande2' => $listerdemande2, 'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-periode-graphe-annee",name="statistiques-periode-graphe-annee")
     */
    public function statistiquesPeriodeGrapheAnneeAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = '01-01-' . substr($dateDebutT->format('d-m-Y'), 6, 4);
        $dateFinT = new \DateTime('now');
        $dateFin = '31-12-' . substr($dateFinT->format('d-m-Y'), 6, 4);
        $typeGraphe = "column";
        $plageDate = [];

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeGraphe($dateDebut, $dateFin, $plageDate);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dateDebut = '01-01-' . $data['dateCreationDebut'];
            $dateFin = '31-12-' . $data['dateCreationFin'];
            $typeGraphe = $data['typeGraphe'];

            $plageDate = $this->date_range($dateDebut, $dateFin, '+1 year', 'd-m-Y');

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeGraphe($dateDebut, $dateFin, $plageDate);
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[2];
            $tabResult[$i][1] = $demande[3];
            $i = $i + 1;
        }

        $dateFin[0] = '0';
        $dateFin[1] = '1';


        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-periode-graphe-annee.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-formJuridique-excel/{typePlage}",name="statistiques-formJuridique-excel",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesNbEntrepriseByFormeJuridiqueExcelAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        //die(dump($typePlage));

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseParFormeJurique($dateDebut, $dateFin, $langue->getId());

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseParFormeJurique($dateDebut, $dateFin, $langue->getId());
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0];
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-formeJuridique-excel.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-formJuridique-graphe/{typePlage}",name="statistiques-formJuridique-graphe",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesNbEntrepriseByFormeJuridiqueGrapheAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $tabResult = null;
        $tabResult1 = null;
        $tabResult2 = null;
        $tabResult3 = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        $typeGraphe = "column";

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseParFormeJurique($dateDebut, $dateFin, $idLangue);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];
            $typeGraphe = $data['typeGraphe'];

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }


            if ($typePlage != 1 && $typeGraphe == 'column') {
                $plageDate = $this->date_range($dateDebut, $dateFin, 'next month', 'd-m-Y');

                $listerdemande1 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseParFormeJuriqueGrapheAnnuel($dateDebut, $dateFin, $idLangue, "EI", $plageDate);

                $listerdemande2 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseParFormeJuriqueGrapheAnnuel($dateDebut, $dateFin, $idLangue, "SARL/SARLU", $plageDate);

                $listerdemande3 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseParFormeJuriqueGrapheAnnuel($dateDebut, $dateFin, $idLangue, "AUTRE", $plageDate);


                $i = 0;
                foreach ($listerdemande1 as $demande1) {
                    $tabResult1[$i][0] = $this->chiffreToMonth($demande1[1]) . " " . $demande1[2];
                    $tabResult1[$i][1] = $demande1[3];
                    $tabResult1[$i][2] = $this->get('translator')->trans("IND");
                    $i = $i + 1;
                }

                $i = 0;
                foreach ($listerdemande2 as $demande2) {
                    $tabResult2[$i][0] = $this->chiffreToMonth($demande2[1]) . " " . $demande2[2];
                    $tabResult2[$i][1] = $demande2[3];
                    $tabResult2[$i][2] = $this->get('translator')->trans("SARL/SARLU");
                    $i = $i + 1;
                }

                $i = 0;
                foreach ($listerdemande3 as $demande3) {
                    $tabResult3[$i][0] = $this->chiffreToMonth($demande3[1]) . " " . $demande3[2];
                    $tabResult3[$i][1] = $demande3[3];
                    $tabResult3[$i][2] = $this->get('translator')->trans("AUTRE");
                    $i = $i + 1;
                }

                $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
                $form->bind($request);

                return $this->render('DefaultBundle:Default:statistiques-formeJuridique-graphe.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'tabResult1' => $tabResult1, 'tabResult2' => $tabResult2, 'tabResult3' => $tabResult3, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));

            }

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseParFormeJurique($dateDebut, $dateFin, $idLangue);
        }


        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $this->get('translator')->trans($demande[0]);
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-formeJuridique-graphe.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'tabResult1' => $tabResult1, 'tabResult2' => $tabResult2, 'tabResult3' => $tabResult3, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-genre-excel/{typePlage}",name="statistiques-genre-excel",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesNbEntrepriseGenreExcelAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        //die(dump($typePlage));

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParGenre($dateDebut, $dateFin, $langue->getId());

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParGenre($dateDebut, $dateFin, $langue->getId());
        }

        //die(dump($listerdemande));

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0];
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-genre-excel.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-genre-graphe/{typePlage}",name="statistiques-genre-graphe",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesNbEntrepriseGenreGrapheAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $tabResult = null;
        $tabResult2 = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        $typeGraphe = "column";

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParGenre($dateDebut, $dateFin, $idLangue);

        //die(dump($listerdemande));

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];
            $typeGraphe = $data['typeGraphe'];

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }


            if ($typeGraphe == 'column' || $typeGraphe == 'line') {
                if ($typePlage == 1) {
                    $dateDebut1 = $data['dateCreationDebut'];
                    $dateFin1 = "31-12-" . substr($data['dateCreationDebut'], 6, 4);

                    $dateDebut2 = "01-01-" . substr($data['dateCreationFin'], 6, 4);
                    $dateFin2 = $data['dateCreationFin'];
                } else if ($typePlage == 2) {
                    $dateDebut1 = "01-" . $data['dateCreationDebut'];
                    $dateFin1 = "31-12-" . substr($data['dateCreationDebut'], 3, 7);

                    $dateDebut2 = "01-01-" . substr($data['dateCreationFin'], 3, 7);
                    $dateFin2 = "31-" . $data['dateCreationFin'];
                } else {
                    $dateDebut1 = '01-01-' . $data['dateCreationDebut'];
                    $dateFin1 = '31-12-' . $data['dateCreationDebut'];

                    $dateDebut2 = '01-01-' . $data['dateCreationFin'];
                    $dateFin2 = '31-12-' . $data['dateCreationFin'];
                }

                $plageDate = $this->date_range($dateDebut, $dateFin, 'next month', 'd-m-Y');

                $listerdemande1 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseParGenreGrapheAnnuel($dateDebut, $dateFin, $idLangue, "homme", $plageDate);

                //die(dump($listerdemande1));

                $listerdemande2 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseParGenreGrapheAnnuel($dateDebut, $dateFin, $idLangue, "femme", $plageDate);

                $listerdemandeAll = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseParGenreGrapheAnnuel($dateDebut, $dateFin, $idLangue, null, $plageDate);

                //transformation des montant en pourcentage
                for ($i = 0; $i < count($listerdemandeAll); $i++) {
                    if ($listerdemandeAll[$i][3] > 0) {
                        $listerdemande1[$i][3] = round(($listerdemande1[$i][3] / $listerdemandeAll[$i][3]) * 100, 2);
                        $listerdemande2[$i][3] = round(($listerdemande2[$i][3] / $listerdemandeAll[$i][3]) * 100, 2);
                    }
                }
                //die(dump($listerdemande));

                $i = 0;
                foreach ($listerdemande1 as $demande) {
                    $tabResult[$i][0] = $this->chiffreToMonth($demande[1]) . " " . $demande[2];
                    $tabResult[$i][1] = $demande[3];
                    $tabResult[$i][2] = $this->get('translator')->trans("homme");
                    $i = $i + 1;
                }

                $i = 0;
                foreach ($listerdemande2 as $demande2) {
                    $tabResult2[$i][0] = $this->chiffreToMonth($demande2[1]) . " " . $demande2[2];
                    $tabResult2[$i][1] = $demande2[3];
                    $tabResult2[$i][2] = $this->get('translator')->trans("femme");
                    $i = $i + 1;
                }
                //die(dump($tabResult2));

                $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
                $form->bind($request);

                return $this->render('DefaultBundle:Default:statistiques-genre-graphe.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
            }

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParGenre($dateDebut, $dateFin, $idLangue);
        }


        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $this->get('translator')->trans($demande[0]);
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }


        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-genre-graphe.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-continent-excel/{typePlage}",name="statistiques-continent-excel",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesContinentExcelAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        //die(dump($typePlage));

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParContinentExcel($dateDebut, $dateFin);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParContinentExcel($dateDebut, $dateFin);
        }

        //die(dump($listerdemande));

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0];
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-continent-excel.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-continent-graphe/{typePlage}",name="statistiques-continent-graphe",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesContinentGrapheAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $tabResult2 = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        $typeGraphe = "column";
        $continents = array($this->get('translator')->trans("afrique"), $this->get('translator')->trans("amerique"), $this->get('translator')->trans("antartique"), $this->get('translator')->trans("asie"), $this->get('translator')->trans("europe"), $this->get('translator')->trans("oceanie"));

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParContinentGraphe($dateDebut, $dateFin);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];
            $typeGraphe = $data['typeGraphe'];

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }

            if ($typeGraphe == 'line') {

                if ($typePlage == 1) {
                    $dateDebut1 = $data['dateCreationDebut'];
                    $dateFin1 = "31-12-" . substr($data['dateCreationDebut'], 6, 4);

                    $dateDebut2 = "01-01-" . substr($data['dateCreationFin'], 6, 4);
                    $dateFin2 = $data['dateCreationFin'];
                } else if ($typePlage == 2) {
                    $dateDebut1 = "01-" . $data['dateCreationDebut'];
                    $dateFin1 = "31-12-" . substr($data['dateCreationDebut'], 3, 7);

                    $dateDebut2 = "01-01-" . substr($data['dateCreationFin'], 3, 7);
                    $dateFin2 = "31-" . $data['dateCreationFin'];
                } else {
                    $dateDebut1 = '01-01-' . $data['dateCreationDebut'];
                    $dateFin1 = '31-12-' . $data['dateCreationDebut'];

                    $dateDebut2 = '01-01-' . $data['dateCreationFin'];
                    $dateFin2 = '31-12-' . $data['dateCreationFin'];
                }

                $plageDate = $this->date_range($dateDebut, $dateFin, 'next month', 'd-m-Y');

                //die(dump($dateDebut1." ".$dateFin1." et ".$dateDebut2." ".$dateFin2));

                $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParContinentGraphe($dateDebut1, $dateFin1);

                $listerdemande2 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParContinentGraphe($dateDebut2, $dateFin2);

                $i = 0;
                foreach ($listerdemande as $demande) {
                    $tabResult[$i][0] = $this->get('translator')->trans($demande[0]);
                    $tabResult[$i][1] = $demande[1];
                    $i = $i + 1;
                }

                $i = 0;
                foreach ($listerdemande2 as $demande2) {
                    $tabResult2[$i][0] = $this->get('translator')->trans($demande2[0]);
                    $tabResult2[$i][1] = $demande2[1];
                    $i = $i + 1;
                }
                //die(dump($tabResult));

                $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
                $form->bind($request);

                return $this->render('DefaultBundle:Default:statistiques-continent-graphe.html.twig', array('listerdemande' => $listerdemande,
                    'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage,
                    'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
            }

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParContinentGraphe($dateDebut, $dateFin);
            //die(dump($listerdemande));
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $this->get('translator')->trans($demande[0]);
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-continent-graphe.html.twig', array('listerdemande' => $listerdemande,
            'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage,
            'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-pays-excel/{typePlage}",name="statistiques-pays-excel",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesPaysExcelAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        //die(dump($typePlage));

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPays($dateDebut, $dateFin, $idLangue);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPays($dateDebut, $dateFin, $idLangue);

        }

        //die(dump($listerdemande));

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0];
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-pays-excel.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-pays-graphe/{typePlage}",name="statistiques-pays-graphe",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesPaysGrapheAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $tabResult = null;
        $tabResult2 = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        $typeGraphe = "column";
        $codeContinent = "ALL";

        $listerdemande = null; //$em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPays($dateDebut, $dateFin, $idLangue);
        $continents = $em->getRepository('BanquemondialeBundle:Continent')->findAll();

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];
            $typeGraphe = $data['typeGraphe'];

            if (isset($request->request->all()['continent'])) {
                $codeContinent = $request->request->all()['continent'];
            } else {
                $codeContinent = null;
            }


            if ($typeGraphe === "line") {
                $typeGraphe = "scatter";
            }

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }

            if ($typeGraphe == 'scatter') {

                if ($typePlage == 1) {
                    $dateDebut1 = $data['dateCreationDebut'];
                    $dateFin1 = "31-12-" . substr($data['dateCreationDebut'], 6, 4);

                    $dateDebut2 = "01-01-" . substr($data['dateCreationFin'], 6, 4);
                    $dateFin2 = $data['dateCreationFin'];
                } else if ($typePlage == 2) {
                    $dateDebut1 = "01-" . $data['dateCreationDebut'];
                    $dateFin1 = "31-12-" . substr($data['dateCreationDebut'], 3, 7);

                    $dateDebut2 = "01-01-" . substr($data['dateCreationFin'], 3, 7);
                    $dateFin2 = "31-" . $data['dateCreationFin'];
                } else {
                    $dateDebut1 = '01-01-' . $data['dateCreationDebut'];
                    $dateFin1 = '31-12-' . $data['dateCreationDebut'];

                    $dateDebut2 = '01-01-' . $data['dateCreationFin'];
                    $dateFin2 = '31-12-' . $data['dateCreationFin'];
                }


                $plagePays = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPays($dateDebut1, $dateFin2, $idLangue, $codeContinent);
                $paysExistant = [];

                foreach ($plagePays as $element) {
                    array_push($paysExistant, $element[0]);
                }
                //die(dump($paysExistant));

                //die(dump($dateDebut1." ".$dateFin1." et ".$dateDebut2." ".$dateFin2));

                $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPays($dateDebut1, $dateFin1, $idLangue, $codeContinent);
                //die(dump($listerdemande));
                $listerdemande2 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPays($dateDebut2, $dateFin2, $idLangue, $codeContinent);

                $i = 0;
                foreach ($paysExistant as $lePays) {
                    $j = 0;
                    if ($listerdemande != array()) {
                        foreach ($listerdemande as $result) {
                            if ($result[0] === $lePays) {
                                $tabResult[$i][0] = $this->get('translator')->trans($lePays);
                                $tabResult[$i][1] = $result[1];
                                break;
                            } else {
                                $tabResult[$i][0] = $this->get('translator')->trans($lePays);
                                $tabResult[$i][1] = 0;
                            }

                            $j++;
                        }
                        $i++;
                    } else {

                        $tabResult[$i][0] = $this->get('translator')->trans($lePays);
                        $tabResult[$i][1] = 0;
                        $i++;
                    }
                }
                //die(dump($tabResult));
                $i = 0;
                foreach ($paysExistant as $lePays) {
                    $j = 0;
                    if ($listerdemande2 != array()) {
                        foreach ($listerdemande2 as $result) {
                            if ($result[0] === $lePays) {
                                $tabResult2[$i][0] = $this->get('translator')->trans($lePays);
                                $tabResult2[$i][1] = $result[1];
                                break;
                            } else {
                                $tabResult2[$i][0] = $this->get('translator')->trans($lePays);
                                $tabResult2[$i][1] = 0;
                            }

                            $j++;
                        }
                        $i++;
                    } else {
                        $tabResult2[$i][0] = $this->get('translator')->trans($lePays);
                        $tabResult2[$i][1] = 0;
                        $i++;
                    }
                }

                //die(dump($tabResult2));
                /*
                $i = 0;
                foreach ($listerdemande as $demande) {
                    $tabResult[$i][0] = $this->get('translator')->trans($demande[0]);
                    $tabResult[$i][1] = $demande[1];
                    $i = $i + 1;
                }

                $i = 0;
                foreach ($listerdemande2 as $demande2) {
                    $tabResult2[$i][0] = $this->get('translator')->trans($demande2[0]);
                    $tabResult2[$i][1] = $demande2[1];
                    $i = $i + 1;
                }
                */
                //die(dump($tabResult2));

                $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
                $form->bind($request);

                return $this->render('DefaultBundle:Default:statistiques-pays-graphe.html.twig', array('listerdemande' => $listerdemande,
                    'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage,
                    'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'continents' => $continents, 'codeContinent' => $codeContinent, 'form' => $form->createView()));
            } else if ($typeGraphe === "pie") {
                $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParOrigineGraphe($dateDebut, $dateFin);
            } else {
                $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPays($dateDebut, $dateFin, $idLangue, $codeContinent);
            }


            $i = 0;
            foreach ($listerdemande as $demande) {
                $tabResult[$i][0] = $this->get('translator')->trans($demande[0]);
                $tabResult[$i][1] = $demande[1];
                $i = $i + 1;
            }
        }


        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-pays-graphe.html.twig', array('listerdemande' => $listerdemande,
            'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage,
            'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'continents' => $continents, 'codeContinent' => $codeContinent, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-secteur-activite-excel/{typePlage}",name="statistiques-secteur-activite-excel",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesSecteurActiviteExcelAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        //die(dump($typePlage));

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParSecteurActivite($dateDebut, $dateFin, $idLangue);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParSecteurActivite($dateDebut, $dateFin, $idLangue);
        }

        //die(dump($listerdemande));

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0];
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-secteur-activite-excel.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-categorie-activite-excel/{typePlage}",name="statistiques-categorie-activite-excel",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesCategorieActiviteExcelAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        //die(dump($typePlage));

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParCategorieActivite($dateDebut, $dateFin, $idLangue);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParCategorieActivite($dateDebut, $dateFin, $idLangue);
        }

        //die(dump($listerdemande));

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0];
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-categorie-activite-excel.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-categorie-activite-graphe/{typePlage}",name="statistiques-categorie-activite-graphe",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesCategorieActiviteGrapheAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        $typeGraphe = "column";


        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParCategorieActivite($dateDebut, $dateFin, $idLangue);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];
            $typeGraphe = $data['typeGraphe'];

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParCategorieActivite($dateDebut, $dateFin, $idLangue);
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $this->get('translator')->trans($demande[0]);
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-categorie-activite-graphe.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-pays-cnss-graphe/{typePlage}",name="statistiques-pays-cnss-graphe",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesPaysCNSSGrapheAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $tabResult = null;
        $tabResult2 = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        $typeGraphe = "column";
        $codeContinent = "ALL";

        $listerdemande = null; //$em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPays($dateDebut, $dateFin, $idLangue);
        $continents = $em->getRepository('BanquemondialeBundle:Continent')->findAll();

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];
            $typeGraphe = $data['typeGraphe'];

            if (isset($request->request->all()['continent'])) {
                $codeContinent = $request->request->all()['continent'];
            } else {
                $codeContinent = null;
            }


            if ($typeGraphe === "line") {
                $typeGraphe = "scatter";
            }

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }

            if ($typeGraphe == 'scatter') {

                if ($typePlage == 1) {
                    $dateDebut1 = $data['dateCreationDebut'];
                    $dateFin1 = "31-12-" . substr($data['dateCreationDebut'], 6, 4);

                    $dateDebut2 = "01-01-" . substr($data['dateCreationFin'], 6, 4);
                    $dateFin2 = $data['dateCreationFin'];
                } else if ($typePlage == 2) {
                    $dateDebut1 = "01-" . $data['dateCreationDebut'];
                    $dateFin1 = "31-12-" . substr($data['dateCreationDebut'], 3, 7);

                    $dateDebut2 = "01-01-" . substr($data['dateCreationFin'], 3, 7);
                    $dateFin2 = "31-" . $data['dateCreationFin'];
                } else {
                    $dateDebut1 = '01-01-' . $data['dateCreationDebut'];
                    $dateFin1 = '31-12-' . $data['dateCreationDebut'];

                    $dateDebut2 = '01-01-' . $data['dateCreationFin'];
                    $dateFin2 = '31-12-' . $data['dateCreationFin'];
                }


                $plagePays = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPaysCNSS($dateDebut1, $dateFin2, $idLangue, $codeContinent);
                $paysExistant = [];

                foreach ($plagePays as $element) {
                    array_push($paysExistant, $element[0]);
                }

                $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPaysCNSS($dateDebut1, $dateFin1, $idLangue, $codeContinent);

                $listerdemande2 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPaysCNSS($dateDebut2, $dateFin2, $idLangue, $codeContinent);

                $i = 0;
                foreach ($paysExistant as $lePays) {
                    $j = 0;
                    if ($listerdemande != array()) {
                        foreach ($listerdemande as $result) {
                            if ($result[0] === $lePays) {
                                $tabResult[$i][0] = $this->get('translator')->trans($lePays);
                                $tabResult[$i][1] = $result[1];
                                break;
                            } else {
                                $tabResult[$i][0] = $this->get('translator')->trans($lePays);
                                $tabResult[$i][1] = 0;
                            }

                            $j++;
                        }
                        $i++;
                    } else {

                        $tabResult[$i][0] = $this->get('translator')->trans($lePays);
                        $tabResult[$i][1] = 0;
                        $i++;
                    }
                }

                $i = 0;
                foreach ($paysExistant as $lePays) {
                    $j = 0;
                    if ($listerdemande2 != array()) {
                        foreach ($listerdemande2 as $result) {
                            if ($result[0] === $lePays) {
                                $tabResult2[$i][0] = $this->get('translator')->trans($lePays);
                                $tabResult2[$i][1] = $result[1];
                                break;
                            } else {
                                $tabResult2[$i][0] = $this->get('translator')->trans($lePays);
                                $tabResult2[$i][1] = 0;
                            }

                            $j++;
                        }
                        $i++;
                    } else {
                        $tabResult2[$i][0] = $this->get('translator')->trans($lePays);
                        $tabResult2[$i][1] = 0;
                        $i++;
                    }
                }


                $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
                $form->bind($request);

                return $this->render('DefaultBundle:Default:statistiques-pays-graphe-cnss.html.twig', array('listerdemande' => $listerdemande,
                    'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage,
                    'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'continents' => $continents, 'codeContinent' => $codeContinent, 'form' => $form->createView()));
            } else if ($typeGraphe === "pie") {
                $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParOrigineCNSSGraphe($dateDebut, $dateFin);
            } else {
                $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPaysCNSS($dateDebut, $dateFin, $idLangue, $codeContinent);
            }


            $i = 0;
            foreach ($listerdemande as $demande) {
                $tabResult[$i][0] = $this->get('translator')->trans($demande[0]);
                $tabResult[$i][1] = $demande[1];
                $i = $i + 1;
            }
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-pays-graphe-cnss.html.twig', array('listerdemande' => $listerdemande,
            'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage,
            'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'continents' => $continents, 'codeContinent' => $codeContinent, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-categorie-activite-cnss-graphe/{typePlage}",name="statistiques-categorie-activite-cnss-graphe",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesCategorieActiviteCNSSGrapheAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        $typeGraphe = "column";


        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParCategorieActiviteCNSS($dateDebut, $dateFin, $idLangue);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];
            $typeGraphe = $data['typeGraphe'];

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParCategorieActiviteCNSS($dateDebut, $dateFin, $idLangue);
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $this->get('translator')->trans($demande[0]);
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-categorie-activite-graphe-cnss.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-periode-cnss-graphe-mois",name="statistiques-periode-cnss-graphe-mois")
     */
    public function statistiquesPeriodeCNSSGrapheMoisAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $tabResult2 = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = '01-' . substr($dateDebutT->format('d-m-Y'), 3, 7);
        $dateFinT = new \DateTime('now');
        $dateFin = '31-' . substr($dateFinT->format('d-m-Y'), 3, 7);
        $typeGraphe = "column";
        $plageDate = [];

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeCNSSGraphe($dateDebut, $dateFin, $plageDate, 'mois');
        $listerdemande2 = null;

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
            $dateDebut = '01-' . $data['dateCreationDebut'];
            $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];

            $plageDate = $this->date_range($dateDebut, $dateFin, 'next month', 'd-m-Y');
            //die(dump($plageDate));

            $typeGraphe = $data['typeGraphe'];

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeCNSSGraphe($dateDebut, $dateFin, $plageDate, 'mois');
            $listerdemande2 = null;

            if ($typeGraphe == "line") {
                $annee1 = substr($dateDebut, 6, 4);
                $annee2 = substr($dateFin, 6, 4);

                if ($annee1 >= $annee2) {
                    $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
                    $form->bind($request);

                    $translated = $this->get('translator')->trans("veuillez choisir deux année différentes");
                    $this->get('session')->getFlashBag()->add('error', $translated);

//					die(dump($annee1));
                    return $this->render('DefaultBundle:Default:statistiques-periode-graphe-mois-cnss.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
                }

                $dateDebut1 = $dateDebut;
                $dateFin1 = '31-12-' . $annee1;

                $dateDebut2 = '01-01-' . $annee2;
                $dateFin2 = $dateFin;

                $plageDate = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];

                $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeCNSSGrapheAnnuel($dateDebut1, $dateFin1, $plageDate, 'mois');
                $listerdemande2 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeCNSSGrapheAnnuel($dateDebut2, $dateFin2, $plageDate, 'mois');

                $i = 0;
                foreach ($listerdemande2 as $demande2) {
                    //$tabResult[$i][0] = $this->get('translator')->trans($demande[1]) . " " . $demande[2];
                    $tabResult2[$i][0] = $this->chiffreToMonth($demande2[1]);
                    $tabResult2[$i][1] = $demande2[3];
                    $i = $i + 1;
                }

            }
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            //$tabResult[$i][0] = $this->get('translator')->trans($demande[1]) . " " . $demande[2];
            $tabResult[$i][0] = $this->chiffreToMonth($demande[1]) . " " . $demande[2];
            $tabResult[$i][1] = $demande[3];
            $i = $i + 1;
        }

        $dateFin[0] = '0';
        $dateFin[1] = '1';

        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-periode-graphe-mois-cnss.html.twig', array('listerdemande' => $listerdemande, 'listerdemande2' => $listerdemande2, 'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-periode-cnss-graphe-annee",name="statistiques-periode-cnss-graphe-annee")
     */
    public function statistiquesPeriodeCNSSGrapheAnneeAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = '01-01-' . substr($dateDebutT->format('d-m-Y'), 6, 4);
        $dateFinT = new \DateTime('now');
        $dateFin = '31-12-' . substr($dateFinT->format('d-m-Y'), 6, 4);
        $typeGraphe = "column";
        $plageDate = [];

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeCNSSGraphe($dateDebut, $dateFin, $plageDate);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dateDebut = '01-01-' . $data['dateCreationDebut'];
            $dateFin = '31-12-' . $data['dateCreationFin'];
            $typeGraphe = $data['typeGraphe'];

            $plageDate = $this->date_range($dateDebut, $dateFin, '+1 year', 'd-m-Y');

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseParPeriodeCNSSGraphe($dateDebut, $dateFin, $plageDate);
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[2];
            $tabResult[$i][1] = $demande[3];
            $i = $i + 1;
        }

        $dateFin[0] = '0';
        $dateFin[1] = '1';


        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-periode-graphe-annee-cnss.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    function date_range($first, $last, $step = '+1 day', $output_format = 'd-m-Y')
    {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);
        while ($current <= $last) {

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }
        return $dates;
    }

    /**
     * @Route("/{_locale}/statistiques-periode-notaire-excel-jours",name="statistiques-periode-notaire-excel-jours")
     */
    public function statistiquesPeriodeNotaireExcelJoursAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeExcel($dateDebut, $dateFin, 'jours');

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeExcel($dateDebut, $dateFin, 'jours');
        }

        //die(dump($listerdemande));

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0];
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-notaire-periode-excel-jours.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-periode-notaire-excel-mois",name="statistiques-periode-notaire-excel-mois")
     */
    public function statistiquesPeriodeNotaireExcelMoisAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = '01-' . substr($dateDebutT->format('d-m-Y'), 3, 7);
        $dateFinT = new \DateTime('now');
        $dateFin = '31-' . substr($dateFinT->format('d-m-Y'), 3, 7);

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeExcel($dateDebut, $dateFin, 'mois');

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
            $dateDebut = '01-' . $data['dateCreationDebut'];
            $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeExcel($dateDebut, $dateFin, 'mois');
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0];
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        $dateFin[0] = '0';
        $dateFin[1] = '1';


        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-notaire-periode-excel-mois.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-periode-notaire-excel-annee",name="statistiques-periode-notaire-excel-annee")
     */
    public function statistiquesPeriodeNotaireExcelAnneeAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = '01-01-' . substr($dateDebutT->format('d-m-Y'), 6, 4);
        $dateFinT = new \DateTime('now');
        $dateFin = '31-12-' . substr($dateFinT->format('d-m-Y'), 6, 4);

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeExcel($dateDebut, $dateFin);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dateDebut = '01-01-' . $data['dateCreationDebut'];
            $dateFin = '31-12-' . $data['dateCreationFin'];

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeExcel($dateDebut, $dateFin);
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0];
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        $dateFin[0] = '0';
        $dateFin[1] = '1';

        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-notaire-periode-excel-annee.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-periode-notaire-graphe-jours",name="statistiques-periode-notaire-graphe-jours")
     */
    public function statistiquesPeriodeNotaireGrapheJoursAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        $typeGraphe = "column";
        $plageDate = [];

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeGraphe($dateDebut, $dateFin, $plageDate, 'jours');

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];
            $typeGraphe = $data['typeGraphe'];

            $plageDate = $this->date_range($dateDebut, $dateFin, '+1 day', 'd-m-Y');
            //die(dump($typeGraphe));
            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeGraphe($dateDebut, $dateFin, $plageDate, 'jours');
            //die(dump($plageDate));
        }

        //die(dump($listerdemande));

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0] . "-" . $this->get('translator')->trans($demande[1]) . "-" . $demande[2];
            $tabResult[$i][1] = $demande[3];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-notaire-periode-graphe-jours.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-periode-notaire-graphe-mois",name="statistiques-periode-notaire-graphe-mois")
     */
    public function statistiquesPeriodeNotaireGrapheMoisAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $tabResult2 = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = '01-' . substr($dateDebutT->format('d-m-Y'), 3, 7);
        $dateFinT = new \DateTime('now');
        $dateFin = '31-' . substr($dateFinT->format('d-m-Y'), 3, 7);
        $typeGraphe = "column";
        $plageDate = [];

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeGraphe($dateDebut, $dateFin, $plageDate, 'mois');
        $listerdemande2 = null;

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
            $dateDebut = '01-' . $data['dateCreationDebut'];
            $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];

            $plageDate = $this->date_range($dateDebut, $dateFin, 'next month', 'd-m-Y');
            //die(dump($plageDate));

            $typeGraphe = $data['typeGraphe'];

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeGraphe($dateDebut, $dateFin, $plageDate, 'mois');
            $listerdemande2 = null;

            if ($typeGraphe == "line") {
                $annee1 = substr($dateDebut, 6, 4);
                $annee2 = substr($dateFin, 6, 4);

                if ($annee1 >= $annee2) {
                    $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
                    $form->bind($request);

                    $translated = $this->get('translator')->trans("veuillez choisir deux année différentes");
                    $this->get('session')->getFlashBag()->add('error', $translated);

//					die(dump($annee1));
                    return $this->render('DefaultBundle:Default:statistiques-notaire-periode-graphe-mois.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
                }

                $dateDebut1 = $dateDebut;
                $dateFin1 = '31-12-' . $annee1;

                $dateDebut2 = '01-01-' . $annee2;
                $dateFin2 = $dateFin;

                $plageDate = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];

                $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeGrapheAnnuel($dateDebut1, $dateFin1, $plageDate, 'mois');
                $listerdemande2 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeGrapheAnnuel($dateDebut2, $dateFin2, $plageDate, 'mois');

                $i = 0;
                foreach ($listerdemande2 as $demande2) {
                    //$tabResult[$i][0] = $this->get('translator')->trans($demande[1]) . " " . $demande[2];
                    $tabResult2[$i][0] = $this->chiffreToMonth($demande2[1]);
                    $tabResult2[$i][1] = $demande2[3];
                    $i = $i + 1;
                }

            }
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            //$tabResult[$i][0] = $this->get('translator')->trans($demande[1]) . " " . $demande[2];
            $tabResult[$i][0] = $this->chiffreToMonth($demande[1]) . " " . $demande[2];
            $tabResult[$i][1] = $demande[3];
            $i = $i + 1;
        }

        $dateFin[0] = '0';
        $dateFin[1] = '1';

        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-notaire-periode-graphe-mois.html.twig', array('listerdemande' => $listerdemande, 'listerdemande2' => $listerdemande2, 'tabResult' => $tabResult, 'tabResult2' => $tabResult2, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-periode-notaire-graphe-annee",name="statistiques-periode-notaire-graphe-annee")
     */
    public function statistiquesPeriodeNotaireGrapheAnneeAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = '01-01-' . substr($dateDebutT->format('d-m-Y'), 6, 4);
        $dateFinT = new \DateTime('now');
        $dateFin = '31-12-' . substr($dateFinT->format('d-m-Y'), 6, 4);
        $typeGraphe = "column";
        $plageDate = [];

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeGraphe($dateDebut, $dateFin, $plageDate);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dateDebut = '01-01-' . $data['dateCreationDebut'];
            $dateFin = '31-12-' . $data['dateCreationFin'];
            $typeGraphe = $data['typeGraphe'];

            $plageDate = $this->date_range($dateDebut, $dateFin, '+1 year', 'd-m-Y');

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listEntrepriseNotaireParPeriodeGraphe($dateDebut, $dateFin, $plageDate);
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[2];
            $tabResult[$i][1] = $demande[3];
            $i = $i + 1;
        }

        $dateFin[0] = '0';
        $dateFin[1] = '1';


        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-notaire-periode-graphe-annee.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-formJuridique-notaire-excel/{typePlage}",name="statistiques-formJuridique-notaire-excel",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesNbEntrepriseByFormeJuridiqueNotaireExcelAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        //die(dump($typePlage));

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseNotaireParFormeJurique($dateDebut, $dateFin, $langue->getId());

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];

            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseNotaireParFormeJurique($dateDebut, $dateFin, $langue->getId());
        }

        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $demande[0];
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-notaire-formeJuridique-excel.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/statistiques-formJuridique-notaire-graphe/{typePlage}",name="statistiques-formJuridique-notaire-graphe",requirements={"typePlage" = "\d+"}, defaults={"typePlage" = 1})
     */
    public function statistiquesNbEntrepriseByFormeJuridiqueNotaireGrapheAction($typePlage)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();
        $tabResult = null;
        $tabResult1 = null;
        $tabResult2 = null;
        $tabResult3 = null;
        $dateDebutT = new \DateTime('now');
        $dateDebut = $dateDebutT->format('d-m-Y');
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');
        $typeGraphe = "column";

        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseNotaireParFormeJurique($dateDebut, $dateFin, $idLangue);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statGrapheType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];
            $typeGraphe = $data['typeGraphe'];

            if ($typePlage == 2) {
                $dernierJourMois = date('t', strtotime('01-' . $data['dateCreationFin']));
                $dateDebut = '01-' . $data['dateCreationDebut'];
                $dateFin = $dernierJourMois . '-' . $data['dateCreationFin'];
            } else if ($typePlage == 3) {
                $dateDebut = '01-01-' . $data['dateCreationDebut'];
                $dateFin = '31-12-' . $data['dateCreationFin'];
            }


            if ($typePlage != 1 && $typeGraphe == 'column') {
                $plageDate = $this->date_range($dateDebut, $dateFin, 'next month', 'd-m-Y');

                $listerdemande1 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseNotaireParFormeJuriqueGrapheAnnuel($dateDebut, $dateFin, $idLangue, "EI", $plageDate);

                $listerdemande2 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseNotaireParFormeJuriqueGrapheAnnuel($dateDebut, $dateFin, $idLangue, "SARL/SARLU", $plageDate);

                $listerdemande3 = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseNotaireParFormeJuriqueGrapheAnnuel($dateDebut, $dateFin, $idLangue, "AUTRE", $plageDate);


                $i = 0;
                foreach ($listerdemande1 as $demande1) {
                    $tabResult1[$i][0] = $this->chiffreToMonth($demande1[1]) . " " . $demande1[2];
                    $tabResult1[$i][1] = $demande1[3];
                    $tabResult1[$i][2] = $this->get('translator')->trans("IND");
                    $i = $i + 1;
                }

                $i = 0;
                foreach ($listerdemande2 as $demande2) {
                    $tabResult2[$i][0] = $this->chiffreToMonth($demande2[1]) . " " . $demande2[2];
                    $tabResult2[$i][1] = $demande2[3];
                    $tabResult2[$i][2] = $this->get('translator')->trans("SARL/SARLU");
                    $i = $i + 1;
                }

                $i = 0;
                foreach ($listerdemande3 as $demande3) {
                    $tabResult3[$i][0] = $this->chiffreToMonth($demande3[1]) . " " . $demande3[2];
                    $tabResult3[$i][1] = $demande3[3];
                    $tabResult3[$i][2] = $this->get('translator')->trans("AUTRE");
                    $i = $i + 1;
                }

                $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
                $form->bind($request);

                return $this->render('DefaultBundle:Default:statistiques-notaire-formeJuridique-graphe.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'tabResult1' => $tabResult1, 'tabResult2' => $tabResult2, 'tabResult3' => $tabResult3, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));

            }

            $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->listeNbEntrepriseNotaireParFormeJurique($dateDebut, $dateFin, $idLangue);
        }


        $i = 0;
        foreach ($listerdemande as $demande) {
            $tabResult[$i][0] = $this->get('translator')->trans($demande[0]);
            $tabResult[$i][1] = $demande[1];
            $i = $i + 1;
        }

        $form = $this->createForm(new StatistiqueGrapheType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:statistiques-notaire-formeJuridique-graphe.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'tabResult1' => $tabResult1, 'tabResult2' => $tabResult2, 'tabResult3' => $tabResult3, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'typePlage' => $typePlage, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'typeGraphe' => $typeGraphe, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/processus-diaspora",name="telecharger_processus_diaspora")
     */
    public function telechargerProcessusDiasporaPdfAction()
    {

        $font = 'dejavuserifcondensed';
        $html = $this->renderView('DefaultBundle:Default:diaspora_content.html.twig');
        $nomFichier = "processus.pdf"; //cofifier après rccm
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->setDefaultFont($font);
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($nomFichier, 'D');
        exit;
    }

    /**
     * @Route("/{_locale}/visualiserAnnonceExporte/{idd}",name="visualiser-annonce-exporte")
     */
    public function visualiserAnnonceExporteAction(Request $request, DossierDemande $idd)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();

        if ($pole and $pole->getSigle() != "AL") {
            return $this->redirectToRoute('accueil');
        }

        $idPole = 1;
        $idPole = $pole->getId();

        $em = $this->getDoctrine()->getManager();

        $greffe = $em->getRepository('ParametrageBundle:Pole')->findOneBySigle("GF");
        $idGreffe = $greffe->getId();

        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }

        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);

        $data['numeroDossier'] = $dossierDemande->getNumeroDossier();
        $data['denominationSociale'] = "";
        $data['dateSoumissionDebut'] = "";
        $data['dateSoumissionFin'] = "";
        $data['formeJuridique'] = "";
        $data['typeDossier'] = "";
        $data['numRccmFormalite'] = "";
        $data['numRccmEntreprise'] = "";

        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierAnnonceur($user, $data, $langue->getId(), $idPole, null, 2);
        //die(dump($dossierDemande->getNumeroDossier()));
        if (sizeof($listerdemande) > 0) {
            try {
                if ($listerdemande[0]['libelleTypeDossier'] == 'Notaire' && $listerdemande[0]['sigleFormeJuridique'] != 'EI') {

                    $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findAnnonceLegaleAnnonceurNotaire($user, $data, $langue->getId(), $idPole, null, 2);

                    $this->annonceLegalePdfPortailAction($listerdemande);
                } else {

                    $this->annonceLegalePdfAction($listerdemande);
                }
            } catch (Exception $e) {

            }
        }
        exit;
    }

    public function chiffreToMonth($moisEnChiffre)
    {
        $mois = "";

        switch ($moisEnChiffre) {
            case "01":
                $mois = $this->get('translator')->trans("janvier");
                break;
            case "02":
                $mois = $this->get('translator')->trans("fevrier");
                break;
            case "03":
                $mois = $this->get('translator')->trans("mars");
                break;
            case "04":
                $mois = $this->get('translator')->trans("avril");
                break;
            case "05":
                $mois = $this->get('translator')->trans("mai");
                break;
            case "06":
                $mois = $this->get('translator')->trans("juin");
                break;
            case "07":
                $mois = $this->get('translator')->trans("juillet");
                break;
            case "08":
                $mois = $this->get('translator')->trans("aout");
                break;
            case "09":
                $mois = $this->get('translator')->trans("septembre");
                break;
            case "10":
                $mois = $this->get('translator')->trans("octobre");
                break;
            case "11":
                $mois = $this->get('translator')->trans("novembre");
                break;
            case "12":
                $mois = $this->get('translator')->trans("decembre");
                break;
        }

        return $mois;
    }

    /**
     * @Route("/{_locale}/extraction-nbEntreprise-excel",name="extraction-nbEntreprise-excel")
     */
    public function extractionEntrepriseCreeParPeriodeAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $tabResult = null;
        $dateDebut = null;
        $dateFinT = new \DateTime('now');
        $dateFin = $dateFinT->format('d-m-Y');

        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findListeDesEntrepriseForExportExcel($dateDebut, $dateFin, $langue->getId(), 25);

        if ($request->getMethod() == 'POST') {
            $data = $request->request->all()['statType'];
            $dateDebut = $data['dateCreationDebut'];
            $dateFin = $data['dateCreationFin'];
            $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findListeDesEntrepriseForExportExcel($dateDebut, $dateFin, $langue->getId());
        }

        //die(dump($listerdemande));


        //die(dump($tabResult));

        $form = $this->createForm(new StatistiqueType(array('langue' => $langue)));
        $form->bind($request);

        return $this->render('DefaultBundle:Default:extraction-Entreprise-excel.html.twig', array('listerdemande' => $listerdemande, 'tabResult' => $tabResult, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'locale' => $codLang, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/validerDossierChefGreffeAction",name="validerDossierChefGreffeAction")
     */
    public function validerDossierChefGreffeAction(Request $request)
    {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        $user_manager = $this->get('fos_user.user_manager');
        $factory = $this->get('security.encoder_factory');

        //$user = $user_manager->loadUserByUsername($username);

        $encoder = $factory->getEncoder($user);

        $idd = $request->get('idd');
        $password = $request->get('mdp');

        $mdpCorrect = $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());

        if ($mdpCorrect) {
            $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
            $codeFormJ = $dossierDemande->getFormeJuridique()->getSigle();
            //die(dump($codeFormJ));
            if (strtolower($codeFormJ) == "ei") {
                $this->validerChefGreffeP1($idd, $pole);
            } else if (strtolower($codeFormJ) == "gie") {
                $this->validerChefGreffeG1($idd, $pole);
            } else {
                $this->validerChefGreffeM0($idd, $pole);
            }

            //success
            return new JsonResponse(array('resultat' => '1'));
        } else {
            //mdp incorrect
            return new JsonResponse(array('resultat' => '2'));
        }


        //erreur
        return new JsonResponse(array('resultat' => '0'));
    }

    public function validerChefGreffeM0($idd, $pole)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = "fr";
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        $dateActuelle = new \DateTime();
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

        $afficherSignature = 0;
        $afficherQRCodeGreffe = 0;
        $libelleSignatureGreffe = "Me Alseny Fofana Greffier en chef du TPI de Kaloum";

        $parametrageSignature = $em->getRepository('DefaultBundle:ReglageActivation')->findFirst();
        if ($parametrageSignature) {
            $afficherSignature = $parametrageSignature->getIsSignatureVisible();
            $afficherQRCodeGreffe = $parametrageSignature->getIsQRVisible();
            $libelleSignatureGreffe = $parametrageSignature->getLibelleSignatureGreffe();
        }
        if ($afficherQRCodeGreffe) {
            $baseUrl = $em->getRepository('ParametrageBundle:Chemins')->find(3)->getNom();
            //$key = "numeroDossier=".$dossierDemande->getNumeroDossier()."&rccm=".$rccm->getNumRccmEntreprise();
            $key = "numero=" . $dossierDemande->getId();
            $keyCodee = $this->crypter($key);

            //$parametres = "/fr/verification-document-rccm?key=".urlencode($keyCodee);
            $parametres = "/fr/verif?key=" . urlencode($keyCodee);

            if ($listeDirigeants[0]) {
                $textQr = "RCCM: " . $rccm->getNumRccmEntreprise() . " GERANT: " . strtoupper($listeDirigeants[0]['prenom']) . "\n\n " . strtoupper($listeDirigeants[0]['nom']) . "\n\n " . $baseUrl . $parametres;
            } else {
                $textQr = "RCCM: " . $rccm->getNumRccmEntreprise() . "\n\n " . $baseUrl . $parametres;
            }
            //$textQr = $baseUrl.$parametres;

            $qrCode = new QrCode($textQr);
            $qrCode->setSize(180);
            //$qrCode = $this->get('endroid.qrcode.factory')->create('QR Code', ['size' => 180]);
            $path = $this->get('kernel')->getRootDir() . '/../web/img/qrcodegreffe.png';
            // Save it to a file
            $qrCode->writeFile($path);
        }

        $temp = $cheminDownload . $idd . '\\';
        if (!is_dir($temp)) {
            mkdir($temp);
        }

        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserChefGreffeM0.html.twig', array('idd' => $idd,
            'activites' => $lesActivites, 'dd' => $dossierDemande, 'listeDirigeants' => $listeDirigeants, 'listeTypeOrigine' => $listeTypeOrigine,
            'listeAssocies' => $listeAssocies, 'listeComissaires' => $listeCommissareAuxCptes, 'listeTypeFormalite' => $listeTypeFormalite,
            'origine' => $origine, 'dateRccm' => $dateRccm, 'rccm' => $rccm, 'soussigne' => $soussigne, 'libelleSignatureGreffe' => $libelleSignatureGreffe,
            'afficherSignature' => $afficherSignature, 'afficherQRCodeGreffe' => $afficherQRCodeGreffe, 'documentValide' => true));


        $leFormulaire_a_delive = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->getNomFormulaireDelivre($pole->getId(), 'RCCM');
        $nomFichier = "formulaire" . $idd . "_" . $leFormulaire_a_delive->getId() . ".pdf";


        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', array(5, 5, 5, 0));
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);

        $html2pdf->Output($cheminDownload . $idd . '\\' . $nomFichier, 'F');

        $dossierDemande->setDateValidationChefGreffe($dateActuelle);
        $dossierDemande->setStatutValidationChefGreffe(2);
        $em->persist($dossierDemande);
        $em->flush();
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

        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserP1.html.twig', array('idd' => $idd, 'dd' => $dossierDemande, 'rep' => $representant[0], 'listeTypeOrigine' => $listeTypeOrigine, 'origine' => $origine,
            'pole' => $pole, 'listeTypeFormalite' => $listeTypeFormalite, 'dateRccm' => $dateRccm,
            'rccm' => $rccm,
            'activiteAnterieure' => $activiteAnterieure, 'personneEngageurs' => $personneEngageurs, 'activitePrincipale' => $activitePrincipale,
            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2));
        $leFormulaire_a_delive = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->getNomFormulaireDelivre($pole->getId(), "RCC");
        $nomFichier = "formulaire" . $idd . "_" . $leFormulaire_a_delive->getId() . ".pdf";

        $em->flush();
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        //$html2pdf->Output('document.pdf');
        //exit;

        $html2pdf->Output($cheminDownload . $idd . '\\' . $nomFichier, 'F');
    }

    public function validerChefGreffeP1($idd, $pole)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = "fr";
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);
        $dateRccm = new \DateTime();
        $dateActuelle = new \DateTime();
        if ($rccm) {
            $dateRccm = $rccm->getDate();
        }

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


        $afficherSignature = 0;
        $afficherQRCodeGreffe = 0;
        $libelleSignatureGreffe = "Me Alseny Fofana Greffier en chef du TPI de Kaloum";

        $parametrageSignature = $em->getRepository('DefaultBundle:ReglageActivation')->findFirst();
        if ($parametrageSignature) {
            $afficherSignature = $parametrageSignature->getIsSignatureVisible();
            $afficherQRCodeGreffe = $parametrageSignature->getIsQRVisible();
            $libelleSignatureGreffe = $parametrageSignature->getLibelleSignatureGreffe();
        }

        if ($afficherQRCodeGreffe) {
            $baseUrl = $em->getRepository('ParametrageBundle:Chemins')->find(3)->getNom();
            //$key = "numeroDossier=".$dossierDemande->getNumeroDossier()."&rccm=".$rccm->getNumRccmEntreprise();
            $key = "numero=" . $dossierDemande->getId();
            $keyCodee = $this->crypter($key);

            //$parametres = "/fr/verification-document-rccm?key=".urlencode($keyCodee);
            $parametres = "/fr/verif?key=" . urlencode($keyCodee);

            if ($representant[0]) {
                $textQr = "RCCM: " . $rccm->getNumRccmEntreprise() . " GERANT: " . strtoupper($representant[0]['prenom']) . "\n\n " . strtoupper($representant[0]['nom']) . "\n\n " . $baseUrl . $parametres;
            } else {
                $textQr = "RCCM: " . $rccm->getNumRccmEntreprise() . "\n\n " . $baseUrl . $parametres;
            }
            //$textQr = $baseUrl.$parametres;

            $qrCode = new QrCode($textQr);
            $qrCode->setSize(180);
            //$qrCode = $this->get('endroid.qrcode.factory')->create('QR Code', ['size' => 180]);
            $path = $this->get('kernel')->getRootDir() . '/../web/img/qrcodegreffe.png';
            // Save it to a file
            $qrCode->writeFile($path);
        }


        $temp = $cheminDownload . $idd . '\\';
        if (!is_dir($temp)) {
            mkdir($temp);
        }

        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserChefGreffeP1.html.twig', array('idd' => $idd, 'dd' => $dossierDemande, 'rep' => $representant[0], 'listeTypeOrigine' => $listeTypeOrigine, 'origine' => $origine,
            'pole' => $pole, 'listeTypeFormalite' => $listeTypeFormalite, 'dateRccm' => $dateRccm, 'rccm' => $rccm,
            'activiteAnterieure' => $activiteAnterieure, 'personneEngageurs' => $personneEngageurs, 'conjoints' => $conjoints,
            'activitePrincipale' => $activitePrincipale, 'activiteSecondaire' => $activiteSecondaire,
            'activiteSecondaire2' => $activiteSecondaire2, 'afficherSignature' => $afficherSignature,
            'afficherQRCodeGreffe' => $afficherQRCodeGreffe, 'libelleSignatureGreffe' => $libelleSignatureGreffe, 'documentValide' => true));

        $leFormulaire_a_delive = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->getNomFormulaireDelivre($pole->getId(), 'RCCM');
        $nomFichier = "formulaire" . $idd . "_" . $leFormulaire_a_delive->getId() . ".pdf";


        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);


        $html2pdf->Output($cheminDownload . $idd . '\\' . $nomFichier, 'F');


        $dossierDemande->setDateValidationChefGreffe($dateActuelle);
        $dossierDemande->setStatutValidationChefGreffe(2);
        $em->persist($dossierDemande);
        $em->flush();
    }

    public function validerChefGreffeG1($idd, $pole)
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

        $dateActuelle = new \DateTime();

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

        $dossier = $em->getRepository('BanquemondialeBundle:dossierDemande')->find($idd);
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


        $afficherSignature = 0;
        $afficherQRCodeGreffe = 0;
        $libelleSignatureGreffe = "Me Alseny Fofana Greffier en chef du TPI de Kaloum";

        $parametrageSignature = $em->getRepository('DefaultBundle:ReglageActivation')->findFirst();
        if ($parametrageSignature) {
            $afficherSignature = $parametrageSignature->getIsSignatureVisible();
            $afficherQRCodeGreffe = $parametrageSignature->getIsQRVisible();
            $libelleSignatureGreffe = $parametrageSignature->getLibelleSignatureGreffe();
        }
        if ($afficherQRCodeGreffe) {
            $baseUrl = $em->getRepository('ParametrageBundle:Chemins')->find(3)->getNom();
            $key = "numero=" . $dossier->getId();
            $keyCodee = $this->crypter($key);

            $parametres = "/fr/verif?key=" . urlencode($keyCodee);

            if ($representant[0]) {
                $textQr = "RCCM: " . $rccm->getNumRccmEntreprise() . " GERANT: " . strtoupper($representant[0]['prenom']) . "\n\n " . strtoupper($representant[0]['nom']) . "\n\n " . $baseUrl . $parametres;
            } else {
                $textQr = "RCCM: " . $rccm->getNumRccmEntreprise() . "\n\n " . $baseUrl . $parametres;
            }
            //$textQr = $baseUrl.$parametres;

            $qrCode = new QrCode($textQr);
            $qrCode->setSize(180);
            //$qrCode = $this->get('endroid.qrcode.factory')->create('QR Code', ['size' => 180]);
            $path = $this->get('kernel')->getRootDir() . '/../web/img/qrcodegreffe.png';
            // Save it to a file
            $qrCode->writeFile($path);
        }

        $temp = $cheminDownload . $idd . '\\';
        if (!is_dir($temp)) {
            mkdir($temp);
        }


        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserChefGreffeG1.html.twig', array('idd' => $idd, 'representant' => $representant
        , 'activites' => $lesActivites, 'dossier' => $dossier, 'formeJ' => $formJ, 'secAct' => $secAct, 'associe' => $associe,
            'afficherSignature' => $afficherSignature, 'afficherQRCodeGreffe' => $afficherQRCodeGreffe, 'libelleSignatureGreffe' => $libelleSignatureGreffe
        , "numSequentiel" => $numSequentiel, 'rccm' => $rccm, 'typeF' => $typeF, 'leRepresentant' => $leRepresentant, 'soussigne' => $soussigne
        , 'documentValide' => true));


        $leFormulaire_a_delive = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->getNomFormulaireDelivre($pole->getId(), 'RCCM');
        $nomFichier = "formulaire" . $idd . "_" . $leFormulaire_a_delive->getId() . ".pdf";


        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);


        $html2pdf->Output($cheminDownload . $idd . '\\' . $nomFichier, 'F');


        $dossier->setDateValidationChefGreffe($dateActuelle);
        $dossier->setStatutValidationChefGreffe(2);
        $em->persist($dossier);
        $em->flush();


    }

    /**
     * @Route("/{_locale}/verif",name="verification-document-rccm")
     */
    public function verificationDocumentRccmAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $code = $request->getLocale();
        $dossierRccm = null;
        $gerant = null;
        try {
            $key = $request->query->get('key');
            $key = parse_url($key)['path'];
            $keyDecryptee = $this->decrypter($key);

            $parties = explode("=", $keyDecryptee);

            $dossierRccm = $em->getRepository('BanquemondialeBundle:Rccm')->findDossierValideById($parties[1]);

            if ($dossierRccm) {
                $listeDirigeants = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierRccm->getDossierDemande()->getId(), $langue->getId());

                if ($listeDirigeants) {
                    $gerant = $listeDirigeants[0];
                }
            }


        } catch (\Exception $ex) {
            $translated = $this->get('translator')->trans('erreur_verification_document_rccm');
            $this->get('session')->getFlashBag()->add('error', $translated);
            return $this->render('DefaultBundle:Default:verification-document-rccm.html.twig', array('langues' => $lgs, 'dossierRccm' => $dossierRccm, 'gerant' => $gerant));
        }

        return $this->render('DefaultBundle:Default:verification-document-rccm.html.twig', array('langues' => $lgs, 'dossierRccm' => $dossierRccm, 'gerant' => $gerant));
    }

    function crypter($maChaineACrypter)
    {
        //$maCleDeCryptage = md5($maCleDeCryptage);
        $maCleDeCryptage = "Xy14o!%14OR021$*45;+0)=Hh(+-**At";
        $letter = -1;
        $newstr = "";
        $strlen = strlen($maChaineACrypter);
        for ($i = 0; $i < $strlen; $i++) {
            $letter++;
            if ($letter > 31) {
                $letter = 0;
            }
            $neword = ord($maChaineACrypter{$i}) + ord($maCleDeCryptage{$letter});
            if ($neword > 255) {
                $neword -= 256;
            }
            $newstr .= chr($neword);
        }
        return base64_encode($newstr);
    }

    function decrypter($maChaineADecrypter)
    {
        //$maCleDeCryptage = md5($maCleDeCryptage);
        $maCleDeCryptage = "Xy14o!%14OR021$*45;+0)=Hh(+-**At";
        $letter = -1;
        $newstr = "";
        $maChaineADecrypter = base64_decode($maChaineADecrypter);
        $strlen = strlen($maChaineADecrypter);
        for ($i = 0; $i < $strlen; $i++) {
            $letter++;
            if ($letter > 31) {
                $letter = 0;
            }
            $neword = ord($maChaineADecrypter{$i}) - ord($maCleDeCryptage{$letter});
            if ($neword < 1) {
                $neword += 256;
            }
            $newstr .= chr($neword);
        }
        return $newstr;
    }

    /**
     * @Route("/{_locale}/signature",name="signature_chef_greffe")
     */
    public function parametrageSignatureChefGreffeAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $reglageActivation = $em->getRepository('DefaultBundle:ReglageActivation')->findFirst();

        //die(dump($reglageActivation));
        if (!$reglageActivation) {
            $reglageActivation = new ReglageActivation();
        }
        $form = $this->createForm('DefaultBundle\Form\ReglageActivationType', $reglageActivation);
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST') {

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($reglageActivation);
                $em->flush();
                $translated = $this->get('translator')->trans('parametrage_signature_modification_success');
                $this->get('session')->getFlashBag()->add('info', $translated);
            } else {
                $translated = $this->get('translator')->trans('parametrage_signature_modification_echec');
                $this->get('session')->getFlashBag()->add('error', $translated);
            }
        }
        return $this->render('DefaultBundle:Default:parametrageSignatureChefGreffe.html.twig', array('reglageActivation' => $reglageActivation, 'form' => $form->createView()));
    }

    /**
     * @Route("/{_locale}/uploadSignature",name="upload_signature")
     */
    public function changerSignatureAction()
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $reglageActivation = $em->getRepository('DefaultBundle:ReglageActivation')->findFirst();
        $form = $this->createForm('DefaultBundle\Form\ReglageActivationType', $reglageActivation);
        $form->handleRequest($request);

        $tailleMaximum = 20000000;
        $message = "";
        $bestwidth = 382;
        $bestheight = 120;

        if ($request->getMethod() == 'POST') {
            if (isset($_FILES['fileSignature'])) {
                $_FILES['fileSignature']['name'];

                if ($_FILES['fileSignature']['type'] != "image/png") {
                    $message = $this->get('translator')->trans("fichier_non_png");
                    $translated = $this->get('translator')->trans('fichier_non_png');
                    $this->get('session')->getFlashBag()->add('error', $translated);
                    return $this->render('DefaultBundle:Default:menuSignature.html.twig', array('message' => $message, 'form' => $form->createView()));
                }

                $image_sizes = getimagesize($_FILES['fileSignature']['tmp_name']);

                if ($image_sizes[0] != $bestwidth OR $image_sizes[1] > $bestheight) {
                    $message = $this->get('translator')->trans("signature_mauvaise_dimensions");
                    $translated = $this->get('translator')->trans('signature_mauvaise_dimensions');
                    $this->get('session')->getFlashBag()->add('error', $translated);
                    //return $this->render('DefaultBundle:Default:parametrageSignatureChefGreffe.html.twig', array('message' => $message, 'form' => $form->createView()));
                }

                if ($_FILES['fileSignature']['size'] > $tailleMaximum) {
                    $message = $this->get('translator')->trans("message_fichier_trop_volumineux");
                    $translated = $this->get('translator')->trans('message_fichier_trop_volumineux');
                    $this->get('session')->getFlashBag()->add('error', $translated);
                    return $this->render('DefaultBundle:Default:parametrageSignatureChefGreffe.html.twig', array('message' => $message, 'form' => $form->createView()));
                }

                $cheminUpload = "../web/img/";

                $temp = $cheminUpload;
                if (!is_dir($temp) && $_FILES['fileSignature']['tmp_name']) {
                    mkdir($temp);
                }
                $resultat = move_uploaded_file($_FILES['fileSignature']['tmp_name'], $temp . "/signature.png");
            }


            if ($resultat) {
                $message = $this->get('translator')->trans("signature_remplace_succes");
                $translated = $this->get('translator')->trans('signature_remplace_succes');
                $this->get('session')->getFlashBag()->add('info', $translated);
            } else {
                $message = $this->get('translator')->trans("signature_remplace_erreur");
                $translated = $this->get('translator')->trans('signature_remplace_erreur');
                $this->get('session')->getFlashBag()->add('error', $translated);
            }
            //die(dump($_FILES['fileSignature']));
        }

        return $this->render('DefaultBundle:Default:parametrageSignatureChefGreffe.html.twig', array('message' => $message, 'form' => $form->createView()));
    }

    /**
     * @Route("/{datedebut}/{datefin}/{entreprise}/{poleChoisi}/{formeJuridique}/{modePaiement}/{idLangue}/statistiques-de-caisse-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="statistiques-de-caisse-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:Default:statistiques-de-caisse-excel.xls.twig")
     */
    public function brouillardCaisseEnExcelAction($datedebut, $datefin, $entreprise, $poleChoisi, $formeJuridique,$modePaiement,$idLangue)
    {
        $em=$this->getDoctrine()->getManager();
        $montantTotal = 0;
        if (!empty($entreprise)) {
            $caisse = $em->getRepository('BanquemondialeBundle:Entreprise')->find($entreprise);
            if ($caisse) {
                $nomCaisse = $caisse->getDenomination();
            }
        }
        $totaux = $em->getRepository('BanquemondialeBundle:RepartitionQuittance')->findRepartitionQuittanceByParametres($datedebut, $datefin, $entreprise, $poleChoisi, $formeJuridique,$modePaiement);

        $repartitions = $em->getRepository('BanquemondialeBundle:RepartitionQuittance')->findBrouillardByParametres($datedebut, $datefin, $entreprise, $poleChoisi, $formeJuridique, $idLangue,$modePaiement);
      //  die(dump('ok'));
        if (!empty($poleChoisi)) {
            $poles = $em->getRepository('ParametrageBundle:Pole')->find($poleChoisi);
          //  die(dump('ok'));
            $repartitions = $em->getRepository('BanquemondialeBundle:RepartitionQuittance')->findBrouillardPoleByParameters($datedebut, $datefin, $entreprise, $poleChoisi, $formeJuridique, $idLangue);
            foreach ($repartitions as $repartition) {
                $montant = $repartition['montant'];
                $montantTotal = $montantTotal + $montant;
            }
        }
        else {
            $poles = $em->getRepository('ParametrageBundle:Pole')->getPolesQuittance();

            foreach ($totaux as $total) {
                $montant = $total['montant'];
                $montantTotal = $montantTotal + $montant;
            }
        }
//        foreach ($totaux as $total) {
//            $montant = $total['montant'];
//            $montantTotal = $montantTotal + $montant;
//        }
       // die(dump($montantTotal));
        return array(
            'repartitions' => $repartitions,
            'montantTotal' => $montantTotal,
            'poles' => $poles,
            'poleChoisi' => $poleChoisi,
            'totaux' => $totaux,
            'dateDebut' => $datedebut,
            'dateFin' => $datefin,
            'formeJuridique'=>$formeJuridique,
            'modePaiement'=>$modePaiement,
            'entreprise'=>$entreprise,
            'nomCaisse' => $nomCaisse);
    }

    /**
     * @Route("/{_locale}/kenneh/test-kenneh-update2",name="kennehTestAction_test2")
     */
    public function kennehTest2Action()
    {

        $this->get('monServices')->pingIPServer('192.168.5.44');
        $em = $this->getDoctrine()->getManager();
        $connection = $this->getDoctrine()->getConnection();
        $RAW_QUERYlike = "SELECT r.id FROM DossierDemande r where r.id>=22503";
        $statement2 = $connection->prepare($RAW_QUERYlike);
        $statement2->execute();
        $resultlike = $statement2->fetchAll();
        for ($i = 0; $i < count($resultlike); $i++) {
            $representant = $this->get('monServices')->getRepresentanByDossier($em->getRepository('BanquemondialeBundle:DossierDemande')->find($resultlike[$i]['id']));
            for ($j = 0; $j < count($representant); $j++) {
                $entity = $em->getRepository('BanquemondialeBundle:Representant')->findOneById($representant[$j]->getId());
                if ($j == 0) {
                    $entity->setGp(1);
                    $em->persist($entity);
                    $em->flush();
                }

            }
        }
        var_dump('succes');
        die();
        return $this->render('DefaultBundle:Default:testtkenneh.html.twig', array('dossiers' => $resultlike));

    }

    /**
     * @Route("/{_locale}/admin/make-orange-money-payement",name="make-orange-money-payement")
     */
    public function makeOrangeMoneyPayementAction(Request $request)
    {
        if ($this->get('monServices')->pingIPServer()==true){
            $em = $this->getDoctrine()->getManager();
            $session = $request->getSession();
            $session->remove('sessionOrderId');
            $session->remove('sessionPayToken');
            $session->remove('sessionAmount');
            $sessionData = $session->get('sessionData');
            $sessionIdq = $session->get('sessionIdq');
            $codLang = $session->get('codLang');
            $customer = array_merge(
                array(
                    'denominationSociale' => $sessionData['denominationSociale'],
                    'numeroDossier' => $sessionData['numeroDossier'],
                    'telephone' => $sessionData['telephone'],
                    'idq'=>$sessionIdq,
                    'codeLang'=>$codLang
                ),$sessionData);
//        die(dump($customer));
            $amount=500;
            // $sessionData['montantTotalFacture'];
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $paiementOrange = new PaiementOrange();
            $orderIder = $this->get('monServices')->genrateReferancefactureOrange();
            $omWeb = $this->get('monServices')->webPayement( $customer['denominationSociale'], $amount, $orderIder);
            $paiementOrange
                ->setAmount($amount)
                ->setPayToken($omWeb['webPayment']['pay_token'])
                ->setOrderId($omWeb['transactionStatus']['order_id'])
                ->setStatus($omWeb['transactionStatus']['status'])
                ->setTxnid($omWeb['transactionStatus']['txnid'])
                ->setUser($user)
                ->setCustomer($customer);
            $em->persist($paiementOrange);
            $em->flush();
            if (!$session->has('sessionOrderId')) {
                $session->set('sessionOrderId', $omWeb['transactionStatus']['order_id']);
                $session->set('sessionPayToken', $omWeb['webPayment']['pay_token']);
                $session->set('sessionAmount', $amount);
            }
            return new RedirectResponse($omWeb['webPayment']['payment_url']);
        }
        else{
            $this->get('session')->getFlashBag()->add('echecSMS','Impossible de lancer le paiement par orange money problème lie à la connexion internet');
            return $this->redirectToRoute('confirmation-payement-orange-money');
        }
    }

    /**
     * @Route("/{_locale}/admin/confirmation-payement-orange-money",name="confirmation-payement-orange-money")
     */
    public function confirmationPayementOrangeMoneyAction(Request $request)
    {
      //$em = $this->getDoctrine()->getManager();
        $referer = $this->getRequest()->headers->get('referer');
        return $this->render('DefaultBundle:Default:confirm-om-payement.html.twig', array(
            'referer' => $referer
        ));
    }
    /**
     * @Route("/{_locale}/admin/returning-payement-orange-money",name="returning-payement-orange-money")
     */
    public function returningPayementOrange(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $sessionData = $session->get('sessionData');
        $sessionIdq = $session->get('sessionIdq');
        $codLang = $session->get('codLang');
        $sessionOrderId = $session->get('sessionOrderId');
        $sessionPayToken = $session->get('sessionPayToken');
        $sessionAmount = $session->get('sessionAmount');
        $statusPayement = $this->get('monservices')->getStatusPayement($sessionOrderId, $sessionAmount, $sessionPayToken);
        $paiementOrange=$em->getRepository('DefaultBundle:PaiementOrange')->findOneByOrderId($sessionOrderId);
        $tabStatus = array(
            'INITIATED' => 'INITIATED',
            'PENDING' => 'PENDING',
            'EXPIRED' => 'EXPIRED',
            'SUCCESS' => 'SUCCESS',
            'FAILED' => 'FAILED');
            if ($statusPayement['status'] == $tabStatus['INITIATED']) {
            $errorMessage = "Le Paiement a été annule veuillez réessayer";
            $this->get('session')->getFlashBag()->add('echecStatus', $errorMessage);
            return $this->redirectToRoute('traiter_quittance', array('idq' => $sessionIdq));
        }
        elseif ($statusPayement['status'] == $tabStatus['PENDING']) {
            $errorMessage = "L'utilisateur a cliqué sur « Confirmer », la transaction est en cours du côté d’Orange";
            $this->get('session')->getFlashBag()->add('successStatus', $errorMessage);
           // die(dump($errorMessage));
        }
        elseif ($statusPayement['status'] == $tabStatus['EXPIRED']) {
            $this->get('monservices')->updatePayementOrange($paiementOrange,$tabStatus['EXPIRED'],$statusPayement['txnid']);
            $errorMessage = "Le délai de validation a expiré, veuillez réessayer";
            $this->get('session')->getFlashBag()->add('echecStatus', $errorMessage);
            return $this->redirectToRoute('traiter_quittance', array('idq' => $sessionIdq));
        }
        elseif ($statusPayement['status'] == $tabStatus['SUCCESS']) {
            $this->get('monservices')->returnSuccessPayementOrange($sessionData, $sessionIdq, $codLang);
            $this->get('monservices')->updatePayementOrange($paiementOrange,$tabStatus['SUCCESS'],$statusPayement['txnid']);
            $errorMessage = "le paiement est effectué";
            $this->get('session')->getFlashBag()->add('successStatus', $errorMessage);
            /// Supression  variables de session
            $session->remove('sessionData');
            $session->remove('sessionIdq');
            $session->remove('codLang');

            $session->remove('sessionOrderId');
            $session->remove('sessionPayToken');
            $session->remove('sessionAmount');
            return $this->redirectToRoute('reporting_quittance');
            // // die(dump($errorMessage));
        }
        elseif ($statusPayement['status'] == $tabStatus['FAILED']) {
            $this->get('monservices')->updatePayementOrange($paiementOrange,$tabStatus['FAILED'],$statusPayement['txnid']);
            $errorMessage = "Le paiement a échoué veillez recomencer";
            $this->get('session')->getFlashBag()->add('echecStatus', $errorMessage);
            return $this->redirectToRoute('traiter_quittance', array('idq' => $sessionIdq));
        }
    }

    /**
     * @Route("/{_locale}/admin/test-payement-orange-money",name="test-payement-orange-money")
     */
    public function testPayementOrange(Request $request)
    {
        $session = $request->getSession();
        $sessionData = $session->get('sessionData');
        $sessionIdq = $session->get('sessionIdq');
        $codLang = $session->get('codLang');
       $this->get('monservices')->verifyQuittancePayementStstus('22560');


       $this->get('monservices')->returnSuccessPayementOrange($sessionData,$sessionIdq,$codLang);

//        $host = $this->container->get('router')->getContext()->getHost();
//        $parameter_locale = $this->container->get('router')->getContext()->getParameter('_locale');
//        $scheme = $this->container->get('router')->getContext()->getScheme();
//        $port = $this->container->get('router')->getContext()->getHttpPort();
//
//        $baseUrl = $scheme . '://' . $host . '/' . $parameter_locale . '/';
//
//        die(dump($baseUrl));
//        die(dump($this->get('router')->generate('test-payement-orange-money')));
//        $omWeb = $this->get('monservices')->testAPIwebPayement();
        return new RedirectResponse($omWeb['payment_url']);
    }

    /**
     * @Route("/{_locale}/test-export-excel-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="testExportExcel_excel", methods={"GET","POST"})
     * @Template("DefaultBundle:Default:test-excel.xls.twig")
     */
    public function testExportExcel(Request $request)
    {

        $data=['name'=>'Kante Mohamed','Genre'=>'Masculin','Age'=>27,'Adresse'=>'Lambayi'];
       // die(dump($data));
        return  ['persone' => ['name'=>'Kante Mohamed','Genre'=>'Masculin','Age'=>27,'Adresse'=>'Lambayi']];
    }

}
