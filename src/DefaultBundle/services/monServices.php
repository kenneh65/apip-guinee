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
use Foris\OmSdk\OmSdk;
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
        try {
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
            if ($this->check_internet_connection() == true) {
                $mailer->send($message);
                $this->get('session')->getFlashBag()->add('successMail', ' Mail envoyé avec succès au promoteur');
            } else {
                $this->get('session')->getFlashBag()->add('echecMail', 'Dossier enregistré avec succès mais Mail non envoyé au promoteur un probleme lie à la conexion internet ');
            }
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('echecMail', 'Dossier enregistré avec succès mais Mail non envoyé au promoteur un probleme lie au server de messagerie');
        }
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
        $tabMessage=['Depot','Retrait'];
        $errorMessage="Operation";
        $senderAddress = 'tel:+224629842274';
        $receiverAddress = 'tel:+224' . $phoneNumber;
        $senderName = 'APIP-GUINEE';
        $messageDepot = 'Bonjour Mr/Mme ' . $representant->getNom() . ' ' . $representant->getPrenom() . ' votre entreprise (' . $representant->getDossierDemande()->getDenominationSociale() . ') est en cours de création.';
        $messageRetrait = 'Bonjour Mr/Mme ' . $representant->getNom() . ' ' . $representant->getPrenom() . ' votre entreprise (' . $representant->getDossierDemande()->getDenominationSociale() . ') est disponible. Veuillez recuperer le dossier à l\'APIP entre 11H et 16H.';

        if ($typeSms == 'depot') {
            $message = $messageDepot;
            $errorMessage=$tabMessage[0];
        } else {
            $message = $messageRetrait;
            $errorMessage=$tabMessage[1];
        }

        if (!empty($response['access_token'])) {
            if ($this->pingIPServer() == true) {
                $osms->sendSMS($senderAddress, $receiverAddress, $message, $senderName);
                $this->get('session')->getFlashBag()->add('successSMS', ' SMS envoyé avec succès au promoteur');
            } else {
                $this->get('session')->getFlashBag()->add('echecSMS', $errorMessage.' effectué avec succès mais SMS non envoyé au promoteur');
            }
        }
        else {
            $this->get('session')->getFlashBag()->add('echecSMS', $errorMessage.' effectué avec succès mais SMS non envoyé au promoteur probleme lie à la conexion internet');
        }
    }
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
    /**
     * Check Internet Connection.
     *
     * @param string $sCheckHost Default: www.google.com
     * @return           boolean
     * @author           Pierre-Henry Soria <ph7software@gmail.com>
     * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
     */
    function check_internet_connection()
    {
        return (bool)@fsockopen('www.google.com', 80, $iErrno, $sErrStr, 5);
    }
    public function getOperationEncour()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT op FROM DefaultBundle:typeOperation op
                WHERE op.encour=:encour'
        )->setParameter('encour', true);
        $typeOperationEncour = $query->setMaxResults(1)->getOneOrNullResult();
        return $typeOperationEncour;
    }
    function pingIPServer()
    {
        $host = "ssl0.ovh.net";
        exec("ping -4 " . $host, $output, $result);
        if ($result == 0) {
            return true;
        } else if ($result == 1) {
            return false;
        }
    }

    public function OrangeMoney(){
        $om=new OmSdk();
        var_dump($om);die();
      return 'WELCOM ORANGE MONEY PAGE !!!';
    }

}
