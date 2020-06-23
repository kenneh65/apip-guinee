<?php

namespace BanquemondialeBundle\Controller;

use BanquemondialeBundle\Entity\Conjoint;
use BanquemondialeBundle\Entity\Representant;
use BanquemondialeBundle\Form\ConjointType;
use BanquemondialeBundle\Form\RepresentantType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Representant controller.
 * 
 * @Route("/representant")
 */
class RepresentantController extends Controller {   //@Security("has_role('ROLE_ADMIN')")

//    public function listerRepresentantAction($id = null, $idd = null) {
//        $message = '';
//        //echo "<script>alert($maxItemPerPage)</script>";
//
//        $idDossier = $idd;
//		$personneAssujettie = null;
//		$user = $this->container->get('security.context')->getToken()->getUser();
//		$profilName="";
//		if ($user->getProfile()) {
//            $profil = $user->getProfile()->getDescription();
//			$profilName=$profil;
//		}
//        $em = $this->container->get('doctrine')->getManager();
//        $request = $this->get('request');
//        //$request->setLocale("en");
//        $codLang = $request->getLocale();
//
//        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
//        $idCodeLangue = $langue->getId();
//        $fonctionTraduit = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findByLangue($langue->getId());
//
//        $dossier = $em->find('BanquemondialeBundle:DossierDemande', $idd);
//
//		$numeroDossier = $dossier->getNumeroDossier();
//        $isAguipe = true;
//        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findBySigle("AGUIPE");
//        $docAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
//        if ($docAguipe) {
//            $isAguipe = true;
//        } else {
//            $isAguipe = false;
//        }
//        $formeJuridiqueId = $dossier->getFormeJuridique()->getId();
//
//
//		if(strtoupper($dossier->getFormeJuridique()->getSigle()) == 'EI')
//		{
//			$personneAssujettie = $em->getRepository('BanquemondialeBundle:Representant')->findOneByDossierDemande($idd);
//			if($id == 0 && $personneAssujettie != null)
//			{
//				return $this->redirectToRoute('representant_listerrepresentant', array('id'=>$personneAssujettie->getId(), 'idd' => $idd));
//			}
//		}
//		//die(dump($personneAssujettie));
//
//
//        $definedPaysTraduction = NULL;
//        $definedGenreTraduction = NULL;
//        $definedSituationMatrimonialeTraduction = NULL;
//        $definedFonctionTraduction = NULL;
//
//
//        //$creationRepresentant = $em->find('BanquemondialeBundle:Representant', $id);
//        $creationRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findOneBy(array('id' => $id, 'dossierDemande' => $idd));
//        if ($creationRepresentant) {
//           // $message = $this->get('translator')->trans("message_modification_en_cours");
//
//            //$definedPaysTraduction =$em->getRepository("BanquemondialeBundle:PaysTraduction")->getPaysTraduction($creationRepresentant->getPays(),$langue->getId());
//
//            $definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $creationRepresentant->getPays(), 'langue' => $langue));
//            $definedGenreTraduction = $em->getRepository('BanquemondialeBundle:GenreTraduction')->findOneBy(array('genre' => $creationRepresentant->getGenre(), 'langue' => $langue));
//            $definedSituationMatrimonialeTraduction = $em->getRepository('BanquemondialeBundle:SituationMatrimonialeTraduction')->findOneBy(array('situationMatrimoniale' => $creationRepresentant->getSituationMatrimoniale(), 'langue' => $langue));
//            $definedFonctionTraduction = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('fonction' => $creationRepresentant->getFonction(), 'langue' => $langue));
//
//
//            $form = $this->container->get('form.factory')->create(new RepresentantType(), $creationRepresentant, array('definedPaysTraduction' => $definedPaysTraduction, 'definedSituationMatrimonialeTraduction' => $definedSituationMatrimonialeTraduction, 'definedGenreTraduction' => $definedGenreTraduction,
//                'definedFonctionTraduction' => $definedFonctionTraduction, 'fonctionTraduit' => $fonctionTraduit, 'langue' => $langue));
//            $request = $this->container->get('request');
//            if ($request->getMethod() == 'POST') {
//                $form->bind($request);
//                if ($form->isValid()) {
//                    $message = 'OK2';
//                    if ($creationRepresentant) {
//                        $em->persist($creationRepresentant);
//                        //$message = $this->get('translator')->trans("succes_modification");
//
//                    } else {
//
//                        $em->persist($creationRepresentant);
//                        //$message = $this->get('translator')->trans("message_representant_ajouter_succes");
//						$translated = $this->get('translator')->trans('message_representant_ajouter_succes');
//						$this->get('session')->getFlashBag()->add('info', $translated);
//                    }
//                    $em->flush();
//
//					if(strtoupper($dossier->getFormeJuridique()->getSigle()) == 'EI' && $creationRepresentant->getSituationMatrimoniale() && strtoupper($creationRepresentant->getSituationMatrimoniale()->getCode()) != 'M')
//					{
//						$rteSuivant = $this->getNextEtapeRoute($formeJuridiqueId, $isAguipe);
//						return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idd, 'numeroDossier' => $numeroDossier)));
//					}
//					else if(strtoupper($dossier->getFormeJuridique()->getSigle()) == 'EI' && $creationRepresentant->getSituationMatrimoniale() && strtoupper($creationRepresentant->getSituationMatrimoniale()->getCode()) == 'M')
//					{
//						$translated = $this->get('translator')->trans('succes_modification');
//						$this->get('session')->getFlashBag()->add('info', $translated);
//						return $this->redirectToRoute('representant_listerrepresentant', array('id'=>$creationRepresentant->getId(), 'idd' => $idd));
//					}
//					if(strtoupper($dossier->getFormeJuridique()->getSigle()) != 'EI')
//					{
//						$translated = $this->get('translator')->trans('succes_modification');
//						$this->get('session')->getFlashBag()->add('info', $translated);
//						return $this->redirectToRoute('representant_listerrepresentant', array('id'=>0, 'idd' => $idd));
//					}
//
//                } else {
//
//                    $message = $this->get('translator')->trans("message_formulaire_non_valide");
//                }
//            }
//        } else {
//
//            $creationRepresentant = new Representant();
//            $codeLangue = $langue->getId();
//
//
//            //echo "<script>alert($codeLangue)</script>";
//            $form = $this->container->get('form.factory')->create(new RepresentantType(), $creationRepresentant, array('definedPaysTraduction' => null, 'fonctionTraduit' => $fonctionTraduit, 'langue' => $langue));
//
//            $request = $this->container->get('request');
//            if ($request->getMethod() == 'POST') {
//                $form->bind($request);
//
//                if ($form->isValid()) {
//
//                    $em = $this->container->get('doctrine')->getManager();
//                    $em->persist($creationRepresentant);
//                    $em->flush();
//                    $message = $this->get('translator')->trans("message_representant_ajouter_succes");
//					//$translated = $this->get('translator')->trans('message_representant_ajouter_succes');
//					//$this->get('session')->getFlashBag()->add('info', $translated);
//
//					if(strtoupper($dossier->getFormeJuridique()->getSigle()) == 'EI' && $creationRepresentant->getSituationMatrimoniale() && strtoupper($creationRepresentant->getSituationMatrimoniale()->getCode()) != 'M')
//					{
//						$rteSuivant = $this->getNextEtapeRoute($formeJuridiqueId, $isAguipe);
//						return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idd, 'numeroDossier' => $numeroDossier)));
//					}
//					else if($creationRepresentant->getSituationMatrimoniale() && strtoupper($creationRepresentant->getSituationMatrimoniale()->getCode()) == 'M')
//					{
//						return $this->redirectToRoute('representant_listerrepresentant', array('id'=>$creationRepresentant->getId(), 'idd' => $idd));
//					}
//					$creationRepresentant = new Representant();
//                    $form = $this->container->get('form.factory')->create(new RepresentantType(), $creationRepresentant, array('fonctionTraduit' => $fonctionTraduit, 'langue' => $langue));
//
//
//				} else {
//					//die(dump($form->getErrorsAsString()));
//                    $message = $this->get('translator')->trans("message_formulaire_non_valide");
//                }
//            }
//        }
//
//        $em = $this->container->get('doctrine')->getManager();
//        //$listerRepresentant= $em->getRepository('BanquemondialeBundle:Representant')->findAll();
//        //$listerRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findBy(array('dossierDemande' => $idd));
//        //$dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
//        //$listerRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findByDossierDemande($idd);
//		$listerRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findListeRepresentantByDossierDemande($idd, $langue);
//
//        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:Representant/layout/representant.html.twig', array('form' => $form->createView(), 'message' => $message,
//		'listerRepresentant' => $listerRepresentant, 'idDossier' => $idDossier, 'representant' => $creationRepresentant, 'profilName' => $profilName,
//		'id' => $id, 'idd' => $idd, 'fonctionTraduit' => $fonctionTraduit, 'dossier' => $dossier, 'personneAssujettie' => $personneAssujettie));
//    }


