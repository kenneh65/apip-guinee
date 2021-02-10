<?php

namespace DefaultBundle\Controller;

use DefaultBundle\Entity\DossierReponseJson;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DefaultBundle\Entity\ReponseJson;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\HttpFoundation\JsonResponse;
use function MongoDB\BSON\fromJSON;

class repEntWSController extends Controller
{
    /**
     * @Route("/ws/get-rep-enp",name="get-rep-enp")
     * @Method("POST")
     */
    public function getEntrpriseDataAction(Request $request)
    {
//        $heder=$request->getMethod();
//        die(dump($heder));
        $encoders = array(new JsonEncoder(new JsonEncode(JSON_UNESCAPED_UNICODE), new JsonDecode(false)));
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $em = $this->getDoctrine()->getManager();
        if ($this->isJSON($request->getContent()) == false) {
            $reponse = [
				"message" => "Bad data, check the JSON data format",
                "status" =>400
				];
            $reponseJson = $serializer->serialize($reponse, 'json');
            return new Response($reponseJson);
        }
        $data = $request->getContent();
        $codeRetour = 0;
        $dossierRecu = $serializer->deserialize($data, 'BanquemondialeBundle\Entity\DossierDemande', 'json');
        $reponseWSReprository = $em->getRepository('DefaultBundle:ReponseWS');
        $reponse = new DossierReponseJson();
        $dataRecu = json_decode($data);
        if (json_encode($dataRecu) == '{}') {
            $reponse = [
			"message" => "Request body cannot be empty.",
                "status" => -2000
                
				];
            $reponseJson = $serializer->serialize($reponse, 'json');
            return new Response($reponseJson);
        }
        if (!isset($dataRecu->numRccm)) {
            $reponse = [
			"message" => "The numRccm field is mandatory.",
                "status" => 0
                
				];
            $reponseJson = $serializer->serialize($reponse, 'json');
            return new Response($reponseJson);
        } elseif (!isset($dataRecu->password)) {
            $reponse = [
			 "message" => "Le champs password  est  aubligatoire",
                "status" => 0
               
				];
            $reponseJson = $serializer->serialize($reponse, 'json');
            return new Response($reponseJson);
        } elseif (!isset($dataRecu->username)) {
            $reponse = [
			 "message" => "Le champs username  est  aubligatoire",
                "status" => 0
               
				];
            $reponseJson = $serializer->serialize($reponse, 'json');
            return new Response($reponseJson);
        }
        $password = $dataRecu->password;
        $username = $dataRecu->username;
        $rccm = $dataRecu->numRccm;
        if ((!$password || !$username)) {
            $codeRetour = 0;
            $reponse->setStatus($codeRetour);
            $reponse->setMessages("Erreur d'authentification");
            $reponseJson = $serializer->serialize($reponse, 'json');
            return new Response($reponseJson);
        } else {
            if (!(strcmp($username, "APIP-USER") === 0 && strcmp($password, "APIP-USER-2020@") === 0)) {
                $codeRetour = 0;
				 $reponse->setMessages("Erreur d'authentification");
                $reponse->setNumeroDossier($dossierRecu->getNumeroDossier());
                $reponse->setStatus($codeRetour);
                $reponseJson = $serializer->serialize($reponse, 'json');
                return new Response($reponseJson);
            }
        }
        $histoDossier = $em->getRepository("BanquemondialeBundle:DossierDemande")->getRepEntre($rccm);
        if ($histoDossier) {
            $codeRetour = 1;
			$reponse->setMessages("Cette entreprise existe dans la base de données . Voici les informations");
            $reponse->setNumeroDossier($histoDossier[0]['numeroDossier']);
            $reponse->setStatus($codeRetour);
            $reponse->setDenominationCommercial($histoDossier[0]['denominationSociale']);
            $reponse->setNif($histoDossier[0]['nif']);
            $reponse->setRccm($histoDossier[0]['rccm']);
            $reponse->setDateCreation($histoDossier[0]['dateCreation']);
            $reponseJson = $serializer->serialize($reponse, 'json');
            return new Response($reponseJson);
        } else {
            $reponseJson = $serializer->serialize(
                [
                    "message" => "Cette entreprise n'existe pas dans la base de données  Synergui",
					  "status" => 0
                ], 'json');
            return new Response($reponseJson);
        }
    }

    function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}
