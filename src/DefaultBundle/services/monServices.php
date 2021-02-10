<?php
/**
 * Created by PhpStorm.
 * User: Mohamed Kante
 * Date: 19/05/2018
 * Time: 01:05
 */

namespace DefaultBundle\services;

use BanquemondialeBundle\Entity\DossierDemande;
use BanquemondialeBundle\Entity\FormeJuridiqueTraduction;
use BanquemondialeBundle\Entity\Quittance;
use BanquemondialeBundle\Entity\RepartitionQuittance;
use BanquemondialeBundle\Entity\Representant;
use DefaultBundle\Entity\detailReservation;
use DefaultBundle\Entity\factureOrange;
use DefaultBundle\Entity\PaiementOrange;
use DefaultBundle\Entity\PeriodeReservation;
use DefaultBundle\Entity\reservation;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Foris\OmSdk\OmSdk;
use Osms\Osms;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class monServices extends Controller
{
    /**
     * @param $text
     * @return string|string[]|null
     */
    function fixtags($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtoupper($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public function messageSucces($message = "Opération effectuée avec succès.")
    {
        // $message = "Opération effectuée avec succès.";
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

    public function sendSMSandEmail($tiwgView = 'DefaultBundle:Default:email.html.twig', $email = 'kenneh65@gmail.com', $phoneNumber = '621134693', $messageBody = 'Bonjour')
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
                ->setTo($email)
                ->setCharset('utf-8')//Le Charset
                ->setContentType('text/html')//Le type du contenu
                ->setBody(
                    $this->renderView(/*'DefaultBundle:Default:email.html.twig'*/ $tiwgView, ['logo' => $logo, 'messageBody' => $messageBody]), 'text/html'
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

    public function SmstoPromoteur(reservation $reservation)
    {
        $config = array(
            'clientId' => 'jliEaf0ABXlgiYsGALjLZKqEahvCbf4F',
            'clientSecret' => 'W9xttZSCD9EwaI8a'
        );

        $osms = new Osms($config);
        $response = $osms->getTokenFromConsumerKey();
        $senderAddress = 'tel:+224629842274';
        $receiverAddress = 'tel:+224' . $this->formatPhoneNumber($reservation->getTelephone());
        $senderName = 'APIP-GUINEE';
        $message = 'Bonjour Mr/Mme ' . $reservation->getNom() . ' ' . $reservation->getPrenom() . ' votre code de validation est :  ' . $reservation->getValidationCode();

        if (!empty($response['access_token'])) {
            if ($this->pingIPServer() == true) {
                $osms->sendSMS($senderAddress, $receiverAddress, $message, $senderName);
                $this->get('session')->getFlashBag()->add('successSMS', ' SMS envoyé avec succès au promoteur');
            } else {
                $this->get('session')->getFlashBag()->add('echecSMS', 'SMS non envoyé au promoteur');
            }
        } else {
            $this->get('session')->getFlashBag()->add('echecSMS', 'SMS non envoyé au promoteur probleme lie à la conexion internet');
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

    public function SmsOrangetest()
    {
        $config = array(
            'clientId' => 'jliEaf0ABXlgiYsGALjLZKqEahvCbf4F',
            'clientSecret' => 'W9xttZSCD9EwaI8a'
        );
        $osms = new Osms($config);
        $response = $osms->getTokenFromConsumerKey();
        if (!empty($response['access_token'])) {
            $senderAddress = 'tel:+224621134693';
            $receiverAddress = 'tel:+224621134693';
            $message = 'Hello World!';
            $senderName = 'Optimus Prime';
            $osms->sendSMS($senderAddress, $receiverAddress, $message, $senderName);
        } else {
            // error
        }

    }

    public function SmsOrange($phoneNumber, Representant $representant, $typeSms = 'depot')
    {
        $config = array(
            'clientId' => 'jliEaf0ABXlgiYsGALjLZKqEahvCbf4F',
            'clientSecret' => 'W9xttZSCD9EwaI8a'
        );

        $osms = new Osms($config);
        //  var_dump($osms);die();
        // retrieve an access token
        $response = $osms->getTokenFromConsumerKey();
        //  die(dump($response));
        $tabMessage = ['Depot', 'Retrait'];
        $errorMessage = "Operation";
        $senderAddress = 'tel:+224629842274';
        $receiverAddress = 'tel:+224' . $phoneNumber;
        $senderName = 'APIP-GUINEE';
        $messageDepot = 'Bonjour Mr/Mme ' . $representant->getNom() . ' ' . $representant->getPrenom() . ' votre entreprise (' . $representant->getDossierDemande()->getDenominationSociale() . ') est en cours de création.';
        $messageRetrait = 'Bonjour Mr/Mme ' . $representant->getNom() . ' ' . $representant->getPrenom() . ' votre entreprise (' . $representant->getDossierDemande()->getDenominationSociale() . ') est disponible. Veuillez recuperer le dossier à l\'APIP entre 10H et 15H.';

        if ($typeSms == 'depot') {
            $message = $messageDepot;
            $errorMessage = $tabMessage[0];
        } else {
            $message = $messageRetrait;
            $errorMessage = $tabMessage[1];
        }

        if (!empty($response['access_token'])) {
            if ($this->pingIPServer() == true) {
                $osms->sendSMS($senderAddress, $receiverAddress, $message, $senderName);
                $this->get('session')->getFlashBag()->add('successSMS', ' SMS envoyé avec succès au promoteur');
            } else {
                $this->get('session')->getFlashBag()->add('echecSMS', $errorMessage . ' effectué avec succès mais SMS non envoyé au promoteur');
            }
        } else {
            $this->get('session')->getFlashBag()->add('echecSMS', $errorMessage . ' effectué avec succès mais SMS non envoyé au promoteur probleme lie à la conexion internet');
        }
    }

    public function formatPhoneNumber($telephone)
    {
        $phone = str_replace('-', '', substr($telephone, -12));
        return $phone;
    }

    public function getToken()
    {
        require 'Osms.php__';
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
        require 'Osms.php__';
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
        require 'Osms.php__';
        $config = array(
            'token' => 'zEde12gFGIAX93hqcPaHSqFBgUqR',
        );
        $osms = new Osms($config);

//$osms->setVerifyPeerSSL(false);

        $response = $osms->getAdminPurchasedBundles();
//$response = $osms->getAdminPurchasedBundles('CIV');

        if (empty($response['error'])) {
            $purchaseOrders = $response['purchaseOrders'];
            $mytab = array();
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

    public function pingIPServer()
    {
        $host_name = 'ssl0.ovh.net';
        $port_no = '80';

        $st = (bool)@fsockopen($host_name, $port_no, $err_no, $err_str, 10);
        if ($st) {
            // echo 'You are connected to internet.';
            //  die(dump('You are connected to internet.'));
            return true;
        } else {
            return false;
            // die(dump('Sorry! You are offline.'));
        }
//        $host = "ssl0.ovh.net";
//        exec("ping -4 " . $host, $output, $result);
//        if ($result == 0) { die(dump($result));
//            return true;
//        }
//        else if ($result == 1)
//        { die(dump($result));
//            return false;
//        }
    }

    /**
     * @param $customerRef
     * @param $amount
     * @param $orderId
     * @return \GuzzleHttp\Psr7\Response|mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function webPayement($customerRef, $amount, $orderId, $returnPath = 'returning-payement-orange-money')
    {
        $baseUrl = $this->getBaseUrl();
        $secondaireUrl = $this->generateUrl($returnPath);
        $returnUrl = $baseUrl . $secondaireUrl;
        $om = new OmSdk();
        $Currency = ['prod' => 'GNF', 'dev' => 'OUV'];

        $opt = array(
            "currency" => $Currency['prod'],
            "order_id" => $orderId,
            "amount" => $amount,
            "return_url" => $returnUrl,
            "cancel_url" => $returnUrl,
            "notif_url" => $baseUrl,
            "lang" => "fr",
            "reference" => $this->fixtags($customerRef),

        );
        $webPayment = $om->webPayment($opt);
        //die(dump($webPayment));
        if (isset($webPayment['pay_token'])) {
            $transactionStatus = $om->checkTransactionStatus($orderId, $amount, $webPayment['pay_token']);
        }
        // die(dump($transactionStatus));
        return compact('webPayment', 'transactionStatus');
    }

    /**
     * @param $orderId
     * @param $amount
     * @param $payToken
     * @return array|\GuzzleHttp\Psr7\Response|mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function getStatusPayement($orderId, $amount, $payToken)
    {
        $om = new OmSdk();
        $stat = $om->checkTransactionStatus($orderId, $amount, $payToken);
        return $stat;
    }

    public function genrateReferancefactureOrange()
    {
        $em = $this->getDoctrine()->getManager();
        $allfacture = $em->getRepository('DefaultBundle:factureOrange')->findAll();
        // $PaiementOrange = $em->getRepository('DefaultBundle:PaiementOrange')->findOneById($id);
        $ref = '0000001';
        $newfacture = new factureOrange();
        if (empty($allfacture)) {
            //    $newfacture->setPaiementOrange($PaiementOrange);
            $newfacture->setRef('0000001');
            $em->persist($newfacture);
            $em->flush();
            $invoice_number = sprintf('%07d', 1);
            $ref = $invoice_number . '';
        } else {
            // var_dump('$maxIdFacture');die();
            $maxIdFacture = $em->getRepository('DefaultBundle:factureOrange')->getMaxId();
            // var_dump($maxIdFacture->getId());die();
            // var_dump($maxIdFacture);die();
            $nb = 7;
            if (strlen($maxIdFacture->getRef()) > $nb) {
                $nb = strlen($maxIdFacture->getRef()) + 1;
            }
            $ref = sprintf('%0' . $nb . 'd', $maxIdFacture->getId() + 1);
            //  $newfacture->setPaiementOrange($PaiementOrange);
            $newfacture->setRef($ref);
            $em->persist($newfacture);
            $em->flush();
            $ref = $newfacture->getRef();
        }
        return $ref . '';
    }

    public function returnSuccessPayementOrange($data, $idq, $codLang)
    {
        $message = '';
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $request = $data;
        //  die(dump($idq));
        // $codLang = $request->getLocale();
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
        }
        else if ($montantVerse < $quittance->getMontantRestant()) {
            $translated = $this->get('translator')->trans("message_paiement_inferieur_facture");
            $this->get('session')->getFlashBag()->add('info', $translated);
            $quittance->setMontantVerse($montantVerse);
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
                //  $em->flush();
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
                $repartitionQuittance->setModePaiement($modePaiement);
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
                $repartitionQuittance->setModePaiement($modePaiement);
                $em->persist($repartitionQuittance);
            }
            // $em->flush();
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
                $this->forward('BanquemondialeBundle:Quittance:sauvergarderQuittanceDelivre');
                //  $this->sauvergarderQuittanceDelivre($idq);
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
            $this->get('suivistatutdossierservice')->getAndsetStatQuittance('set', $dossierDemande->getId(), null, null);
            $translated = $this->get('translator')->trans("message_facture_payee");
            $this->get('session')->getFlashBag()->add('info', $translated);
//                return $this->redirectToRoute('reporting_quittance');
        }
    }

    public function getModePayement($idmodepayement = 3)
    {
        $em = $this->getDoctrine()->getManager();
        $modePayement = $em->getRepository('BanquemondialeBundle:ModePaiement')->findOneById($idmodepayement);
        return $modePayement;
    }

    /**
     * @param $phoneNumber
     * @param DossierDemande $dossierDemande
     */
    public function payementSmsOrange($phoneNumber, DossierDemande $dossierDemande, $amount)
    {
        $config = array(
            'clientId' => 'jliEaf0ABXlgiYsGALjLZKqEahvCbf4F',
            'clientSecret' => 'W9xttZSCD9EwaI8a'
        );
        $osms = new Osms($config);
        //  var_dump($osms);die();
        // retrieve an access token
        $response = $osms->getTokenFromConsumerKey();
        //  die(dump($response));
        $phone = $this->formatPhoneNumber($phoneNumber);

        $senderAddress = 'tel:+224629842274';
        $receiverAddress = 'tel:+224' . $phone;
        $senderName = 'APIP-GUINEE';
        $pourcentage = 0.02;
        $message = "Bonjour l’entreprise " . $dossierDemande->getNomCommercial() . "\nVeillez générer votre code de paiement\n en composant *144*4*2*1#\nVotre compte sera débité de " . number_format($amount + $amount * $pourcentage, 0, ",", " ") . " GNF\nreparti comme suit :\nFrais de création entreprise  : " . number_format($amount, 0, ",", " ") . " GNF\nFrais de retrait Orange-Money : " . number_format($amount * $pourcentage, 0, ",", " ") . " GNF .";
        // die(dump($message));
        if (!empty($response['access_token'])) {
            if ($this->pingIPServer() == true) {
                $osms->sendSMS($senderAddress, $receiverAddress, $message, $senderName);
                $this->get('session')->getFlashBag()->add('successSMS', ' SMS envoyé avec succès au promoteur');
            } else {
                $this->get('session')->getFlashBag()->add('echecSMS', 'SMS non envoyé au promoteur un probleme lie à la conexion internet');
            }
        } else {
            $this->get('session')->getFlashBag()->add('echecSMS', 'SMS non envoyé au promoteur un probleme lie à la conexion internet');
        }
    }

    /**
     * @param PaiementOrange $paiementOrange
     * @param $status
     * @param $txnid
     */
    public function updatePayementOrange(PaiementOrange $paiementOrange, $status, $txnid)
    {
        $em = $this->getDoctrine()->getManager();
        $paiementOrange->setStatus($status)->setTxnid($txnid);
        $paiementOrange->setEndDate(new \Datetime());
        $em->persist($paiementOrange);
        $em->flush();
    }

    public function updateReservationPayementOrange(PaiementOrange $paiementOrange, $status, $txnid, $op = 'update')
    {
        $em = $this->getDoctrine()->getManager();

        if ($op == 'update') {
            $paiementOrange
                ->setStatus($status)
                ->setTxnid($txnid)
                ->setEndDate(new \Datetime());
            $em->persist($paiementOrange);
            $detailReservation = $em->getRepository('DefaultBundle\Entity\detailReservation')->findOneById($paiementOrange->getDetailReservation()->getId());
            $detailReservation->setStatut(1);
            $detailReservation->setIsPaid(true);
            $em->persist($detailReservation);
        } elseif ($op == 'delete') {

            $detailReservation = $em->getRepository('DefaultBundle\Entity\detailReservation')->findOneById($paiementOrange->getDetailReservation()->getId());

            $resevation = $detailReservation->getReservation();

            $em->remove($detailReservation);


            $em->remove($resevation);


            $em->remove($paiementOrange);

        } elseif ($op == 'initiated') {
            $paiementOrange
                ->setStatus($status)
                ->setTxnid($txnid)
                ->setEndDate(new \Datetime());
            $em->persist($paiementOrange);
            $detailReservation = $em->getRepository('DefaultBundle\Entity\detailReservation')->findOneById($paiementOrange->getDetailReservation()->getId());
            $detailReservation->setStatut(0);
            $detailReservation->setIsPaid(false);
            $em->persist($detailReservation);
        }
        $em->flush();
        // die(dump('ok'));
    }

    /**
     * @param $idq
     * @return bool
     */
    public function verifyQuittancePayementStstus($idq)
    {
        $em = $this->getDoctrine()->getManager();
        $temoin = false;
        $trancsaction = $em->getRepository('DefaultBundle:PaiementOrange')->findBy(["status" => "INITIATED", 'operation' => 'quittance']);
        foreach ($trancsaction as $key => $value) {
            if ($value->getCustomer()['idq'] == $idq) {
                $statusPayement = $this->getStatusPayement($value->getOrderId(), $value->getAmount(), $value->getPayToken());
                if ($statusPayement['status'] == "SUCCESS") {
                    $this->returnSuccessPayementOrange($value->getCustomer(), $idq, $value->getCustomer()['codeLang']);
                    $this->updatePayementOrange($value, $statusPayement['status'], $statusPayement['txnid']);
                    $errorMessage = "le paiement de ce dossier est dejà effectué";
                    $this->get('session')->getFlashBag()->add('echecStatus', $errorMessage);
                    $temoin = true;
                } elseif ($statusPayement['status'] == "FAILED") {
                    $this->updatePayementOrange($value, $statusPayement['status'], $statusPayement['txnid']);

                } elseif ($statusPayement['status'] == "EXPIRED") {
                    $this->updatePayementOrange($value, $statusPayement['status'], $statusPayement['txnid']);
                } elseif ($statusPayement['status'] == "PENDING") {
                    $this->updatePayementOrange($value, $statusPayement['status'], $statusPayement['txnid']);
                }
//              elseif ($statusPayement['status']=="NOT FOUND"){
//                  $this->returnSuccessPayementOrange($value->getCustomer(), $idq, $value->getCustomer()['codeLang']);
//                  $this->updatePayementOrange($value,$statusPayement['status'],$statusPayement['txnid']);
//                  $errorMessage = "le paiement de ce dossier est dejà effectué";
//                  $this->get('session')->getFlashBag()->add('echecStatus', $errorMessage);
//                  $temoin=true;
//              }
            }
        }
        return $temoin;
    }

    public function verifyReservetionPayementStstus($param)
    {

        $em = $this->getDoctrine()->getManager();
        $temoin = false;
        $trancsaction = $em->getRepository('DefaultBundle:PaiementOrange')->findBy(["status" => "INITIATED", 'operation' => 'reservation']);
        foreach ($trancsaction as $key => $value) {
            if (strtolower($value->getCustomer()[0]->getNomCommercial()) == strtolower($param)) {
                $statusPayement = $this->getStatusPayement($value->getOrderId(), $value->getAmount(), $value->getPayToken());
                if ($statusPayement['status'] == "SUCCESS") {
                    $this->updateReservationPayementOrange($value, $statusPayement['status'], $statusPayement['txnid'], 'update');
                    $temoin = true;
                } elseif ($statusPayement['status'] == "FAILED") {

                    $this->updateReservationPayementOrange($value, $statusPayement['status'], $statusPayement['txnid'], 'delete');
                } elseif ($statusPayement['status'] == "EXPIRED") {

                    $this->updateReservationPayementOrange($value, $statusPayement['status'], $statusPayement['txnid'], 'delete');

                } elseif ($statusPayement['status'] == "PENDING") {
                    $this->updateReservationPayementOrange($value, $statusPayement['status'], $statusPayement['txnid'], 'delete');
                } elseif ($statusPayement['status'] == "INITIATED") {
                    $this->updateReservationPayementOrange($value, $statusPayement['status'], $statusPayement['txnid'], 'initiated');
                }
            }
        }
        return $temoin;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        $host = $this->container->get('router')->getContext()->getHost();
        $scheme = $this->container->get('router')->getContext()->getScheme();
        $baseUrl = $scheme . '://' . $host;
        return $baseUrl;
    }

    /**
     * @param $entityName
     * @param $idElement
     * @return mixed
     */
    public function getElementByIdByEntityName($entityName, $idElement)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($entityName)->findOneById($idElement);
        return $entity;
    }

    /**
     * @param Request $search
     * @return bool
     */
    public function verificationNomCommercialReservation(Request $search, $temoin = 'search')
    {

        if ($temoin == 'search') {
            $nomCommercial = $search->request->get($temoin);
        } else {
            $nomCommercial = $temoin;

        }
        $this->verifyReservetionPayementStstus($nomCommercial);
        return $this->isElementExisteInEntity('DefaultBundle:reservation', ['nomCommercial' => $nomCommercial, 'statut' => true]);
    }

    public function verificationNomCommercialDossierDemande(Request $search, $temoin = 'search')
    {

        if ($temoin == 'search') {
            $nomCommercial = $search->request->get($temoin);
        } else {
            $nomCommercial = $temoin;

        }
        return $this->isElementExisteInEntity('BanquemondialeBundle:DossierDemande', ['denominationSociale' => $nomCommercial]);
    }

    public function verificationNomCommercialArchiveNomCommerciale(Request $search, $temoin = 'search')
    {

        if ($temoin == 'search') {
            $nomCommercial = $search->request->get($temoin);
        } else {
            $nomCommercial = $temoin;

        }
        return $this->isElementExisteInEntity('BanquemondialeBundle:DossierDemande', ['denominationSociale' => $nomCommercial]);
    }

    /**
     * @param $nomCommercial
     * @return bool
     */
    public function verificationNomCommercial(Request $search, $temoin = 'search')
    {
        // $this->updatePerideReservation();
        $actuels = null;
        $nomUtilise = false;
        $em = $this->getDoctrine()->getManager();
        if ($temoin == 'search') {
            $nomCommercial = $search->request->get($temoin);
        } else {
            $nomCommercial = $temoin;

        }
        $resultaRecherNomCommercialUpdate = $em->getRepository('BanquemondialeBundle:DossierDemande')->recherNomcommercialUpdate(true, $nomCommercial);
        if (count($resultaRecherNomCommercialUpdate) > 0) {
            $nomUtilise = true;
        } else {
            $actuels = $em->getRepository('BanquemondialeBundle:DocumentCollected')->nomCommercialExiste($nomCommercial);
            if (count($actuels) > 0) {
                $nomUtilise = true;
            }
        }
        return $nomUtilise;

    }

    public function testAPIwebPayement()
    {
        $om = new OmSdk();
        $opt = array(
            "currency" => "OUV",
            "order_id" => '33129',
            "amount" => 5000,
            "return_url" => 'http://apip-guineesms.dev.gov/fr',
            "cancel_url" => 'https://apip.gov.gn/synergui',
            "notif_url" => 'https://apip.gov.gn/synergui',
            "lang" => "fr",
            "reference" => 'Kante Mohamed',

        );
        $result = $om->webPayment($opt);
        //   die(dump($result));
        return $result;
    }

    public function getlocal($id = 1)
    {
        return $id;
    }

    public function isElementExisteInEntity($entity, array $tabElement)
    {
        $em = $this->getDoctrine()->getManager();
        $virife = $em->getRepository($entity)->findOneBy($tabElement);
        if (!empty($virife)) {
            return true;
        } else {
            return false;
        }
    }

    public function moisEnfr($nombre)
    {

        return ucfirst(numfmt_create('fr_FR', \NumberFormatter::SPELLOUT)->format($nombre)) . ' mois';
    }

    public function secondsToTime($dateFin)
    {
        $date1 = new \DateTime();
        if (strtotime(date_format($date1, 'Y-m-d H:i:s')) <= strtotime(date_format(new \DateTime($dateFin), 'Y-m-d H:i:s'))) {
            $date2 = $date1->diff(new \DateTime($dateFin));
            return [
                'Jour Total' => $date2->days
                ,
                'Ans' => $date2->y,
                'Mois' => $date2->m,
                'Jours' => $date2->d,
                'Heures' => $date2->h,
                'Minutes' => $date2->i,
                'Secondes' => $date2->s
            ];
        } else {
            return [
                'Ans' => 0,
                'Mois' => 0,
                'Jours' => 0,
                'Heures' => 0,
                'Minutes' => 0,
                'Secondes' => 0
            ];
        }

    }

    function secondsToTimebis($seconds)
    {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a Jours, %h heurs, %i minutes et   %s seconds');
    }

    public function getDateFinDabonnement($nbrMois, $dateDebut)
    {
        $days = $nbrMois * 30;
        $date = strtotime("+$days day", strtotime($dateDebut));
        $dateFinDabonnement = date("Y-m-d H:i:s", $date);
        return $dateFinDabonnement;
    }

    public function getReservationsEncour($reserv)
    {
        $em = $this->getDoctrine()->getManager();
        $reservations = $em->getRepository('DefaultBundle\Entity\reservation')->getReservationEncour($reserv);
        return $reservations;
    }

    public function TabnumbrtoMois($nombre)
    {
        $table = [];
        for ($i = 1; $i < $nombre + 1; $i++) {
            $table[ucfirst(numfmt_create('fr_FR', \NumberFormatter::SPELLOUT)->format($i)) . ' mois'] = $i;
        }

        return $table;
    }

    public function numbrtoMois($nombre, $local = 'fr_FR', $mois = 'mois')
    {
        $moi = ucfirst(numfmt_create($local, \NumberFormatter::SPELLOUT)->format($nombre)) . ' ' . $mois;
        return $moi;
    }

    public function updatePerideReservation()
    {
        $em = $this->getDoctrine()->getManager();
        $formJuridique = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findAll();
        for ($i = 0; $i < count($formJuridique); $i++) {
            for ($j = 0; $j < 13; $j++) {
                $periode = new PeriodeReservation();
                if ($j == 0) {
                    $periode
                        ->setLibelle(($formJuridique[$i]->getLangue()->getId() == 1) ? '7 jours' : '7 hours')
                        ->setAmount(100 * $j)
                        ->setFormeJuridiqueTraduction($formJuridique[$i])
                        ->setNombre($j);
                    $em->persist($periode);
                }
//                elseif ($j!=0){
//                    $periode
//                        ->setLibelle(($formJuridique[$i]->getLangue()->getId()==1) ? $this->numbrtoMois($j,'fr_FR','mois') :$this->numbrtoMois($j,'en_US',($j==1)?'month':'months'))
//                        ->setAmount(100*$j)
//                        ->setFormeJuridiqueTraduction($formJuridique[$i])
//                        ->setNombre($j);
//                    $em->persist($periode);
//                }
                $em->flush();
            }
        }


//        for($i=0;$i<count($formJuridique);$i++){
//            for ($j=1;$j<13;$j++){
//                $periode=new PeriodeReservation();
//                if ($formJuridique[$i]->getLangue()->getId()==1){
//                    $libelle=$this->numbrtoMois($j,'fr_FR','mois');
//                }elseif ($formJuridique[$i]->getLangue()->getId()==2){
//                    $libelle=$this->numbrtoMois($j,'en_US','month');
//                }
//                $periode
//                    ->setLibelle($this->numbrtoMois($libelle))
//                    ->setAmount(100*$j)
//                    ->setFormeJuridiqueTraduction($formJuridique[$i])
//                    ->setNombre($j);
//                $em->persist($periode);
//                $em->flush();
//            }
//        }

        return die(dump($periode));
    }

    public function generateCustomToken($min = 555, $max = 1000)
    {

        return rand($min, $max);
    }

    public function listeReservationEnExpiration()
    {
        $em = $this->getDoctrine()->getManager();
        $reservation = $em->getRepository('DefaultBundle\Entity\reservation')->getReservationEnExpiration();
        return $reservation;
    }

    public function listeReservationExpirer()
    {
        $em = $this->getDoctrine()->getManager();
        $reservation = $em->getRepository('DefaultBundle\Entity\reservation')->getReservationExpirer();
        return $reservation;
    }

    public function getJour($jour = 5)
    {
        return intval($jour);
    }

    public function notifPackSmsExpired($message)
    {
        $this->get('session')->getFlashBag()->add('echecSMS', $message);
        return 1;
    }

    public function makeReAbonnement($em, reservation $reservation, FormeJuridiqueTraduction $formeJuridiqueTraduction, $operation, $nbrMois, $dateDebut)
    {
        $detailReservation = new detailReservation();

        $detailReservation
            ->setOperation($operation)
            ->setReservation($reservation)
            ->setDateDebut(new \DateTime())
            ->setDateFin(new \DateTime($this->getDateFinDabonnement($nbrMois,
                date_format($dateDebut, 'Y-m-d H:i:s'))))
            ->setIsPaid(false)
            ->setFormeJuridiqueTraduction($formeJuridiqueTraduction)
            ->setStatut(true);
        $em->persist($detailReservation);
        $em->flush();
//
//
//
//
//        $detailReservation
//            ->setReservation($reservation)
//            ->setDateDebut($dateDebut)
//            ->setFormeJuridiqueTraduction($formeJuridiqueTraduction)
//            ->setStatut(1)
//            ->setDateFin(new \DateTime($this->getDateFinDabonnement($nbrMois,
//            date_format($dateDebut, 'Y-m-d H:i:s'))));
//         $em->persist($detailReservation);

        /// $this->messageSucces();
    }

    public function updateStututAbonnement(reservation $reservation, EntityManager $entityManager)
    {
        $detailReservation = $reservation->getDetailReservation();
        foreach ($detailReservation as $da) {
            $da->setStatut(0);
            $entityManager->persist($da);
            $entityManager->flush();
        }
    }

    public function updateAbonnement(reservation $reservation, EntityManager $entityManager, $datedebut, $datefin)
    {
        $reservation->setDateDebut($datedebut);
        $reservation->setDateFin($datefin);
        $entityManager->persist($reservation);
        $entityManager->flush();
    }

    public function updatePaiementOrangeWhenUpdateDossier($numeroDossier)
    {
        $em = $this->getDoctrine()->getManager();
        $paiementOranges = $em->getRepository('DefaultBundle:PaiementOrange')->findBy(['numeroDossier' => $numeroDossier, 'operation' => 'quittance']);
        foreach ($paiementOranges as $elementPOM) {
            $elementPOM->setStatus('INITIATED');
            $em->persist($elementPOM);
            $em->flush();
        }
    }

    function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function ajoutQuittance($idd)
    {
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

    public function jsonDecode($str)
    {
        return json_decode($str);
    }

    /**
     * @param $entity
     * @param $id
     */
    function updateStatus($entity, $id, $value = false)
    {
        $em = $this->getDoctrine()->getManager();
        $element = $em->getRepository($entity)->findOneById($id);
        //  die(dump($setter));
        $element->setStatut($value);
        $em->persist($element);
        $em->flush();
    }

    public function createAcronym($string, $onlyCapitals = false)
    {
        $output = null;
        $token = strtok($string, ' ');
        while ($token !== false) {
            $character = mb_substr($token, 0, 1);
            if ($onlyCapitals and mb_strtoupper($character) !== $character) {
                $token = strtok(' ');
                continue;
            }
            $output .= $character;
            $token = strtok(' ');
        }
        return $output;
    }

    public function truncateWord($string, $length)
    {
        return (strlen($string) > $length) ? substr($string, 0, (intval($length) - 5)) . '...' : $string;
    }

    public function UpdateSecteurActiviteNonValid()
    {
        $em = $this->getDoctrine()->getManager();
        $apipSecteur = $em->getRepository('BanquemondialeBundle\Entity\SecteurActivite')->findBy(['actif' => true]);
        $DNISecteur = [1210,
            8399,
            7194,
            1110,
            1111,
            1112,
            1113,
            1114,
            1115,
            1116,
            1117,
            1118,
            1119,
            1120,
            1121,
            1122,
            1123,
            1124,
            1125,
            2110,
            2111,
            2112,
            2113,
            2114,
            2115,
            2116,
            2117,
            2118,
            2119,
            2120,
            2121,
            3110,
            3111,
            3113,
            3114,
            3115,
            3116,
            3117,
            3118,
            3119,
            3121,
            3122,
            3123,
            3124,
            3125,
            3126,
            3127,
            3129,
            3130,
            3131,
            3132,
            3133,
            3134,
            3140,
            3141,
            3142,
            3143,
            3144,
            3145,
            3146,
            3147,
            3148,
            3149,
            3150,
            3151,
            3152,
            3153,
            3154,
            3155,
            8216,
            3156,
            3157,
            3158,
            3159,
            3160,
            3161,
            3162,
            3163,
            3164,
            3165,
            3166,
            3167,
            3168,
            3169,
            3170,
            3171,
            3172,
            3173,
            3174,
            3175,
            3176,
            7193,
            8104,
            5118,
            3177,
            3178,
            3179,
            3180,
            3181,
            3182,
            3183,
            3184,
            3185,
            3186,
            9201,
            3187,
            5119,
            3188,
            6162,
            3189,
            3190,
            3191,
            3192,
            3193,
            3194,
            3195,
            2122,
            3196,
            3197,
            8214,
            8215,
            3198,
            3199,
            5110,
            5111,
            5112,
            5113,
            5114,
            5115,
            5116,
            5117,
            6101,
            6102,
            6103,
            6104,
            6105,
            6106,
            3101,
            6107,
            6108,
            6109,
            6111,
            6112,
            6113,
            6114,
            6115,
            6116,
            6117,
            6118,
            6119,
            6120,
            6121,
            6122,
            6123,
            6124,
            6125,
            6126,
            6127,
            6128,
            6129,
            6131,
            6132,
            6170,
            6134,
            6135,
            6136,
            6137,
            6138,
            6139,
            6140,
            6141,
            6142,
            6143,
            6144,
            6145,
            6146,
            6147,
            6148,
            6149,
            6150,
            6151,
            6152,
            6153,
            6154,
            6155,
            6156,
            6157,
            6158,
            3102,
            6159,
            6160,
            7110,
            7111,
            7113,
            7112,
            7114,
            7115,
            7116,
            7117,
            7118,
            7120,
            7121,
            7122,
            7123,
            7130,
            7131,
            7132,
            7190,
            7191,
            7192,
            8101,
            8102,
            8103,
            8310,
            8320,
            8321,
            8322,
            8323,
            5124,
            8325,
            6161,
            8330,
            8201,
            8202,
            8203,
            8204,
            8205,
            8206,
            8207,
            8208,
            8209,
            8210,
            8211,
            8212,
            8213,
            9410,
            9411,
            9412,
            9413,
            9414,
            9415,
            9420,
            9490,
            8424,
            8402,
            4101,
            8404,
            8405,
            8406,
            8407,
            8408,
            8409,
            8410,
            8411,
            8413,
            8414,
            8415,
            8416,
            8417,
            8418,
            8419,
            8420,
            8421,
            8422,
            8423,
            9101,
            8401,
            6169,
            6168,
            8425,
            7140,
            8217,
            8403,
            8430,
            8431,
            3112,
            6165,
            8450,
            2125,
            8350,
            2160,
            1126,
            8440,
            1135,
            2180,
            3109,
            3200,
            5101,
            7141,
            4102,
            8300,
            8351,
            8352,
            8353,
            8354,
            8355,
            8356,
            8340,
            9110,
            3100,
            8301,
            2130,
            9301,
            2129,
            6190,
            7199,
            9851,
            8398,
            8397,
            9901,
            9902,
            9904,
            9480,
            9501,
            3139,
            0000,
            6110,
            9416,
            6191,
            6192,
            6193,
            3103,
            9401,
            9701
        ];
        foreach ($apipSecteur as $sectApip) {
            if (array_search($sectApip->getCode(), $DNISecteur)) {
                $sectApip->setActif(true);
                $em->persist($sectApip);
                $em->flush();
            } else {
                $sectApip->setActif(false);
                $em->persist($sectApip);
                $em->flush();
            }
        }
        var_dump('traitement effectuer');
        die();
    }

    public function removeFolders($choix = 'depot')
    {

    }

    public function getRccmbyNumDossier($idd)
    {
        $em = $this->getDoctrine()->getManager();
        $dossierDemmande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findOneById($idd);
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($dossierDemmande);
        return $rccm;
    }

    public function getNifbyNumDossier($idd)
    {
        $em = $this->getDoctrine()->getManager();
        $dossierDemmande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findOneById($idd);
        $niff = $em->getRepository('BanquemondialeBundle:Nif')->findOneByDossierDemande($dossierDemmande);
        return $niff;
    }

    public function getUserById($userId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UtilisateursBundle:Utilisateurs')->findOneBy(['id' => $userId]);
        return $user;
    }
    /**
     * @param $array
     * @param $column
     * @return array
     */
    function uniqueArray($array, $column)
    {
        $unique_arr = array_unique(array_column($array, $column));
        return array_values(array_intersect_key($array, $unique_arr));
    }

    /**
     * @param DossierDemande $dossierDemande
     * @return string
     */
    public function getRepEntreprisData(DossierDemande $dossierDemande)
    {
        $arrayDossier = [];
        $arrayRepresentants = [];
        $em = $this->getDoctrine()->getManager();
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($dossierDemande);
        $nif = $em->getRepository('BanquemondialeBundle:Nif')->findOneByDossierDemande($dossierDemande);
        $quartier = $em->getRepository('BanquemondialeBundle:Quartier')->findOneBy(
            ['sousPrefecture' => empty($dossierDemande->getSousPrefecture())?000:$dossierDemande->getSousPrefecture()->getId(), 'actif' => '1']);
        $gerantPrinci = $em->getRepository('BanquemondialeBundle:Representant')->findOneBy([
            'dossierDemande' => $dossierDemande->getId(), 'gp' => 1]);
        $arrayDossier = [
            'id' => $dossierDemande->getId(),
            'denominationSociale' => $dossierDemande->getDenominationSociale(),
            'dateCreation' => date_format($dossierDemande->getDateCreation(), 'm-d-Y'),
            'idRegion' => empty($dossierDemande->getRegion())?null: $dossierDemande->getRegion()->getId(),
            'idPrefecture' => empty($dossierDemande->getPrefecture())?null: $dossierDemande->getPrefecture()->getId(),
            'idSouprefecture' =>empty($dossierDemande->getSousPrefecture())?null:$dossierDemande->getSousPrefecture()->getId(),
            'idQuartier' => empty($quartier)?"": $quartier->getId(),
            'categorieActivite' => $dossierDemande->getSecteurActivite()->getCategorieActivite()->getId(),
            'idSecteurActivite' => $dossierDemande->getSecteurActivite()->getId(),
            'telephoneEntreprise' => $dossierDemande->getTelephone(),
            'emailEntreprise' => $dossierDemande->getEmail(),
            'rccm' => empty($rccm)?"": $rccm->getNumRccmEntreprise(),
            'nif' => $nif->getNumeroIdentificationFiscale(),
            'formjuridique' => empty($dossierDemande->getFormeJuridique())?null:$dossierDemande->getFormeJuridique()->getId(),
            'capitalSocial' => $dossierDemande->getCapitalSocial(),
            'dateLivraisonRccm' => empty($rccm) ? null : date_format($rccm->getDate(), 'm-d-Y'),
            'dateLivraisonNif' => empty($nif->getDate()) ? null : date_format($nif->getDate(), 'm-d-Y'),
            'dateDebut' => empty($dossierDemande->getDateDebut())?null:date_format($dossierDemande->getDateDebut(), 'm-d-Y'),
            'nomRepresentant' => $gerantPrinci->getNom(),
            'prenomRepresentant' => $gerantPrinci->getPrenom(),
            'dateDeNaissanceRepresentant' => date_format($gerantPrinci->getDateDeNaissance(), 'm-d-Y'),
            'telephoneRepresentant' => empty($gerantPrinci->getTelephone())? $dossierDemande->getTelephone():$gerantPrinci->getTelephone(),
            'genreRepresentant' => $gerantPrinci->getGenre()->getCode(),
            'emailRepresentant' => empty($gerantPrinci->getEmail())?$dossierDemande->getEmail():$gerantPrinci->getEmail(),
        ];
        return  json_encode($arrayDossier);
    }
}
