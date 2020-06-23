<?php

namespace DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use DefaultBundle\Entity\NifRecu;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DefaultBundle\Entity\ReponseJson;
use BanquemondialeBundle\Entity\Nif;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\HttpFoundation\JsonResponse;
use BanquemondialeBundle\Entity\FormulaireDelivre;
use phpseclib\Net\SFTP;

class ServiceWSController extends Controller {

    /**
     * @Route("/ws/get-nif",name="get-nif")
     * @Method("POST")
     */
    public function getNifDataAction(Request $request) {
//        $data = '{"numeroDossier": "GU-CE-GN00000204",
// "numeroIdentificationFiscale": "031245",
// "dateImmatriculation":"2018-03-29 11:44:25",
// "numeroFormulaire":"0124/MB/DNI/CI/2018",
// "nomFichierEnvoye":"nif_GU-CE-GN00000204.pdf",
// "nomPrepose":"KEITA",
// "prenomPrepose":"SALIF"
// }';
        $encoders = array(new JsonEncoder(new JsonEncode(JSON_UNESCAPED_UNICODE), new JsonDecode(false)));
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $em = $this->getDoctrine()->getManager();
        $poleNif = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("BNI");
        $data = $request->getContent();
        $codeRetour = "";

        $nifReceived = $serializer->deserialize($data, 'DefaultBundle\Entity\NifRecu', 'json');
        $errors = $this->get('validator')->validate($nifReceived);
        $reponseWSReprository = $em->getRepository('DefaultBundle:ReponseWS');
        $reponse = new ReponseJson();
        $password = $request->headers->get('Php-Auth-Pw');
        //die(dump($password));
        $username = $request->headers->get('Php-Auth-User');
        if ((!$password || !$username)) {
            $codeRetour = "DNI00";
            $reponse->setNumeroDossier($nifReceived->getNumeroDossier());
            $reponse->setCode($codeRetour);
            $reponse->setDescription("Erreur d'authentification");

            $reponseJson = $serializer->serialize($reponse, 'json');
            return new Response($reponseJson);
        } else {
            if (!(strcmp($username, "userDNI") === 0 && strcmp($password, "1ntegr@tionDNI!PIP") === 0)) {
                $codeRetour = "DNI00";
                $reponse->setNumeroDossier($nifReceived->getNumeroDossier());
                $reponse->setCode($codeRetour);
                $reponse->setDescription("Erreur d'authentification");

                $reponseJson = $serializer->serialize($reponse, 'json');
                return new Response($reponseJson);
            }
        }

        //$validator->validate($nifReceived);

        if (count($errors) > 0) {

            $codeRetour = "DNI02";
            $reponse->setNumeroDossier($nifReceived->getNumeroDossier());
            $reponse->setCode($codeRetour);
            $reponse->setDescription($reponseWSReprository->getDescriptionByCode($codeRetour));

            //$historique->setCodeRetourDNI($codeRetour);
            //$em->persist($historique);
            $reponseJson = $serializer->serialize($reponse, 'json');
            //die(dump($reponseJson));
            return new Response($reponseJson);
        }
        $historique = $em->getRepository("DefaultBundle:HistoriqueEchangeDNI")->findLastDataSent($nifReceived->getNumeroDossier()); //findBy(array('numeroDossier' => $nifReceived->getNumeroDossier()),array('Id','DESC'),1);
        if ($historique) {
            $historique->setContenuDataRecu($data);
        }
        if ($nifReceived->getCode() && $nifReceived->getCode() === "DNI01") {
           /* if ($nifReceived->getNomFichierEnvoye()) {
                //$fullPath = "public/NIFp_dni/"; // parametrer avec l'adresse FTP
                $nomFichier = $nifReceived->getNomFichierEnvoye();

                //Conexion sft
                try {
                    set_error_handler(function($errno, $errstr, $errfile, $errline ) {
                        throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
                    });
                    $sftp = new SFTP('10.13.15.204');
                    if (!$sftp->login('apip', 'apip@P@ssw0d')) {//'dte', 'gain-or2k'
                        $codeRetour = "DNI00";
                        $reponse->setNumeroDossier($nifReceived->getNumeroDossier());
                        $reponse->setCode($codeRetour);
                        $reponse->setDescription($reponseWSReprository->getDescriptionByCode($codeRetour));

                        $reponseJson = $serializer->serialize($reponse, 'json');
                        return new Response($reponseJson);
                    }
                    restore_error_handler();
                } catch (\ErrorException $e) {
//                die('fff');
                    $codeRetour = "DNI00";
                    $reponse->setNumeroDossier($nifReceived->getNumeroDossier());
                    $reponse->setCode($codeRetour);
                    $reponse->setDescription($reponseWSReprository->getDescriptionByCode($codeRetour));

                    $reponseJson = $serializer->serialize($reponse, 'json');
                    return new Response($reponseJson);
                }
                ///die(dump($sftp->nlist('public/NIFp_dni')));
                $sftp->chdir('public/OUT'); //home/apip/in
                //$contenu = $sftp->get($nomFichier);
                //die(dump($contenu));//
                //le repertoire exite now tester l'exitance du fichier
                /*if (!$sftp->get($nomFichier)) {
                    $sftp->disconnect();
                    $codeRetour = "DNI06";
                    $reponse->setNumeroDossier($nifReceived->getNumeroDossier());
                    $reponse->setCode($codeRetour);
                    $reponse->setDescription($reponseWSReprository->getDescriptionByCode($codeRetour));

                    $reponseJson = $serializer->serialize($reponse, 'json');
                    return new Response($reponseJson);
                } else {
                    
                }
            }*/
            //fin 

            $date = new \DateTime();

           // if($nifReceived->getDateImmatriculation()){
                $dateTimeImmat = date_create_from_format('Y-m-d H:i:s', $nifReceived->getDateImmatriculation());           
           // }


            if ($historique) {
                //Controle donnée dejà enregistrer
                $historique->setDateReceptionDonneeNIF($date); //($nifReceived->getDateImmatriculation());
                //$historique->setNumeroFormulaire($nifReceived->getNumeroFormulaire());
                //$historique->setNumeroIdentificationFiscale($nifReceived->getNumeroIdentificationFiscale());
                $historique->setNomFichierEnvoye($nifReceived->getNomFichierEnvoye());
                $historique->setContenuDataRecu($data);
                $em->persist($historique);
                $em->flush();
                $dossierDemande = $historique->getDossierDemande();
                $nif = $em->getRepository('BanquemondialeBundle:Nif')->findOneBy(array('dossierDemande' => $historique->getDossierDemande()));
                if ($nif) {

                    $nif->setDossierDemande($dossierDemande);
                    $nif->setNumeroIdentificationFiscale($nifReceived->getNumeroIdentificationFiscale());
                    $strDate = date('Y-m-d', strtotime($nifReceived->getDateImmatriculation()));
                    $dateImmat = date_create_from_format('Y-m-d', $strDate);
                    $nif->setDate($dateImmat);
                    if ($nifReceived->getNumeroFormulaire()) {
                        $tab = explode("/", $nifReceived->getNumeroFormulaire());
                        if ($tab) {
                            $numFormulaire = $tab[0];
                            $index = strpos($nifReceived->getNumeroFormulaire(), "/");
                            $nif->setNumeroFormulaire($numFormulaire);
                            $numFormulaireBis = substr($nifReceived->getNumeroFormulaire(), $index);
                            $nif->setNumeroFormulaireBis($numFormulaireBis);
                        }
                    }

                    //$nif->setBoutique($nifReceived->getBoutique());
                    //$nif->setSecteur($nifReceived->getSecteur());
                    //$nif->setMarche($nifReceived->getMarche());
                    //$nif->setRue($nifReceived->getRue());
                    //$nif->setQuartier($dossierDemande->getQuartier());
                    $em->persist($nif);
                } else {
                    $nif = new Nif();
                    $nif->setDossierDemande($dossierDemande);
                    $nif->setNumeroIdentificationFiscale($nifReceived->getNumeroIdentificationFiscale());
                    // $nif->setDate(date("Y-m-d",$nifReceived->getDateImmatriculation()));
                    if ($nifReceived->getNumeroFormulaire()) {
                        $tab = explode("/", $nifReceived->getNumeroFormulaire());
                        if ($tab) {
                            $numFormulaire = $tab[0];
                            $index = strpos($nifReceived->getNumeroFormulaire(), "/");
                            $nif->setNumeroFormulaire($numFormulaire);
                            $numFormulaireBis = substr($nifReceived->getNumeroFormulaire(), $index);
                            $nif->setNumeroFormulaireBis($numFormulaireBis);
                        }
                    }

                    //$nif->setBoutique($nifReceived->getBoutique());
                    //$nif->setSecteur($nifReceived->getSecteur());
                    //$nif->setMarche($nifReceived->getMarche());
                    //$nif->setRue($nifReceived->getRue());
                    //$nif->setQuartier($dossierDemande->getQuartier());
                    $em->persist($nif);
                }

                $documentNif = $em->getRepository("BanquemondialeBundle:DocumentCollected")->findOneBy((array('dossierDemande' => $historique->getDossierDemande(), "pole" => $poleNif->getId())));
                //die(dump($documentNif));
                if ($documentNif) {
                    $documentNif->setDateDelivrance($dateTimeImmat);
                    $statutDelivre = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(2);
                    $documentNif->setStatutTraitement($statutDelivre);
                    $em->persist($documentNif);
                }
                $em->flush();
                //on copie le fichier dès que le document passe a delivré. a l'avenir penser a couper au lieu de la copie
                $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
                $cheminLocal = $chemin->getNom();
                $libelleFormulaire = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->findOneByPole($poleNif);
                $nifSynergui = "formulaire" . $historique->getDossierDemande()->getId() . "_" . $libelleFormulaire->getId() . ".pdf";
               // $sftp->get($nomFichier, $cheminLocal . $historique->getDossierDemande()->getId() . '\\' . $nifSynergui);
               $this->enregistrerNIF($dossierDemande->getId());
//fin

                $codeRetour = "DNI01";

                $reponse->setNumeroDossier($dossierDemande->getNumeroDossier());
                $reponse->setCode($codeRetour);
                $reponse->setDescription($reponseWSReprository->getDescriptionByCode($codeRetour));

                $historique->setCodeRetourDNI($codeRetour);
                $em->persist($historique);

                //$sftp->disconnect();//commenté en attendant le fichier
                $reponseJson = $serializer->serialize($reponse, 'json');
                return new Response($reponseJson);
            } else {
                $codeRetour = "APIP07";

                $reponse->setNumeroDossier($nifReceived->getNumeroDossier());
                $reponse->setCode($codeRetour);
                $reponse->setDescription($reponseWSReprository->getDescriptionByCode($codeRetour));
                $reponseJson = $serializer->serialize($reponse, 'json');
                return new Response($reponseJson);
            }
            if (false) {
                return $this->view($errors, Response::HTTP_BAD_REQUEST); //cas connection impossible
            }
        } else {
            $codeRetour = $nifReceived->getCode();
            $messages = $nifReceived->getMessages() ? $nifReceived->getMessages() : array();
            $reponse->setNumeroDossier($nifReceived->getNumeroDossier());
            $reponse->setCode($codeRetour);
            $reponse->setDescription(implode("_", $messages));
            $reponseJson = $serializer->serialize($reponse, 'json');
            
            $documentNif = $em->getRepository("BanquemondialeBundle:DocumentCollected")->findOneBy((array('dossierDemande' => $historique->getDossierDemande(), "pole" => $poleNif->getId())));               
            $statutEnModif= $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(3);          
            $documentNif->setStatutTraitement($statutEnModif);      
            $motif=($messages) ?$messages[0]:$codeRetour;
            $documentNif->setMotif($motif);
            $em->persist($documentNif);
            return new Response($reponseJson);
        }
//        //die($nifReceived);
//        return new Response($nifReceived, Response::HTTP_CREATED);
//        //return $response;
    }
    public function enregistrerNIF($idd) {

        //$user = $this->container->get('security.context')->getToken()->getUser();
         $em = $this->getDoctrine()->getManager();
         $pole = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("BNI");
        //$pole = $user->getPole();
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection        
        if ($pole) {
            $idPole = $pole->getId();
        }
        
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
        $cheminDownload = $chemin->getNom();
        $rccm = $em->getRepository('BanquemondialeBundle:Rccm')->findOneByDossierDemande($idd);

        $libelleFormulaire = $em->getRepository('BanquemondialeBundle:LibelleFormulaireDelivre')->findOneByPole($pole);

        $nif = $em->getRepository('BanquemondialeBundle:Nif')->findOneByDossierDemande($idd);

        $dateNif = new \DateTime();
        //setlocale(LC_TIME, 'fr_FR.UTF8','fr.UTF8','fr_FR.UTF-8','fr.UTF-8');
        $dateValiditeTemp = new \DateTime();
        $timestamp = $dateValiditeTemp->getTimestamp();
        $dateValidite = strftime('%d %B %Y', $timestamp);

        if ($nif) {
            $dateNif = $nif->getDate();
        }

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $gerant = $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande($dossierDemande->getId(), $langue->getId());

        $activitePrincipale = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getSecteurActivite(), 'langue' => 1));
        $activiteSecondaire = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire(), 'langue' => 1));
        $activiteSecondaire2 = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $dossierDemande->getActiviteSecondaire2(), 'langue' => 1));


        $html = $this->renderView('ParametrageBundle:ParameterPole:visualiserNIF.html.twig', array('idd' => $idd,
            'dd' => $dossierDemande, 'dateValidite' => $dateValidite, 'nif' => $nif,
            'rep' => $gerant[0], 'rccm' => $rccm, 'activitePrincipale' => $activitePrincipale,
            'activiteSecondaire' => $activiteSecondaire, 'activiteSecondaire2' => $activiteSecondaire2));
        $nomFichier = "formulaire" . $idd . "_" . $libelleFormulaire->getId() . ".pdf"; //cofifier après rccm
        $html2pdf = new \Html2Pdf_Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($cheminDownload . "\\" . $idd . "\\" . $nomFichier, 'F');

        $formDelivre = $em->getRepository('BanquemondialeBundle:FormulaireDelivre')->findOneBy(array('nomFichier' => $nomFichier, 'pole' => $pole, 'dossierDemande' => $dossierDemande));
        if (!$formDelivre) {
            $date = new \DateTime();
            $formulaireDelivre = new FormulaireDelivre();
            $formulaireDelivre->setPole($pole);
            $formulaireDelivre->setDossierDemande($dossierDemande);
            $formulaireDelivre->setDateCreation($date);
            $formulaireDelivre->setNomFichier($nomFichier);
            $formulaireDelivre->setLibelleFormulaireDelivre($libelleFormulaire);
            $em->persist($formulaireDelivre);
        } else {
            $date = new \DateTime();
            $formDelivre->setDateCreation($date);
            $em->persist($formDelivre);
        }
        $em->flush();
    }

}
