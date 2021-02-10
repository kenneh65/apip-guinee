<?php
/**
 * Created by PhpStorm.
 * User: Mohamed Kante
 * Date: 19/05/2018
 * Time: 01:05
 */

namespace DefaultBundle\services;
use Foris\OmSdk\OmSdk;
use GuzzleHttp\Client;
use Osms\Osms;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class payCardService extends Controller
{
    public function checkPayCarStatus(Request $request,$transactionReference){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://paycard.co/epay/verify/?c=NDMxMjI5Njk&ref=".$transactionReference,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array("Content-Type: application/json; charset=utf-8"),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }
//    public function requestPayement($cardnumber,$paymentAmount){
//        $curl = curl_init();
//        $CURLOPT_POSTFIELDS=[
//            "cardnumber"=>$cardnumber,//"803772960",
//            "paymentAmount"=>$paymentAmount//"1000"
//        ];
//        $data=json_encode($CURLOPT_POSTFIELDS);
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => "https://paycard.co/rest/partner/278949/payment/request",
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => "",
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => "POST",
//            CURLOPT_POSTFIELDS =>$data,
//            CURLOPT_HTTPHEADER => array(
//                "Content-Type: application/json; charset=utf-8",
//                "Authorization: Basic 2222b745-36bf-4f1a-8f4d-19f6f4ed9b53"
//            ),
//        ));
//        $response = curl_exec($curl);
//        curl_close($curl);
//        return json_decode($response);
//    }
//    public function takePayement($cardnumber,$paymentAmount,$onetimeRequestId,$oneTimePin){
//        $curl = curl_init();
//        $CURLOPT_POSTFIELDS=[
//            "cardnumber"=>$cardnumber,//"803772960",
//            "paymentAmount"=>$paymentAmount,//"1000",
//            "oneTimePin"=>$oneTimePin,//"",
//            "onetimeRequestId"=>$onetimeRequestId,//""
//        ];
//        $data=json_encode($CURLOPT_POSTFIELDS);
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => "https://paycard.co/rest/partner/278949/payment/",
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => "",
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => "POST",
//            CURLOPT_POSTFIELDS =>$data,
//            CURLOPT_HTTPHEADER => array(
//                "Content-Type: application/json; charset=utf-8",
//                "Authorization: Basic 2222b745-36bf-4f1a-8f4d-19f6f4ed9b53"
//            ),
//        ));
//        $response = curl_exec($curl);
//        curl_close($curl);
//        return json_decode($response);
//    }
//    public function makePayment($codeOTP,$cardnumber){
//        $requestPay=$this->requestPayement($cardnumber,$paymentAmount);
//        $takePayement=$this->takePayement($requestPay['card_number'],$requestPay['payment_amount'],$requestPay['onetime_request_id'],$codeOTP);
//        return $takePayement;
//    }
//    public  function setPayementSession(Request $request,$data){
//        $session = $request->getSession();
//        /// Supression  variables de session
//        $session->remove('requestPayement');
//        /// Creation  variables de session
//        if (!$session->has('requestPayement')) {
//            $session->set('requestPayement', $data);
//        }
//        return $session;
//    }
//    public function getRequeStstatus($requestID){
//
//        $curl = curl_init();
//        $CURLOPT_POSTFIELDS=[
//            "requestID"=>$requestID];
//        $data=json_encode($CURLOPT_POSTFIELDS);
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => "https://paycard.co/rest/partner/278949/request_status/",
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => "",
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => "POST",
//            CURLOPT_POSTFIELDS => $data,
//            CURLOPT_HTTPHEADER => array(
//                "Content-Type: application/json; charset=utf-8",
//                "Authorization: Basic 2222b745-36bf-4f1a-8f4d-19f6f4ed9b53"
//            ),
//        ));
//        $response = curl_exec($curl);
//        curl_close($curl);
//        return json_decode($response);
//    }
public function relogin(){
    $cred_data = array('_username' => 'aminataidy', '_password' => '123', '_target_path' => 'http://apip-guinee.init/fr/admin/reportingQuittance');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://apip-guinee.init/fr/utilisateurs/login_check');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($cred_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    $result = curl_exec($ch);
   curl_close($ch);
}
}
