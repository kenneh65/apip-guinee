<?php
/**
 * Created by PhpStorm.
 * User: Mohamed Kante
 * Date: 19/05/2018
 * Time: 01:05
 */

namespace DefaultBundle\services;

use Api\Api;
use BanquemondialeBundle\Entity\DossierDemande;
use BanquemondialeBundle\Entity\RepartitionQuittance;
use BanquemondialeBundle\Entity\Representant;
use BanquemondialeBundle\Form\QuittanceType;
use DefaultBundle\Entity\factureOrange;
use DefaultBundle\Entity\PaiementOrange;
use Dompdf\Dompdf;
use Dompdf\Options;
//use Ibracilinks\OrangeMoney\OrangeMoney;
use Foris\OmSdk\OmSdk;
use http\Env\Request;
use Osms\Osms;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class monServices extends Controller
{
    /**
     * @param $text
     * @return string|string[]|null
     */
    function fixtags($text){
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
        $tabMessage = ['Depot', 'Retrait'];
        $errorMessage = "Operation";
        $senderAddress = 'tel:+224629842274';
        $receiverAddress = 'tel:+224' . $phoneNumber;
        $senderName = 'APIP-GUINEE';
        $messageDepot = 'Bonjour Mr/Mme ' . $representant->getNom() . ' ' . $representant->getPrenom() . ' votre entreprise (' . $representant->getDossierDemande()->getDenominationSociale() . ') est en cours de création.';
        $messageRetrait = 'Bonjour Mr/Mme ' . $representant->getNom() . ' ' . $representant->getPrenom() . ' votre entreprise (' . $representant->getDossierDemande()->getDenominationSociale() . ') est disponible. Veuillez recuperer le dossier à l\'APIP .';

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
        $host = "ssl0.ovh.net";
        exec("ping -4 " . $host, $output, $result);
        if ($result == 0) {
            return true;
        } else if ($result == 1) {
            return false;
        }
    }

    /**
     * @param $customerRef
     * @param $amount
     * @param $orderId
     * @return \GuzzleHttp\Psr7\Response|mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function webPayement($customerRef, $amount, $orderId)
    {

        $baseUrl=$this->getBaseUrl();
        $returnUrl = $baseUrl. 'admin/returning-payement-orange-money';
        $om = new OmSdk();
        $opt = array(
            "currency" => "OUV",
            "order_id" => $orderId,
            "amount" => $amount,
            "return_url" => $returnUrl,
            "cancel_url" => $returnUrl,
            "notif_url" => $baseUrl,
            "lang" => "fr",
            "reference" => $this->fixtags($customerRef),

        );
        $webPayment = $om->webPayment($opt);
      //  die(dump($webPayment));
        $transactionStatus = $om->checkTransactionStatus($orderId, $amount, $webPayment['pay_token']);
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
        } else if ($montantVerse < $quittance->getMontantRestant()) {
            $translated = $this->get('translator')->trans("message_paiement_inferieur_facture");
            $this->get('session')->getFlashBag()->add('info', $translated);
            $quittance->setMontantVerse($montantVerse);
        } else {
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
    public function payementSmsOrange($phoneNumber, DossierDemande $dossierDemande)
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
        $phone = $this->formatPhoneNumber($phoneNumber);

        $senderAddress = 'tel:+224629842274';
        $receiverAddress = 'tel:+224' . $phone;
        $senderName = 'APIP-GUINEE';
        $message = "Bonjour l’entreprise " . $dossierDemande->getNomCommercial() . "\nVeillez générer votre code de paiement\n en composant *144*4*2*1#\nVotre compte sera débité de 216 500 GNF\nreparti comme suit :\nFrais de création entreprise individuelle : 212 500 GNF\nFrais de retrait Orange-Money : 4 000 GNF  
         .";
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
    public  function updatePayementOrange(PaiementOrange $paiementOrange,$status,$txnid){
        $em=$this->getDoctrine()->getManager();
       $paiementOrange->setStatus($status)->setTxnid($txnid);
       $em->persist($paiementOrange);
       $em->flush();
    }

    /**
     * @param $idq
     * @return bool
     */
    public  function verifyQuittancePayementStstus($idq){
        $em=$this->getDoctrine()->getManager();
        $temoin=false;
        $trancsaction=$em->getRepository('DefaultBundle:PaiementOrange')->findBy(["status"=>"INITIATED"]);
        foreach ($trancsaction  as $key=>$value){
           if ($value->getCustomer()['idq']==$idq){
               $statusPayement= $this->getStatusPayement($value->getOrderId(),$value->getAmount(),$value->getPayToken());
              if ($statusPayement['status']=="SUCCESS"){
                  $this->returnSuccessPayementOrange($value->getCustomer(), $idq, $value->getCustomer()['codeLang']);
                  $this->updatePayementOrange($value,$statusPayement['status'],$statusPayement['txnid']);
                  $errorMessage = "le paiement de ce dossier est dejà effectué";
                  $this->get('session')->getFlashBag()->add('echecStatus', $errorMessage);
                  $temoin=true;
              }
              elseif ($statusPayement['status']=="FAILED"){
                  $this->updatePayementOrange($value,$statusPayement['status'],$statusPayement['txnid']);

              }
              elseif ($statusPayement['status']=="EXPIRED"){
                  $this->updatePayementOrange($value,$statusPayement['status'],$statusPayement['txnid']);
              }
              elseif ($statusPayement['status']=="PENDING"){
                  $this->updatePayementOrange($value,$statusPayement['status'],$statusPayement['txnid']);
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
        $parameter_locale = $this->container->get('router')->getContext()->getParameter('_locale');
        $scheme = $this->container->get('router')->getContext()->getScheme();
        $port = $this->container->get('router')->getContext()->getHttpPort();
        $baseUrl = $scheme . '://' . $host . '/' . $parameter_locale . '/';
        return $baseUrl;
    }

    /**
     * @param $entityName
     * @param $idElement
     * @return mixed
     */
    public function getElementByIdByEntityName($entityName,$idElement){
        $em=$this->getDoctrine()->getManager();
        $entity=$em->getRepository($entityName)->findOneById($idElement);
        return $entity;
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
}
