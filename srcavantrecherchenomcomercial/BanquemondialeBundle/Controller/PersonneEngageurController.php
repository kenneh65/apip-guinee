<?php

namespace BanquemondialeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BanquemondialeBundle\Entity\PersonneEngageur;
use BanquemondialeBundle\Entity\Langue;
use BanquemondialeBundle\Form\PersonneEngageurType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * PersonneEngageur controller.
 * 
 * @Route("/personneengageur")
 */
class PersonneEngageurController extends Controller {   //@Security("has_role('ROLE_ADMIN')")

    public function listerPersonneEngageurAction($id = null, $idd = null) {
        $message = '';
        //echo "<script>alert($maxItemPerPage)</script>";
        $idDossier = $idd;
        $em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');
        //$request->setLocale("en");
        $codLang = $request->getLocale();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();
		$definedPaysTraduction = NULL;
		$listePaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findByLangue($langue);
		

        $dossier = $em->find('BanquemondialeBundle:DossierDemande', $idd);

        if ($id != 0) {
            $message = $this->get('translator')->trans("message_aucun_element_trouve");
        }

        //$creationPersonneEngageur = $em->find('BanquemondialeBundle:PersonneEngageur', $id);
        $creationPersonneEngageur = $em->getRepository('BanquemondialeBundle:PersonneEngageur')->findOneBy(array('id' => $id, 'dossierDemande' => $idd));
		
        if ($creationPersonneEngageur) {
            $message = $this->get('translator')->trans("message_modification_en_cours");

			$definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $creationPersonneEngageur->getPays(), 'langue' => $langue));
			
			
            $form = $this->container->get('form.factory')->create(new PersonneEngageurType(), $creationPersonneEngageur, array('langue' => $langue, 'definedPaysTraduction' => $definedPaysTraduction));
            $request = $this->container->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {
                    $message = 'OK2';
                    if ($creationPersonneEngageur) {
						$creationPersonneEngageur->setNom(strtoupper($creationPersonneEngageur->getNom()));
						$creationPersonneEngageur->setPrenom(ucfirst($creationPersonneEngageur->getPrenom()));
                        $em->persist($creationPersonneEngageur);
                        $message = $this->get('translator')->trans("succes_modification");
                    } else {
						$creationPersonneEngageur->setNom(strtoupper($creationPersonneEngageur->getNom()));
						$creationPersonneEngageur->setPrenom(ucfirst($creationPersonneEngageur->getPrenom()));
                        $em->persist($creationPersonneEngageur);
                        $message = $this->get('translator')->trans("message_personne_engageur_ajouter_succes");
                    }
                    $em->flush();
                } else {
                    $message = $this->get('translator')->trans("message_formulaire_non_valide");
                }
            }
        } else {

            $creationPersonneEngageur = new PersonneEngageur();
            $codeLangue = $langue->getId();

            //echo "<script>alert($codeLangue)</script>";
            $form = $this->container->get('form.factory')->create(new PersonneEngageurType(), $creationPersonneEngageur, array('langue' => $langue, 'definedPaysTraduction' => $definedPaysTraduction));

            $request = $this->container->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid()) {
                    $em = $this->container->get('doctrine')->getManager();
					$creationPersonneEngageur->setNom(strtoupper($creationPersonneEngageur->getNom()));
					$creationPersonneEngageur->setPrenom(ucfirst($creationPersonneEngageur->getPrenom()));
                    $em->persist($creationPersonneEngageur);
                    $em->flush();
                    $message = $this->get('translator')->trans("message_personne_engageur_ajouter_succes");
                    $creationPersonneEngageur = new PersonneEngageur();
                    $form = $this->container->get('form.factory')->create(new PersonneEngageurType(), $creationPersonneEngageur, array('langue' => $langue, 'definedPaysTraduction' => $definedPaysTraduction));
                } else {
                    $message = $this->get('translator')->trans("message_formulaire_non_valide");
                }
            }
        }

        $em = $this->container->get('doctrine')->getManager();

        $listerPersonneEngageur = $em->getRepository('BanquemondialeBundle:PersonneEngageur')->findBy(array('dossierDemande' => $idd));

        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:PersonneEngageur/layout/personneEngageur.html.twig', array('form' => $form->createView(), 'message' => $message, 'listerPersonneEngageur' => $listerPersonneEngageur, 'idDossier' => $idDossier, 'personneEngageur' => $creationPersonneEngageur, 'id' => $id, 'idd' => $idd, 'dossier' => $dossier, 'listePaysTraduction'=>$listePaysTraduction));
    }

	
    public function supprimerPersonneEngageurAction($id) {
        $em = $this->container->get('doctrine')->getManager();
        $idDossier = 0;
        $personneEngageur = $em->find('BanquemondialeBundle:PersonneEngageur', $id);
        if (!$personneEngageur) {
            $message = $this->get('translator')->trans("message_aucun_element_trouve");
            throw new NotFoundHttpException($message);
        }
        $idDossier = $personneEngageur->getDossierDemande()->getId();
        //echo "<script>alert($idDossier;</script>";
        $em->remove($personneEngageur);
        $em->flush();
        return new RedirectResponse($this->container->get('router')->generate('personne_engageur_lister', array('id' => 0, 'idd' => $idDossier)));
    }
