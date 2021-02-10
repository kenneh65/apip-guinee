<?php

namespace DefaultBundle\Controller;

use DefaultBundle\Entity\PaiementOrange;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
class PayCardControllerController extends Controller
{
    /**
     * @Route("/{_locale}/makePaiementPayCard",name="makePaiement",methods={"POST","GET"})
     * @Security("has_role('ROLE_USER')")
     */
    public function makePaiementAction(Request $request)
    {
        $heder=$request->getMethod();
        $baseUrl=$this->get('monservices')->getBaseUrl();
        $secondaireUrl=$this->generateUrl("returning-after-paycard-paiement");
        if ($this->get('monServices')->pingIPServer() == true) {
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
                    'idq' => $sessionIdq,
                    'codeLang' => $codLang
                ), $sessionData);
            $pourcentage = 0.02;
            $amount = $sessionData['montantTotalFacture'] * $pourcentage + $sessionData['montantTotalFacture'];
            $initietedDate= date_format(new \DateTime(),'Y-m-d H:i:s');
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $paiementOrange = new PaiementOrange();
            $orderIder = $this->get('monServices')->genrateReferancefactureOrange();
            $paiementOrange
                ->setAmount($amount)
                ->setPayToken($orderIder)
                ->setOrderId($orderIder)
                ->setStatus("INIT")
                ->setTxnid(null)
                ->setUser($user)
                ->setNumeroDossier($sessionIdq)
                ->setCustomer($customer)
                ->setInitiateDate(new \DateTime($initietedDate));
            $em->persist($paiementOrange);
            if (!$session->has('sessionOrderId')) {
                $session->set('sessionOrderId', $orderIder);
                $session->set('sessionPayToken',$orderIder);
                $session->set('sessionAmount', $amount);
                $em->flush();
            }
        }
        else {
            $this->get('session')->getFlashBag()->add('echecSMS', 'Impossible de lancer le paiement par PayCard problème lie à la connexion internet');
            return $this->redirectToRoute('makePaiement');
        }
        $returnUrl = $baseUrl.$secondaireUrl;
        return $this->render('DefaultBundle:PayCardController:make_paiement.html.twig',[
            'amount'=>$amount,
            'description'=>"APIP PAYCARD",
            'orderId'=>$orderIder,
            'PayementId'=>$paiementOrange->getId(),
            'initietedDate'=>$initietedDate,
            'returnUrl'=>$returnUrl
        ]);
    }

    /**
     * @Route("/{_locale}/returning-after-paycard-paiement",name="returning-after-paycard-paiement")
     * @Security("has_role('ROLE_USER')")
     */
    public function returningAction(Request  $request)
    {
        $transactionReference=$request->get('transactionReference');
       // $statusPayement = $this->get('paycardservice')->checkPayCarStatus($request,$transactionReference);
        $this->validatePayCardPayement($request);
        return $this->redirectToRoute('reporting_quittance');
    }

    public function validatePayCardPayement(Request $request){
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $sessionData = $session->get('sessionData');
        $sessionIdq = $session->get('sessionIdq');
        $codLang = $session->get('codLang');
        // die(dump($sessionData));
        $sessionOrderId = $session->get('sessionOrderId');
        $sessionPayToken = $session->get('sessionPayToken');
        $sessionAmount = $session->get('sessionAmount');
        $transactionReference=$request->get('transactionReference');
        $statusPayement = $this->get('paycardservice')->checkPayCarStatus($request,$transactionReference);
        $paiementOrange = $em->getRepository('DefaultBundle:PaiementOrange')->findOneByOrderId($sessionOrderId);
        $tabStatus = array(
            'SUCCESS' =>0);
        if ($statusPayement->code== $tabStatus['SUCCESS']) {
            $this->get('monservices')->returnSuccessPayementOrange($sessionData, $sessionIdq, $codLang);
            $paiementOrange->setStatus('SUCCESS');
            $paiementOrange->setEndDate(new \Datetime());
            $em->persist($paiementOrange);
            $em->flush();
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
        }
        else{
            return $this->redirectToRoute('traiter_quittance', array('idq' => $sessionIdq));
        }

    }

}
