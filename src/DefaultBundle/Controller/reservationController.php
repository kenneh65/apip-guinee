<?php

namespace DefaultBundle\Controller;

use DefaultBundle\Entity\detailReservation;
use DefaultBundle\Entity\PaiementOrange;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use DefaultBundle\Entity\reservation;
use DefaultBundle\Form\reservationType;
use BanquemondialeBundle\Form\DossierDemandeSearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * reservation controller.
 *
 * @Route("/{_locale}/reservation")
 */
class reservationController extends Controller
{
    /**
     * @Route("/reservation-encour-expiration", name="reservation_encour_expiration", methods={"GET","POST"})
     */
    public function reservationEncourExpiration(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $reservationRepository = $em->getRepository('DefaultBundle\Entity\reservation');
        $postFormule = $request->get('formule');
        $postAbonnement = $request->get('abonnementId');
        $postnbrMoist = $request->get('nbrMois');
        $postdebutAbonnement = $request->get('debutAbonnement');
        if ($request->getMethod() == 'POST') {
            $entityFormule = $em->getRepository('ecoleBundle:Formule')->findOneById($postFormule);
            $entityAbonnement = $em->getRepository('ecoleBundle:Abonnement')->findOneById($postAbonnement);


            $this->get('monServices')->updateStututAbonnement($entityAbonnement, $em);
            $this->get('monServices')->makeReAbonnement($em, $entityFormule, $entityAbonnement, $postnbrMoist, (new \DateTime($postdebutAbonnement)));
            $this->get('monServices')->updateAbonnement($entityAbonnement, $em, (new \DateTime($postdebutAbonnement)),
                (new \DateTime($this->get('monServices')->getDateFinDabonnement($postnbrMoist,
                    date_format((new \DateTime($postdebutAbonnement)), 'Y-m-d H:i:s'))))
            );
            return $this->redirectToRoute('abonnement_index_encour_expiration');
        }

        $formule = $em->getRepository('ecoleBundle:Formule')->findBy([], ['name' => 'asc']);
        return $this->render('abonnement/abonnementEncourExpiration.html.twig', [
            'abonnements' => $reservationRepository->getAbonnementEnExpiration(),
            'formules' => $formule,
            'clients' => $em->getRepository('ecoleBundle:Client')->findAll(),
            'numbreMois' => $this->get('monServices')->numbreMois(24)
        ]);
    }
    /**
     * @Route("/returning-reservation-payement-orange-money",name="returning-reservation-payement-orange-money")
     */
    public function reservationReturningPayementOrange(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $sessionData = $session->get('sessionData');
        $sessionOrderId = $session->get('sessionOrderId');
        $sessionPayToken = $session->get('sessionPayToken');
        $sessionAmount = $session->get('sessionAmount');
        $statusPayement = $this->get('monservices')->getStatusPayement($sessionOrderId, $sessionAmount, $sessionPayToken);
        $paiementOrange = $em->getRepository('DefaultBundle:PaiementOrange')->findOneByOrderId($sessionOrderId);
        $tabStatus = array(
            'INITIATED' => 'INITIATED',
            'PENDING' => 'PENDING',
            'EXPIRED' => 'EXPIRED',
            'SUCCESS' => 'SUCCESS',
            'FAILED' => 'FAILED');
        if ($statusPayement['status'] == $tabStatus['INITIATED']) {
            $errorMessage = "Le Paiement a été annule veuillez réessayer";
            $this->get('session')->getFlashBag()->add('echecStatus', $errorMessage);
            $this->get('monservices')->updateReservationPayementOrange($paiementOrange, $statusPayement['status'], $statusPayement['txnid'], 'delete');
            return $this->redirectToRoute('accueil');
        } elseif ($statusPayement['status'] == $tabStatus['PENDING']) {
            $errorMessage = "L'utilisateur a cliqué sur « Confirmer », la transaction est en cours du côté d’Orange";
            $this->get('session')->getFlashBag()->add('successStatus', $errorMessage);
            return $this->redirectToRoute('accueil');
        } elseif ($statusPayement['status'] == $tabStatus['EXPIRED']) {
            $this->get('monservices')->updateReservationPayementOrange($paiementOrange, $statusPayement['status'], $statusPayement['txnid'], 'delete');
            $errorMessage = "Le délai de validation a expiré, veuillez réessayer";
            $this->get('session')->getFlashBag()->add('echecStatus', $errorMessage);
            return $this->redirectToRoute('accueil');
        } elseif ($statusPayement['status'] == $tabStatus['SUCCESS']) {
            $this->get('monservices')->updateReservationPayementOrange($paiementOrange, $statusPayement['status'], $statusPayement['txnid'], 'update');
            $errorMessage = "le paiement est effectué";
            $this->get('session')->getFlashBag()->add('successStatus', $errorMessage);
            /// Supression  variables de session
            $session->remove('sessionData');
            $session->remove('sessionReservation');
            $session->remove('sessionIdq');
            $session->remove('codLang');
            $session->remove('sessionOrderId');
            $session->remove('sessionPayToken');
            $session->remove('sessionAmount');
            return $this->redirectToRoute('accueil');
        } elseif ($statusPayement['status'] == $tabStatus['FAILED']) {
            $this->get('monservices')->updateReservationPayementOrange($paiementOrange, $statusPayement['status'], $statusPayement['txnid'], 'delete');
            $errorMessage = "Le paiement a échoué veillez recomencer";
            $this->get('session')->getFlashBag()->add('echecStatus', $errorMessage);
            return $this->redirectToRoute('accueil');
        }

    }
    /**
     * @Route("/make-orange-money-payement-for-reservation",name="make-orange-money-payement-for-reservation")
     */
    public function makeOrangeMoneyPayementAction(Request $request)
    {
        $operation='reservation';
      //  die(dump($this->get('monServices')->pingIPServer()));
        if ($this->get('monServices')->pingIPServer() == true) {

            $em = $this->getDoctrine()->getManager();
            $session = $request->getSession();
            $session->remove('sessionOrderId');
            $session->remove('sessionPayToken');
            $session->remove('sessionAmount');

            $sessionData = $session->get('sessionReservation');
            $sessionPeriodeReservation = $session->get('sessionPeriodeReservation');
            $periodeReservation = $sessionPeriodeReservation->getNombre();
            $dateFin = $this->get('monservices')->getDateFinDabonnement($periodeReservation, date_format(new \DateTime(), 'Y-m-d H:i:s'));
            $customer = $sessionData;
            $verif = $this->get('monservices')->isElementExisteInEntity('DefaultBundle:reservation', ['nomCommercial'=>$customer->getNomCommercial(),'statut'=>true ]);
            // To do approfondire la verification ////////////
            if ($verif == true) {
                $this->get('session')->getFlashBag()->add('error', 'Vous ne pouvez pas réserver ce nom commercial');
                return $this->redirectToRoute('default_reservation_reservation');
            }
            $formJuridique = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneById($sessionData->getFormeJuridique()->getId());
            $amount =$sessionPeriodeReservation->getAmount();
            $paiementOrange = new PaiementOrange();
            $orderIder = $this->get('monServices')->genrateReferancefactureOrange();
            $omWeb = $this->get('monServices')->webPayement($customer->getNom() . ' ' . $customer->getPrenom(), $amount, $orderIder, 'returning-reservation-payement-orange-money');
            if (isset($omWeb['webPayment']['pay_token'])) {
                $paiementOrange
                    ->setAmount($amount)
                    ->setPayToken($omWeb['webPayment']['pay_token'])
                    ->setOrderId($omWeb['transactionStatus']['order_id'])
                    ->setStatus($omWeb['transactionStatus']['status'])
                    ->setTxnid($omWeb['transactionStatus']['txnid'])
                    ->setCustomer(array($customer))
                    ->setOperation('reservation');
                $reservation = new reservation();
                $reservation
                    ->setModePaiement($sessionData->getModePaiement())
                    ->setEmail($sessionData->getEmail())
                    ->setAdresse($sessionData->getAdresse())
                    ->setDateCreation($sessionData->getDateCreation())
                    ->setDateDebut($sessionData->getDateCreation())
                    ->setDateFin(new \DateTime($dateFin))
                    ->setNomCommercial($sessionData->getNomCommercial())
                    ->setNom($sessionData->getNom())
                    ->setPrenom($sessionData->getPrenom())
                    ->setTelephone($sessionData->getTelephone())
                    ->setFormeJuridique($formJuridique);
                $em->persist($reservation);
                $detailReservation = new detailReservation();
                $detailReservation
                    ->setOperation(array_merge(array($reservation), array($paiementOrange), array($sessionPeriodeReservation)))
                    ->setReservation($reservation)
                    ->setDateDebut(new \DateTime())
                    ->setDateFin(new \DateTime($dateFin))
                    ->setIsPaid(false)
                    ->setStatut(true);
                $em->persist($detailReservation);
                ////persite paiement orange////////
                $paiementOrange->setDetailReservation($detailReservation);
                $em->persist($paiementOrange);
                $em->flush();
                //  die(dump($reservation));
                if (!$session->has('sessionOrderId')) {
                    $session->set('sessionOrderId', $omWeb['transactionStatus']['order_id']);
                    $session->set('sessionPayToken', $omWeb['webPayment']['pay_token']);
                    $session->set('sessionAmount', $amount);

                    // $reservation=$em->getRepository('DefaultBundle\Entity\reservation')->findOneById(83);
                    $detailReservation = $em->getRepository('DefaultBundle\Entity\detailReservation')->findOneBy(['reservation' => $reservation, 'statut' => 1], ['dateFin' => 'desc']);
                    $this->sendReservationEmail($reservation->getEmail(), $detailReservation, 'reservation');
                    // die(dump($reservation));
                    return new RedirectResponse($omWeb['webPayment']['payment_url']);
                }
            }
            else{
                $errr='Error: '. $errr=($this->get('monservices')->fixtags( $this->get('monServices')->get_string_between(json_encode($omWeb['webPayment']),'description','\n')));
                $this->get('session')->getFlashBag()->add('echecSMS',$errr );
                return $this->redirectToRoute('confirmation-payement-orange-money-for-reservation',['operation'=>$operation]);
            }
        }
        else {

            $this->get('session')->getFlashBag()->add('echecSMS', 'Impossible de lancer le paiement par orange money problème lie à la connexion internet');
            return $this->redirectToRoute('confirmation-payement-orange-money-for-reservation',['operation'=>$operation]);
        }
    }
    /**
     * @Route("/make-orange-money-payement-for-renouvelement-reservation",name="make-orange-money-payement-for-renouvelement-reservation")
     */
    public function makeOrangeMoneyrenouvelementPayementAction(Request $request)
    {
        if ($this->get('monServices')->pingIPServer() == true) {
            $em = $this->getDoctrine()->getManager();
            $session = $request->getSession();
            $session->remove('sessionOrderId');
            $session->remove('sessionPayToken');
            $session->remove('sessionAmount');
            $sessionData = $session->get('sessionReservation');

            $sessionPeriodeReservation = $session->get('sessionPeriodeReservation');
            $periodeReservation = $sessionPeriodeReservation->getNombre();
            $dateFin = $this->get('monservices')->getDateFinDabonnement($periodeReservation, date_format(new \DateTime(), 'Y-m-d H:i:s'));
            $customer = $sessionData;
            $verif = $this->get('monservices')->isElementExisteInEntity('DefaultBundle:reservation', ['nomCommercial'=>$customer->getNomCommercial(),'statut'=>true]);
            // To do approfondire la verification ////////////
            if ($verif == true) {
                $formJuridique = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneById($sessionData->getFormeJuridique()->getId());
                $entityReservation = $em->getRepository('DefaultBundle\Entity\reservation')->findOneByNomCommercial($sessionData->getNomCommercial());
               // die(dump($entityReservation));
                $this->get('monServices')->updateStututAbonnement($entityReservation, $em);
                $this->get('monServices')->makeReAbonnement($em, $entityReservation, $formJuridique,array($entityReservation), $periodeReservation, (new \DateTime()));
                $this->get('monServices')->updateAbonnement($entityReservation, $em,(new \DateTime()),new \DateTime($dateFin));

                $amount =$sessionPeriodeReservation->getAmount();
                $orderIder = $this->get('monServices')->genrateReferancefactureOrange();
                $omWeb = $this->get('monServices')->webPayement($customer->getNom() . ' ' . $customer->getPrenom(), $amount, $orderIder, 'returning-reservation-payement-orange-money');
               /// die(dump($omWeb));
                if (!$session->has('sessionOrderId')) {
                    $session->set('sessionOrderId', $omWeb['transactionStatus']['order_id']);
                    $session->set('sessionPayToken', $omWeb['webPayment']['pay_token']);
                    $session->set('sessionAmount', $amount);
                }
                return new RedirectResponse($omWeb['webPayment']['payment_url']);
            }
        }
        else {
            $this->get('session')->getFlashBag()->add('echecSMS', 'Impossible de lancer le paiement par orange money problème lie à la connexion internet');
            return $this->redirectToRoute('confirmation-payement-orange-money');
        }
    }
    /**
     * @Route("/verification-disponibilite-nom-commercial",name="default_reservation_reservation")
     */
    public function reservationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        $reservation=[];
        $secondVerification = null;
        $thirdVerification=null;
        $message='';
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        if ($request->getMethod() == 'POST') {
            $reservation=$em->getRepository('DefaultBundle:reservation')->findOneBy(['statut'=>true,'nomCommercial'=>$request->request->get("search")],[]);
            $firstVefication = $this->get('monservices')->verificationNomCommercial($request);
            $thirdVerification = $this->get('monservices')->verificationNomCommercialDossierDemande($request);
            $fouthVerification = $this->get('monservices')->verificationNomCommercialArchiveNomCommerciale($request);

            if ($firstVefication == true ||$thirdVerification==true||$fouthVerification==true) {
                $secondVerification = true;
                $message='Désolé ce nom commercial est déjà  en utilisation';
                $this->get('session')->getFlashBag()->add('error', $message);
            }
            else {

                $secondVerification = $this->get('monservices')->verificationNomCommercialReservation($request);
                if ($secondVerification) {
                    $message='Désolé ce nom commercial est déjà  réserve';
                    $this->get('session')->getFlashBag()->add('error', $message);
                } else {
                    $message='Ce nom commercial est libre vous pouvez le réserver';
                    $this->get('session')->getFlashBag()->add('success',$message);
                }
            }

        }
      //  die(dump($reservation));
        return $this->render('DefaultBundle:Reservation:reservation.html.twig', [
            'langues' => $lgs,
            'resulta' => $secondVerification ?: null,
            'nomCommercial' => $request->request->get("search"),
            'reservation'=>$reservation,
            'message'=>$message

        ]);
    }
    /**
     * @Route("/confirmation-payement-orange-money-pour-reservation-nom-comerciale/{operation}",name="confirmation-payement-orange-money-for-reservation")
     */
    public function confirmationPayementOrangeMoneyAction(Request $request,$operation)
    {

        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        $resulta = null;
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        return $this->render('DefaultBundle:Reservation:confirm-om-payement.html.twig', array('langues' => $lgs,'operation'=>$operation));
    }
    /**
     *
     * @Route("/getPeriode-reservation-by-form-juridique", name="get_PeriodeBy_formJuridique")
     * @Method({"GET", "POST"})
     */
    public function getPeriodeByformJuridique(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $formJ = $request->get('formeJuridiqueTraduction');
            $em = $this->getDoctrine()->getManager();
            $PeriodeReservation = $em->createQueryBuilder()
                ->select('p.id,p.libelle,p.amount,fj.libelle formeJuridiqueTraduction,p.nombre')
                ->from('DefaultBundle:PeriodeReservation', 'p')
                ->innerjoin('BanquemondialeBundle:FormeJuridiqueTraduction', 'fj', 'WITH', 'p.formeJuridiqueTraduction=fj.id')
                ->where('p.formeJuridiqueTraduction=' . $formJ)
                ->orderby('p.nombre', 'asc')
                ->getQuery()
                ->getResult();
            return new JsonResponse(array('PeriodeReservation' => $PeriodeReservation, 'formJ' => $formJ));
        }
    }
    /**
     * Lists all reservation entities.
     *
     * @Route("/", name="reservation_index")
     *@Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(Request  $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tab = $this->get('monServices')->listeReservationEnExpiration();
        $newTab = [];
        for ($i = 0; $i < count($tab); $i++) {
            $newTab[$tab[$i]['idReservation']] = [$tab[$i]['dateFin']];
        }
        $reservations = $em->getRepository('DefaultBundle:reservation')->findReservationAll();
        $searchFilter=$request->get('searchFilter');
//die(dump($reservations));
        if ($request->getMethod() == 'POST')    {
            if ($searchFilter=='encours'){
                $reservations = $this->get('monservices')->listeReservationEnExpiration();
               // die(dump($reservations));
            }
            elseif ($searchFilter=='expirer'){
                $reservations = $this->get('monservices')->listeReservationExpirer();
            }
            else{
                $reservations = $em->getRepository('DefaultBundle:reservation')->findReservationAll();
            }
        }
        return $this->render('DefaultBundle:Reservation:index.html.twig', array(
            'reservations' => $reservations,
            'reservationsEnExpiration' => $newTab,
            'searchFilter'=>$searchFilter
        ));
    }
    /**
     * Creates a new reservation entity.
     *
     * @Route("/new", name="reservation_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        $resulta = null;
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $nomCommercial = $request->request->get('nomCommercial');
        $reservation = new reservation();
        $form = $this->createForm('DefaultBundle\Form\reservationType', $reservation);
        $form->add('nomCommercial', TextType::class, array(
            'data' => $nomCommercial
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $validationCode=$this->get('monservices')->generateCustomToken(111111,9999999);
            if (empty($form->get('PeriodeReservation')->getData()->getAmount())){
                $detailReservation = new detailReservation();
                $date = strtotime("+7 day", strtotime(date_format(new \DateTime(), 'Y-m-d H:i:s')));
                $dateFin = date("Y-m-d H:i:s", $date);
                $reservation->setDateDebut(new \DateTime())->setDateFin(new \DateTime($dateFin));
               // $reservation->setValidationCode($validationCode);
                $em->persist($reservation);
//                die(dump($dateFin));
                $detailReservation
                    ->setOperation(array_merge(array($reservation), array($form->get('modePaiement')->getData()), array($form->get('PeriodeReservation')->getData())))
                  //  ->setOperation(array($reservation))
                    ->setPeriodeReservation($form->get('PeriodeReservation')->getData())
                    ->setReservation($reservation)
                    ->setDateDebut(new \DateTime())
                    ->setDateFin(new \DateTime($dateFin))
                    ->setIsPaid(true)
                    ->setStatut(true);
                $em->persist($detailReservation);
                $em->flush();
              //  die(dump($detailReservation));
              //  $detailReservation=$em->getRepository('DefaultBundle\Entity\detailReservation')->findOneBy(['reservation'=>$reservation,'statut'=>1],['dateFin'=>'desc']);
                $this->sendReservationEmail($reservation->getEmail(),$detailReservation,'reservation');
                $this->get('session')->getFlashBag()->add('successStatus', 'Réservation effectuée');
                return $this->redirectToRoute('accueil');
            }
            else{
                $data = $form->getData();
                $session = $request->getSession();
                /// Supression  variables de session
                $session->remove('sessionReservation');
                /// Creation  variables de session
                if (!$session->has('sessionReservation')) {
                    $session->set('sessionReservation', $data);
                    $session->set('sessionPeriodeReservation', $form->get('PeriodeReservation')->getData());
                }
                if ($data->getModePaiement() == 0) {
                    $operation='reservation';
                    return $this->redirectToRoute('confirmation-payement-orange-money-for-reservation',['operation'=>$operation]);
                }
                else {
                    return $this->redirectToRoute('reservation_show', array('id' => $reservation->getId()));
                }
            }
        }
        return $this->render('DefaultBundle:Reservation:new.html.twig',array(
            'reservation' => $reservation,
            'form' => $form->createView(),
            'langues' => $lgs,
            'nomCommercial' => $nomCommercial
        ));
    }
    /**
     * Creates a new reservation entity.
     *
     * @Route("/{id}/renouvelement-reservation/", name="renouvelement-reservation_new")
     * @Method({"GET", "POST"})
     */
    public function renouvelementReservationAction(Request $request,reservation $reservation)
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        $resulta = null;
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
     //   $nomCommercial = $request->request->get('nomCommercial');
       // $reservation = $em->getRepository('DefaultBundle:reservation')->findOneBy(['nomCommercial'=>$nomCommercial]);
        $form = $this->createForm('DefaultBundle\Form\reservationType', $reservation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (empty($form->get('PeriodeReservation')->getData()->getAmount())){
                $detailReservation = new detailReservation();
                $date = strtotime("+7 day", strtotime(date_format(new \DateTime(), 'Y-m-d H:i:s')));
                $dateFin = date("Y-m-d H:i:s", $date);
                $reservation->setDateDebut(new \DateTime())->setDateFin(new \DateTime($dateFin));
                // die(dump($entityReservation));
                $this->get('monServices')->updateStututAbonnement($reservation, $em);
                $this->get('monServices')->updateAbonnement($reservation, $em,(new \DateTime()),new \DateTime($dateFin));
                $em->persist($reservation);
                $detailReservation
                    ->setOperation(array($reservation))
                    ->setReservation($reservation)
                    ->setPeriodeReservation($form->get('PeriodeReservation')->getData())
                    ->setDateDebut(new \DateTime())
                    ->setDateFin(new \DateTime($dateFin))
                    ->setIsPaid(true)
                    ->setStatut(true);
                $em->persist($detailReservation);
                $em->flush();
               // die(dump($detailReservation->getPeriodeReservation()));
                $this->sendReservationEmail($reservation->getEmail(),$detailReservation,'reservation');
                $this->get('session')->getFlashBag()->add('successStatus', 'Réservation renouvelé avec succès ');
                return $this->redirectToRoute('accueil');

            }
            else{
                $data = $form->getData();
                $session = $request->getSession();
                /// Supression  variables de session
                $session->remove('sessionReservation');
                /// Creation  variables de session
                if (!$session->has('sessionReservation')) {
                    $session->set('sessionReservation', $data);
                    $session->set('sessionPeriodeReservation', $form->get('PeriodeReservation')->getData());
                }
                if ($data->getModePaiement() == 0) {
                    $operation='renouvelement';
                    return $this->redirectToRoute('confirmation-payement-orange-money-for-reservation',['operation'=>$operation]);
                }
                else {
                    return $this->redirectToRoute('reservation_show', array('id' => $reservation->getId()));
                }
            }

        }
       // die(dump($code));
        return $this->render('DefaultBundle:Reservation:edit.html.twig',array(
            'reservation' => $reservation,
            'form' => $form->createView(),
            'langues' => $lgs,
            'nomCommercial' => $reservation->getNomCommercial(),
            'formJuridiqueId'=>$reservation->getFormeJuridique()->getId(),

        ));
    }
    /**
     * Finds and displays a reservation entity.
     *
     * @Route("/{id}", name="reservation_show")
     * @Method("GET")
     */
    public function showAction(reservation $reservation)
    {
        $em=$this->getDoctrine()->getManager();
       // $reservation=$em->getRepository('DefaultBundle\Entity\reservation')->findOneById(83);
       // $this->sendReservationEmail($reservation->getEmail(),$reservation,'reservation');die(dump($reservation));
        $deleteForm = $this->createDeleteForm($reservation);
      // $this->sendReservationEmail($reservation->getEmail(),$reservation,'reservation');
//        {% if monServices.secondsToTime((monServices.reservationsEncour(reservation.idReservation)[0]['dateFin'])|date('Y-m-d H:i:s'))['Ans']>0 %}
//        {{ monServices.secondsToTime((monServices.reservationsEncour(reservation.idReservation)[0]['dateFin'])|date('Y-m-d H:i:s'))['Ans']~' Ans' }}
//        {% endif %}
     //  $secondsToTime= $this->get('monservices')->secondsToTime(date_format($reservation->getDateFin(),'Y-m-d H:i:s'));
 //$detailReservation=$em->getRepository('DefaultBundle\Entity\detailReservation')->findOneBy(['reservation'=>$reservation,'statut'=>1],['dateFin'=>'desc']);
     //  die(dump($detailReservation->getOperation()[2]->getLibelle()));
        return $this->render('DefaultBundle:Reservation:show.html.twig', array(
            'reservation' => $reservation,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing reservation entity.
     *
     * @Route("/{id}/edit", name="reservation_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, reservation $reservation)
    {
        $deleteForm = $this->createDeleteForm($reservation);
        $editForm = $this->createForm('DefaultBundle\Form\reservationType', $reservation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reservation);
            $em->flush();

            return $this->redirectToRoute('reservation_edit', array('id' => $reservation->getId()));
        }

        return $this->render('DefaultBundle:Reservation:edit.html.twig', array(
            'reservation' => $reservation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a reservation entity.
     *
     * @Route("/{id}", name="reservation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, reservation $reservation)
    {
        $form = $this->createDeleteForm($reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reservation);
            $em->flush();
        }

        return $this->redirectToRoute('reservation_index');
    }
    /**
     * Creates a form to delete a reservation entity.
     *
     * @param reservation $reservation The reservation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(reservation $reservation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reservation_delete', array('id' => $reservation->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
    /**
     * @Route("/send-alert-messages/",name="send-alert-messages")
     */
    public function sendAlertMessagesAction(Request $request)
    {
        $tabReservationId=$request->request->get('choixReservation');
        $em=$this->getDoctrine()->getManager();
        if (count($tabReservationId)>0){
            foreach ($tabReservationId as $re){
                $reservation=$em->getRepository('DefaultBundle\Entity\reservation')->findOneById($re);
                $detailReservation=$em->getRepository('DefaultBundle\Entity\detailReservation')->findOneBy(['reservation'=>$reservation,'statut'=>1],['dateFin'=>'desc']);
                $this->sendReservationEmail($reservation->getEmail(),$detailReservation,'renouvelement');
            }
            $this->get('session')->getFlashBag()->add('successStatus', ' Mail envoyé avec succès');
            return $this->redirectToRoute('reservation_index');
        }
    }
    /**
     * @Route("/remove-reservations/",name="remove-reservations")
     */
    public function removeReservationsAction(Request $request)
    {
        $tabReservationId=$request->request->get('choixReservation');
         $em=$this->getDoctrine()->getManager();
        if (count($tabReservationId)>0){
            foreach ($tabReservationId as $re){
                $reservation=$em->getRepository('DefaultBundle\Entity\reservation')->findOneById($re);
                $detailReservation=$reservation->getDetailReservation();
                $paiement=null;
                foreach ($detailReservation as $det){
                 $this->get('monservices')->updateStatus('DefaultBundle\Entity\detailReservation',$det->getId());
                }
                $this->get('monservices')->updateStatus('DefaultBundle\Entity\reservation',$reservation->getId());
            }
            $this->get('session')->getFlashBag()->add('successStatus', $this->get('monservices')->messageSucces());
            return $this->redirectToRoute('reservation_index');
        }
    }
    function deleteElementById($entity,$id){
        $em=$this->getDoctrine()->getManager();
        $element=$em->getRepository($entity)->findOneById($id);
        $em->remove($element);
        $em->flush();
    }
    function sendReservationEmail($email,$entyti,$operation='reservation')
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $messagerie = $em->getRepository('ParametrageBundle:Messagerie')->find(1);
            $logo = $this->get('monservices')->imageToBas64('img/apip.png');
            $transport = \Swift_SmtpTransport::newInstance($messagerie->getMailerHost(), $messagerie->getMailerPort(), 'ssl')
                ->setUsername($messagerie->getMailerUser())
                ->setPassword($messagerie->getMailerPassword())->setEncryption($messagerie->getEncryption());
            $mailer = \Swift_Mailer::newInstance($transport);
            $message = \Swift_Message::newInstance($transport);
            $message->setSubject('APIP-GUINEE')
                ->setFrom($messagerie->getExpediteurEmail())
                ->setTo($email)
                ->setCharset('utf-8')//Le Charset
                ->setContentType('text/html')//Le type du contenu
                ->setBody(
                    $this->renderView(
                        'DefaultBundle:Default:sendReservationEmail.html.twig', ['reservation' => $entyti, 'logo' => $logo,'operation'=>$operation]
                    ), 'text/html'
                );
                if ($this->get('monservices')->check_internet_connection() == true) {
                $mailer->send($message);
//                $this->get('session')->getFlashBag()->add('successMail', ' Mail envoyé avec succès au promoteur');
            } else {
                $this->get('session')->getFlashBag()->add('echecMail', 'Un probleme lie à la conexion internet vous empeche de generer votre code de confirmation ');
            }

        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('echecMail', 'Un probleme lie au server de messagerie');
        }
    }
}