/*
    public function detailsPersonneEngageurAction(PersonneEngageur $personneEngageur) {
        $em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $personneEngageur->getPays(), 'langue' => $langue));

        $definedGenreTraduction = $em->getRepository('BanquemondialeBundle:GenreTraduction')->findOneBy(array('genre' => $personneEngageur->getGenre(), 'langue' => $langue));

        $definedSituationMatrimonialeTraduction = $em->getRepository('BanquemondialeBundle:SituationMatrimonialeTraduction')->findOneBy(array('situationMatrimoniale' => $representant->getSituationMatrimoniale(), 'langue' => $langue));

        $definedFonctionTraduction = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('fonction' => $representant->getFonction(), 'langue' => $langue));


        return $this->render('BanquemondialeBundle:Default:PersonneEngageur/layout/details.html.twig', array(
                    'representant' => $representant, 'definedPaysTraduction' => $definedPaysTraduction, 'definedGenreTraduction' => $definedGenreTraduction, 'definedSituationMatrimonialeTraduction' => $definedSituationMatrimonialeTraduction,
                    'definedFonctionTraduction' => $definedFonctionTraduction
        ));
    }

    public function conjointsPersonneEngageurAction($id = null, $idr = null) {
        $message = '';
        $idPersonneEngageur = $idr;

        $em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');

        $representant = $em->getRepository('BanquemondialeBundle:PersonneEngageur')->find($idr);
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
            $creationConjoint->setPersonneEngageur($representant);
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

        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:PersonneEngageur/layout/conjoints.html.twig', array('form' => $form->createView(), 'message' => $message, 'listerConjoint' => $listerConjoint, 'idPersonneEngageur' => $idPersonneEngageur, 'conjoint' => $creationConjoint, 'idDossier' => $idDossier));
    }

    public function supprimerConjointAction($id) {
        $em = $this->container->get('doctrine')->getManager();
        $idPersonneEngageur = 0;
        $conjoint = $em->find('BanquemondialeBundle:Conjoint', $id);
        if (!$conjoint) {
            $message = $this->get('translator')->trans("message_conjoint_non_trouve");
            throw new NotFoundHttpException($message);
        }
        $idPersonneEngageur = $conjoint->getPersonneEngageur()->getId();
        //echo "<script>alert($idDossier;</script>";
        $em->remove($conjoint);
        $em->flush();
        return new RedirectResponse($this->container->get('router')->generate('representant_conjoints', array('id' => 0, 'idr' => $idPersonneEngageur)));
    }
*/
    public function listerPersonneEngageurPoleAction(Request $request, $idd = null, $maxItemPerPage = 2) {
        $message = '';
        $idDossier = $idd;
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $creationdossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findOneBy(array('id' => $idd));
        $idFormeJ=$creationdossier->getFormeJuridique()->getId();
        $numeroDossier = $creationdossier->getNumeroDossier();
        $listerPersonneEngageur = $em->getRepository('BanquemondialeBundle:PersonneEngageur')->findBy(array('dossierDemande' => $idd));
        $listerPersonneEngageur = $this->get('knp_paginator')->paginate($listerPersonneEngageur, $this->getRequest('request')->query->get('page', 1), $maxItemPerPage);
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
        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:PersonneEngageur/layout/representantPole.html.twig', array('message' => $message, 'previous' => $routeAvant,
                    'listerPersonneEngageur' => $listerPersonneEngageur, 'idDossier' => $idDossier,
                    'id' => 0, 'idd' => $idd, 'fonctionTraduit' => $fonctionTraduit, 'rteSuivant' => $nextRte, 'routeAvant' => $routeAvant, 'numeroDossier' => $numeroDossier));
    }
/*
    public function detailsPersonneEngageurPoleAction(PersonneEngageur $representant) {
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

        return $this->render('BanquemondialeBundle:Default:PersonneEngageur/layout/detailspole.html.twig', array(
                    'representant' => $representant, 'definedPaysTraduction' => $definedPaysTraduction,
                    'definedGenreTraduction' => $definedGenreTraduction,
                    'definedSituationMatrimonialeTraduction' => $definedSituationMatrimonialeTraduction,
                    'definedFonctionTraduction' => $definedFonctionTraduction, 'listerConjoint' => $listerConjoint
        ));
    }
	*/

}
