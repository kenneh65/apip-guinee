<?php
/**
 * Created by PhpStorm.
 * User: Mohamed Kante
 * Date: 19/05/2018
 * Time: 01:05
 */

namespace DefaultBundle\services;

use BanquemondialeBundle\Entity\Representant;
use Dompdf\Dompdf;
use Dompdf\Options;
use Osms\Osms;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class monServices extends Controller
{


    public function messageSucces()
    {
        $message = "Opération effectuée avec succès.";
        return $message;
    }

    public function getRepresentanByDossier($dossier)
    {
        $em = $this->getDoctrine()->getManager();
        $representant = $em->getRepository('BanquemondialeBundle:Representant')->findByDossierDemande($dossier);
        return $representant;
    }

    public function EnvoiMessage(Representant $representant, $tabEmail = 'kenneh65@gmail.com', $typeMessage = 'depot')
    {
        $em = $this->getDoctrine()->getManager();
        $messagerie = $em->getRepository('ParametrageBundle:Messagerie')->find(1);
        $logo = $this->imageToBas64('img/apip.png');
        $transport = \Swift_SmtpTransport::newInstance($messagerie->getMailerHost(), $messagerie->getMailerPort(), 'ssl')
            ->setUsername($messagerie->getMailerUser())
            ->setPassword($messagerie->getMailerPassword())->setEncryption($messagerie->getEncryption());
        $mailer = \Swift_Mailer::newInstance($transport);
        $message = \Swift_Message::newInstance($transport);
        $message->setSubject('APIP-GUINEE')
            ->setFrom($messagerie->getExpediteurEmail())
            ->setTo($tabEmail)
            ->setCharset('utf-8')//Le Charset
            ->setContentType('text/html')//Le type du contenu
            ->setBody(
                $this->renderView(
                    'DefaultBundle:Default:email.html.twig', ['representant' => $representant, 'logo' => $logo, 'temoin' => $typeMessage]
                ), 'text/html'
            );

//        var_dump(  $this->renderView(
//            'DefaultBundle:Default:email2.html.twig', ['representant' => $representant,'logo'=>$logo,'temoin'=>$typeMessage]
//        ));die();
        $mailer->send($message);
    }

    public function imageToBas64($path)
    {
        $image = $path;
        $imageData = base64_encode(file_get_contents($image));
        $src = 'data:' . mime_content_type($image) . ';base64,' . $imageData;
        return $src;
    }

    function is_connected()
    {
        var_dump(@fsockopen('https://www.google.com', '465'));
        die();
        if (!$sock = @fsockopen('https://www.google.com', '465')) {
            return false;
        } else {
            return true;
        }

    }

    public function SmsOrange($phoneNumber, Representant $representant, $typeSms = 'depot')
    {

        require 'Osms.php';
        // require 've';

        $config = array(
            'clientId' => 'jliEaf0ABXlgiYsGALjLZKqEahvCbf4F',
            'clientSecret' => 'W9xttZSCD9EwaI8a'
        );

        $osms = new Osms($config);
        //  var_dump($osms);die();
        // retrieve an access token
        $response = $osms->getTokenFromConsumerKey();
        //  die(dump($response));
        if (!empty($response['access_token'])) {
            $senderAddress = 'tel:+224629842274';
            $receiverAddress = 'tel:+224' . $phoneNumber;
            $senderName = 'APIP-GUINEE';
            $messageDepot = 'Bonjour Mr/Mme ' . $representant->getNom() . ' ' . $representant->getPrenom() . ' votre entreprise (' . $representant->getDossierDemande()->getDenominationSociale() . ') est en cours de création.';
            $messageRetrait = 'Bonjour Mr/Mme ' . $representant->getNom() . ' ' . $representant->getPrenom() . ' votre entreprise (' . $representant->getDossierDemande()->getDenominationSociale() . ') est disponible. Veuillez recuperer le dossier à l\'APIP . entre 13H et 15H';
            if ($typeSms == 'depot') {
                $message = $messageDepot;
            } else {
                $message = $messageRetrait;
            }
            $osms->sendSMS($senderAddress, $receiverAddress, $message, $senderName);
            //   var_dump('ok');die();
        } else {
            // error
        }
        //  var_dump('555');die();
    }

//zEde12gFGIAX93hqcPaHSqFBgUqR

    public function formatPhoneNumber($telephone)
    {
        $phone = str_replace('-', '', substr($telephone, -12));
        return $phone;
    }

    public function getToken()
    {
        require 'Osms.php';
        $config = array(
            'clientId' => 'jliEaf0ABXlgiYsGALjLZKqEahvCbf4F',
            'clientSecret' => 'W9xttZSCD9EwaI8a'
        );
        $osms = new Osms($config);
        $response = $osms->getTokenFromConsumerKey();

        if (empty($response['error'])) {
            echo $response['access_token'];
            // echo $osms->getToken();
        } else {
            echo $response['error'];
        }

    }

    public function getAdminContracts()
    {
        require 'Osms.php';
        $config = array(
            'token' => 'zEde12gFGIAX93hqcPaHSqFBgUqR',
        );

        $osms = new Osms($config);

//$osms->setVerifyPeerSSL(false);

        $response = $osms->getAdminContracts('CIV');
// $response = $osms->getAdminContracts();

        if (empty($response['error'])) {
            var_dump($response);
            die();
        } else {
            var_dump($response['error']);
            die();
        }
    }

    public function getPurchaseHistory()
    {
        require 'Osms.php';
        $config = array(
            'token' => 'zEde12gFGIAX93hqcPaHSqFBgUqR',
        );
        $osms = new Osms($config);

//$osms->setVerifyPeerSSL(false);

        $response = $osms->getAdminPurchasedBundles();
//$response = $osms->getAdminPurchasedBundles('CIV');

        if (empty($response['error'])) {
            $purchaseOrders = $response['purchaseOrders'];
            $mytab = [];
            foreach ($purchaseOrders as $purchaseOrder) {
                $mytab['bundleDescription'] = $purchaseOrder['bundleDescription'];
                $mytab['amount'] = $purchaseOrder['orderExecutioninformation']['amount'];
                $mytab['currency'] = $purchaseOrder['orderExecutioninformation']['currency'];

            }
            return $mytab;

        } else {
            echo $response['error'];
        }
    }
}