    public function listerRepresentantAction($id = null, $idd = null)
    {

        $message = '';
        //echo "<script>alert($maxItemPerPage)</script>";

        $idDossier = $idd;
        $personneAssujettie = null;
        $user = $this->container->get('security.context')->getToken()->getUser();
        $profilName = "";
        if ($user->getProfile()) {
            $profil = $user->getProfile()->getDescription();
            $profilName = $profil;
        }
        $em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();
        $fonctionTraduit = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findByLangue($langue->getId());

        $dossier = $em->find('BanquemondialeBundle:DossierDemande', $idd);

        $numeroDossier = $dossier->getNumeroDossier();
        $isAguipe = true;
        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findBySigle("AGUIPE");
        $docAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
        if ($docAguipe) {
            $isAguipe = true;
        } else {
            $isAguipe = false;
        }
        $formeJuridiqueId = $dossier->getFormeJuridique()->getId();


        if (strtoupper($dossier->getFormeJuridique()->getSigle()) == 'EI') {
            $personneAssujettie = $em->getRepository('BanquemondialeBundle:Representant')->findOneByDossierDemande($idd);
            if ($id == 0 && $personneAssujettie != null) {
                return $this->redirectToRoute('representant_listerrepresentant', array('id' => $personneAssujettie->getId(), 'idd' => $idd));
            }
        }
        //die(dump($personneAssujettie));


        $definedPaysTraduction = NULL;
        $definedGenreTraduction = NULL;
        $definedSituationMatrimonialeTraduction = NULL;
        $definedFonctionTraduction = NULL;


        //$creationRepresentant = $em->find('BanquemondialeBundle:Representant', $id);
        $creationRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findOneBy(array('id' => $id, 'dossierDemande' => $idd));
        if ($creationRepresentant) {
            // $message = $this->get('translator')->trans("message_modification_en_cours");

            //$definedPaysTraduction =$em->getRepository("BanquemondialeBundle:PaysTraduction")->getPaysTraduction($creationRepresentant->getPays(),$langue->getId());

            $definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $creationRepresentant->getPays(), 'langue' => $langue));
            $definedGenreTraduction = $em->getRepository('BanquemondialeBundle:GenreTraduction')->findOneBy(array('genre' => $creationRepresentant->getGenre(), 'langue' => $langue));
            $definedSituationMatrimonialeTraduction = $em->getRepository('BanquemondialeBundle:SituationMatrimonialeTraduction')->findOneBy(array('situationMatrimoniale' => $creationRepresentant->getSituationMatrimoniale(), 'langue' => $langue));
            $definedFonctionTraduction = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('fonction' => $creationRepresentant->getFonction(), 'langue' => $langue));


            $form = $this->container->get('form.factory')->create(new RepresentantType(), $creationRepresentant, array('definedPaysTraduction' => $definedPaysTraduction, 'definedSituationMatrimonialeTraduction' => $definedSituationMatrimonialeTraduction, 'definedGenreTraduction' => $definedGenreTraduction,
                'definedFonctionTraduction' => $definedFonctionTraduction, 'fonctionTraduit' => $fonctionTraduit, 'langue' => $langue));
            $request = $this->container->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {
                    $message = 'OK2';
                    if ($creationRepresentant) {
                        $em->persist($creationRepresentant);
                        //$message = $this->get('translator')->trans("succes_modification");

                    } else {

                        $em->persist($creationRepresentant);
                        //$message = $this->get('translator')->trans("message_representant_ajouter_succes");
                        $translated = $this->get('translator')->trans('message_representant_ajouter_succes');
                        $this->get('session')->getFlashBag()->add('info', $translated);
                    }
                    $em->flush();

                    if (strtoupper($dossier->getFormeJuridique()->getSigle()) == 'EI' && $creationRepresentant->getSituationMatrimoniale() && strtoupper($creationRepresentant->getSituationMatrimoniale()->getCode()) != 'M') {
                        $rteSuivant = $this->getNextEtapeRoute($formeJuridiqueId, $isAguipe);
                        return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idd, 'numeroDossier' => $numeroDossier)));
                    } else if (strtoupper($dossier->getFormeJuridique()->getSigle()) == 'EI' && $creationRepresentant->getSituationMatrimoniale() && strtoupper($creationRepresentant->getSituationMatrimoniale()->getCode()) == 'M') {
                        $translated = $this->get('translator')->trans('succes_modification');
                        $this->get('session')->getFlashBag()->add('info', $translated);
                        return $this->redirectToRoute('representant_listerrepresentant', array('id' => $creationRepresentant->getId(), 'idd' => $idd));
                    }
                    if (strtoupper($dossier->getFormeJuridique()->getSigle()) != 'EI') {
                        $translated = $this->get('translator')->trans('succes_modification');
                        $this->get('session')->getFlashBag()->add('info', $translated);
                        return $this->redirectToRoute('representant_listerrepresentant', array('id' => 0, 'idd' => $idd));
                    }

                } else {

                    $message = $this->get('translator')->trans("message_formulaire_non_valide");
                }
            }
        } else {

            $creationRepresentant = new Representant();
            $codeLangue = $langue->getId();


            //echo "<script>alert($codeLangue)</script>";
            $form = $this->container->get('form.factory')->create(new RepresentantType(), $creationRepresentant, array('definedPaysTraduction' => null, 'fonctionTraduit' => $fonctionTraduit, 'langue' => $langue));
            $request = $this->container->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid()) {
                    $nombreGerant = $em->getRepository('BanquemondialeBundle:Representant')->findListeRepresentantByDossierDemande($idd, $langue);
                    if (empty($nombreGerant)) {
                        $gp = true;
                    } else {
                        $gp = false;
                    }
                    //   var_dump(($gp));die();
                    if ($gp == true) {
                        //// Envoi du SMS////////////////
                        $this->get('monServices')->SmsOrange($this->get('monServices')->formatPhoneNumber($creationRepresentant->getDossierDemande()->getTelephone()), $creationRepresentant, 'depot');
                        //// Envoi de l'Email ////////////////
                        $this->get('monServices')->EnvoiMessage($creationRepresentant, $creationRepresentant->getDossierDemande()->getEmail(), 'depot');
                    }
                    // var_dump($creationRepresentant->getDossierDemande()->getEmail());die();
                    $em = $this->container->get('doctrine')->getManager();
                    $creationRepresentant->setGp($gp);
                    $em->persist($creationRepresentant);

                    $em->flush();
                    $message = $this->get('translator')->trans("message_representant_ajouter_succes");
                    //$translated = $this->get('translator')->trans('message_representant_ajouter_succes');
                    //$this->get('session')->getFlashBag()->add('info', $translated);

                    if (strtoupper($dossier->getFormeJuridique()->getSigle()) == 'EI' && $creationRepresentant->getSituationMatrimoniale() && strtoupper($creationRepresentant->getSituationMatrimoniale()->getCode()) != 'M') {
                        $rteSuivant = $this->getNextEtapeRoute($formeJuridiqueId, $isAguipe);
                        return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idd, 'numeroDossier' => $numeroDossier)));
                    } else if ($creationRepresentant->getSituationMatrimoniale() && strtoupper($creationRepresentant->getSituationMatrimoniale()->getCode()) == 'M') {
                        return $this->redirectToRoute('representant_listerrepresentant', array('id' => $creationRepresentant->getId(), 'idd' => $idd));
                    }
                    $creationRepresentant = new Representant();
                    $form = $this->container->get('form.factory')->create(new RepresentantType(), $creationRepresentant, array('fonctionTraduit' => $fonctionTraduit, 'langue' => $langue));


                } else {
                    //die(dump($form->getErrorsAsString()));
                    $message = $this->get('translator')->trans("message_formulaire_non_valide");
                }
            }
        }

        $em = $this->container->get('doctrine')->getManager();
        //$listerRepresentant= $em->getRepository('BanquemondialeBundle:Representant')->findAll();
        //$listerRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findBy(array('dossierDemande' => $idd));
        //$dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        //$listerRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findByDossierDemande($idd);
        $listerRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findListeRepresentantByDossierDemande($idd, $langue);

        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:Representant/layout/representant.html.twig', array(
            'form' => $form->createView(),
            'message' => $message,
            'listerRepresentant' => $listerRepresentant,
            'idDossier' => $idDossier,
            'representant' => $creationRepresentant,
            'profilName' => $profilName,
            'id' => $id,
            'idd' => $idd,
            'fonctionTraduit' => $fonctionTraduit,
            'dossier' => $dossier,
            'personneAssujettie' => $personneAssujettie));
    }

    public function supprimerRepresentantAction($id) {
        $em = $this->container->get('doctrine')->getManager();
        $idDossier = 0;
        $representant = $em->find('BanquemondialeBundle:Representant', $id);
        if (!$representant) {
            //$message = $this->get('translator')->trans("message_representant_non_trouve");
			$translated = $this->get('translator')->trans('message_representant_non_trouve');
			$this->get('session')->getFlashBag()->add('info', $translated);
            throw new NotFoundHttpException($message);
        }
        $idDossier = $representant->getDossierDemande()->getId();
        //echo "<script>alert($idDossier;</script>";
		try{
			$em->remove($representant);
			$em->flush();
			$translated = $this->get('translator')->trans('message_representant_supprime');
			$this->get('session')->getFlashBag()->add('info', $translated);
		} catch (\Exception $e) {
			$translated = $this->get('translator')->trans('message_erreur_representant_suppression');
			$this->get('session')->getFlashBag()->add('error', $translated);
        }	
        return new RedirectResponse($this->container->get('router')->generate('representant_listerrepresentant', array('id' => 0, 'idd' => $idDossier)));
		

    }

    public function detailsRepresentantAction(Representant $representant) {
        $em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $representant->getPays(), 'langue' => $langue));

        $definedGenreTraduction = $em->getRepository('BanquemondialeBundle:GenreTraduction')->findOneBy(array('genre' => $representant->getGenre(), 'langue' => $langue));

        $definedSituationMatrimonialeTraduction = $em->getRepository('BanquemondialeBundle:SituationMatrimonialeTraduction')->findOneBy(array('situationMatrimoniale' => $representant->getSituationMatrimoniale(), 'langue' => $langue));

        $definedFonctionTraduction = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('fonction' => $representant->getFonction(), 'langue' => $langue));


        return $this->render('BanquemondialeBundle:Default:Representant/layout/details.html.twig', array(
                    'representant' => $representant, 'definedPaysTraduction' => $definedPaysTraduction, 'definedGenreTraduction' => $definedGenreTraduction, 'definedSituationMatrimonialeTraduction' => $definedSituationMatrimonialeTraduction,
                    'definedFonctionTraduction' => $definedFonctionTraduction
        ));
    }

    public function conjointsRepresentantAction($id = null, $idr = null) {
        $message = '';
        $idRepresentant = $idr;

        $em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');

        $representant = $em->getRepository('BanquemondialeBundle:Representant')->find($idr);
        $idDossier = $representant->getDossierDemande()->getId();
        if ((isset($id) && ($id != 0))) {  // modification d'un acteur existant : on recherche ses données
            $message = $this->get('translator')->trans("message_modification_en_cours");

            $creationConjoint = $em->find('BanquemondialeBundle:Conjoint', $id);


            //echo "<script>alert($definedPaysTraduction;</script>";
            if (!$creationConjoint) {
                $message = $this->get('translator')->trans("message_conjoint_non_trouve");
            }
            $form = $this->container->get('form.factory')->create(new conjointType(), $creationConjoint, array());
            $request = $this->container->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                //if ($form->isValid())  {
                $message = 'OK2';
                $em->persist($creationConjoint);
                $em->flush();
                if ((isset($id)) && ($id != 0)) {
                    $message = $message = $this->get('translator')->trans("message_conjoint_modifier_succes");
                } else {
                    $message = $message = $this->get('translator')->trans("message_conjoint_ajouter_succes");
                }
                // }
            }
        } else {
            $creationConjoint = new conjoint();
            $creationConjoint->setRepresentant($representant);
            //echo "<script>alert($codeLangue)</script>";
            $form = $this->container->get('form.factory')->create(new conjointType(), $creationConjoint, array());

            $request = $this->container->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {					
                    $em = $this->container->get('doctrine')->getManager();
                    $em->persist($creationConjoint);
                    $em->flush();
                    $message = $message = $this->get('translator')->trans("operation_succes");
                    $creationConjoint = new conjoint();
					$creationConjoint->setRepresentant($representant);
                    $form = $this->container->get('form.factory')->create(new conjointType(), $creationConjoint, array());
                }
            }
        }

        $em = $this->container->get('doctrine')->getManager();
        //$listerConjoint= $em->getRepository('BanquemondialeBundle:Conjoint')->findAll();
        $listerConjoint = $em->getRepository('BanquemondialeBundle:Conjoint')->findBy(array('representant' => $idr));
        //$listerConjoint = $representant.getConjoints();
        //$dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idr);
        //$listerconjoint = $em->getRepository('BanquemondialeBundle:conjoint')->findByDossierDemande($dossierDemande);

        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:Representant/layout/conjoints.html.twig', array('form' => $form->createView(), 'message' => $message, 'listerConjoint' => $listerConjoint, 'idRepresentant' => $idRepresentant, 'conjoint' => $creationConjoint, 'idDossier' => $idDossier));
    }

    public function supprimerConjointAction($id) {
        $em = $this->container->get('doctrine')->getManager();
        $idRepresentant = 0;
        $conjoint = $em->find('BanquemondialeBundle:Conjoint', $id);
        if (!$conjoint) {
            $message = $this->get('translator')->trans("message_conjoint_non_trouve");
            throw new NotFoundHttpException($message);
        }
        $idRepresentant = $conjoint->getRepresentant()->getId();
        //echo "<script>alert($idDossier;</script>";
        $em->remove($conjoint);
        $em->flush();
        return new RedirectResponse($this->container->get('router')->generate('representant_conjoints', array('id' => 0, 'idr' => $idRepresentant)));
    }

    public function listerRepresentantPoleAction(Request $request, $idd = null, $maxItemPerPage = 2) {
        $message = '';
        $idDossier = $idd;
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $creationdossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findOneBy(array('id' => $idd));
        $idFormeJ=$creationdossier->getFormeJuridique()->getId();
        $numeroDossier = $creationdossier->getNumeroDossier();
        $lisRepresenant= $em->getRepository('BanquemondialeBundle:Representant')->getRepresentantByDossierDemande( $idd,$langue->getId());        
        $representant=$lisRepresenant[0];
        $fonctionTraduit = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findByLangue($langue->getId());
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $nextRte = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextEtapePoleRoute($currentRoute,$idFormeJ);
        if (!$nextRte) {
            $nextRte = 'traiterDossier';
        }
        $routeAvant = $em->getRepository('ParametrageBundle:EtapeCreation')->getEtapePoleRouteBefore($currentRoute,$idFormeJ);
        try {
            $referer = $request->headers->get('referer');
            $path = substr($referer, strpos($referer, $request->getBaseUrl()));
            $path = str_replace($request->getBaseUrl(), '', $path);

            $matcher = $this->get('router')->getMatcher();
            $parameters = $matcher->match($path);
            $previous = $parameters['_route'];

            if ($previous != "representant_detailspole" and $routeAvant != $previous and $previous!=$nextRte) {
                $translated = $this->get('translator')->trans('suivre_etape_acces_page');
                $this->get('session')->getFlashBag()->add('error', $translated);

                return $this->redirectToRoute('administration');
            }
        } catch (\Exception $e) {
            $translated = $this->get('translator')->trans('suivre_etape_acces_page');
            $this->get('session')->getFlashBag()->add('error', $translated);
            return $this->redirectToRoute('administration');
        }
        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:Representant/layout/representantPole.html.twig', array('message' => $message, 'previous' => $routeAvant,
                    'representant' => $representant, 'idDossier' => $idDossier,
                    'id' => 0, 'idd' => $idd, 'fonctionTraduit' => $fonctionTraduit, 'rteSuivant' => $nextRte, 'routeAvant' => $routeAvant, 'numeroDossier' => $numeroDossier));
    }

    public function detailsRepresentantPoleAction(Representant $representant) {
        $idDossier = 11;
        $em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $representant->getPays(), 'langue' => $langue));

        $definedGenreTraduction = $em->getRepository('BanquemondialeBundle:GenreTraduction')->findOneBy(array('genre' => $representant->getGenre(), 'langue' => $langue));

        $definedSituationMatrimonialeTraduction = $em->getRepository('BanquemondialeBundle:SituationMatrimonialeTraduction')->findOneBy(array('situationMatrimoniale' => $representant->getSituationMatrimoniale(), 'langue' => $langue));

        $definedFonctionTraduction = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('fonction' => $representant->getFonction(), 'langue' => $langue));
        $listerConjoint = $em->getRepository('BanquemondialeBundle:Conjoint')->findBy(array('representant' => $representant->getId()));

        return $this->render('BanquemondialeBundle:Default:Representant/layout/detailspole.html.twig', array(
                    'representant' => $representant, 'definedPaysTraduction' => $definedPaysTraduction,
                    'definedGenreTraduction' => $definedGenreTraduction,
                    'definedSituationMatrimonialeTraduction' => $definedSituationMatrimonialeTraduction,
                    'definedFonctionTraduction' => $definedFonctionTraduction, 'listerConjoint' => $listerConjoint
        ));
    }
	
	
	
	 public function detailsConjointAction(Conjoint $conjoint) {
        $em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

 

        return $this->render('BanquemondialeBundle:Default:Representant/layout/detailsconjoint.html.twig', array('conjoint' => $conjoint) );
    }
	
	
	 public function getNextEtapeRoute($formeJuridique, $isAguipe) {
        $em = $this->getDoctrine()->getManager();
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $etape = $em->getRepository('ParametrageBundle:Fonctionnalite')->findOneBy(array('route' => $currentRoute));
        $etapeC = $em->getRepository('ParametrageBundle:EtapeCreation')->findOneBy(array('etape' => $etape->getId(), 'formeJuridique' => $formeJuridique));
        $ordre = 1;
        if ($etapeC) {
            $ordre = $etapeC->getOrdre();
        }
        $etapeSuivant = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextStepFormeJuridique($ordre, $formeJuridique, $isAguipe);
        $rteSuivant = null;
        if ($etapeSuivant) {
            $rteSuivant = $etapeSuivant->getEtape()->getRoute();
        }
        return $rteSuivant;
    }

}
