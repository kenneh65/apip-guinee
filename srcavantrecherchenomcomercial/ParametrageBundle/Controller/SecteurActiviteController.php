<?php

namespace ParametrageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use BanquemondialeBundle\Entity\SecteurActivite as SecteurActivite;
use ParametrageBundle\Entity\SecteurActiviteTraduction;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Pays controller.
 *
 * @Route("/secteurs")
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class SecteurActiviteController extends Controller
{
    /**
     * @Route("/index",name="secteur_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

		$request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
		$idCodeLangue = $langue->getId();

        //$secteurs = $em->getRepository('BanquemondialeBundle:SecteurActivite')->findAll();
		$secteurs = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->getListSecteursAndTraduction($idCodeLangue);

		//die(dump($secteurs));
		
        return $this->render('ParametrageBundle:SecteurActivite:index.html.twig', array('secteurs'=>$secteurs));
		

    }

      /**
     * Creates a new Pays entity.
     *
     * @Route("/new/{idC}", name="secteur_new",defaults={"idC": 0})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
		$request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
		
        $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();
        $secteurs = $em->getRepository('BanquemondialeBundle:SecteurActivite')->findAll();
        $categoriesTraduction = $em->getRepository('ParametrageBundle:CategorieActiviteTraduction')->findByLangue($langue);
        $resultat = -1;
        $idC = $request->get('idC');
        if (isset($idC) && $idC != 0) {
            $categorieSelected=$em->getRepository('BanquemondialeBundle:CategorieActivite')->find($idC);
        }
        if ($request->getMethod() == 'POST') {
			
            if ($request->get('code_secteur') != '' && $request->get('categorie_activite') != '') {
				
                $secteur = new SecteurActivite();
                $code = $request->get('code_secteur');
                $categorie = $em->getRepository('BanquemondialeBundle:CategorieActivite')->find($request->get('categorie_activite'));
				
                if ($em->getRepository('BanquemondialeBundle:SecteurActivite')->count($code) == 0) {
                    $secteur->setCode($code);
					$secteur->setCategorieActivite($categorie);
					$secteur->setActif(true);

                    foreach ($langues as $langue) {
                        if ($request->get('libelle_' . $langue->getLibelle()) and $request->get('libelle_' . $langue->getLibelle()) != '') {
                            $secteurTraduction = new SecteurActiviteTraduction();
                            $secteurTraduction->setLibelle($request->get('libelle_' . $langue->getLibelle()));
                            $secteurTraduction->setSecteurActivite($secteur);
                            $secteurTraduction->setLangue($langue);
                            $em->persist($secteur);
                            $em->persist($secteurTraduction);
                            $em->flush();
                        }
                    }
                    $translated = $translated = $this->get('translator')->trans('message_ajout_secteur');
                    $this->get('session')->getFlashBag()->add('info', $translated);

                    return $this->redirectToRoute('secteur_index');
                }
                $resultat = -3;
            } else
                $resultat = -2;
        }


        return $this->render('ParametrageBundle:SecteurActivite:new.html.twig', array('idC'=>$idC,'langues' => $langues, 'categoriesTraduction' => $categoriesTraduction,'secteurs' => $secteurs, 'resultat' => $resultat));
    }
    /**
     * @Route("/show/{id}",name="secteur_show")
     */
    public function showAction(SecteurActivite $secteur)
    {
        $em = $this->getDoctrine()->getManager();
		$request = $this->get('request');
        $codLang = $request->getLocale();
		$langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
		 
		$categoriesTraduction = $em->getRepository('ParametrageBundle:CategorieActiviteTraduction')->findByLangue($langue);
		//die(dump($categoriesTraduction));
        $langueTraduit = $em->getRepository('BanquemondialeBundle:Langue')->getLanguesBySecteur($secteur);
        $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();

		$categorieDefinie = $secteur->getCategorieActivite();
		
        return $this->render('ParametrageBundle:SecteurActivite:show.html.twig', array('languesTraduit' => $langueTraduit,'langues'=>$langues,'secteur'=>$secteur, 'categoriesTraduction' => $categoriesTraduction,
		'categorieDefinie'=>$categorieDefinie) );
    }

      /**
     * Displays a form to edit an existing Pays entity.
     *
     * @Route("/{id}/edit", name="secteur_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PaysTraduction $pay) {
        $deleteForm = $this->createDeleteForm($pay);
        $editForm = $this->createForm('BanquemondialeBundle\Form\PaysTraductionType', $pay);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pay);
            $em->flush();

            return $this->redirectToRoute('pays_edit', array('id' => $pay->getId()));
        }

        return $this->render('ParametrageBundle:Parametrage:Pays/edit.html.twig', array(
                    'pay' => $pay,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a SecteurActivite entity.
     *
     * @Route("/delete/{id}", name="secteur_delete")
     * @Method("GET")
     */
    public function deleteAction(SecteurActivite $secteur) {
        $em = $this->getDoctrine()->getManager();
		
		$listeDossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findBySecteurActivite($secteur);
		//die(dump($listeDossier));
		if($listeDossier)
		{			
			$secteur->setActif(false);
			$em->flush();
			$translated =  $this->get('translator')->trans('secteur.desactivation_message');
		}
		
		else
		{
			$em->remove($secteur);
			$em->flush();
			$translated =  $this->get('translator')->trans('secteur.suppression_message');
		}

        
        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('secteur_index');
    }
	
	
	/**
     * Deletes a SecteurActivite entity.
     *
     * @Route("/activate/{id}", name="secteur_activate")
     * @Method("GET")
     */
    public function activateAction(SecteurActivite $secteur) {
        $em = $this->getDoctrine()->getManager();
				
		if(!$secteur->getActif())
		{
			$secteur->setActif(true);
			$em->flush();
			$translated =  $this->get('translator')->trans('secteur.activation_message');
		}

        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('secteur_index');
    }
	
	
	/**
     * @Route("/{id}/toggle",name="secteur_toggle")
     */
	public function toggleAction(SecteurActivite $secteur) {
        $em = $this->getDoctrine()->getManager();
				
		if(!$secteur->getActif())
		{
			$secteur->setActif(true);
			$em->flush();
			$translated =  $this->get('translator')->trans('activation_message');
		}
		else{
			$secteur->setActif(false);
			$em->flush();
			$translated =  $this->get('translator')->trans('desactivation_message');
		}

        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('secteur_index');
    }
	

    /**
     * Creates a form to delete a Pays entity.
     *
     * @param Pays $pay The Pays entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PaysTraduction $pay) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('pays_delete', array('id' => $pay->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * @Route("/add-traduction-langue", name="add_traduction_secteur")
     */
    public function addTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
             $secteur = $em->getRepository('BanquemondialeBundle:SecteurActivite')->find($request->get('id'));
          $libelle = $request->get('libelle');

            if (!$langue)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));

            if (!$secteur)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));

            if ($libelle == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));


            $secteurTraduction = new SecteurActiviteTraduction();
            $secteurTraduction->setLibelle($libelle);
            $secteurTraduction->setSecteurActivite($secteur);
            $secteurTraduction->setLangue($langue);


            $em->persist($secteur);
            $em->persist($secteurTraduction);
            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/delete-traduction-langue", name="delete_traduction_secteur")
     */
    public function deleteTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
            $secteur = $em->getRepository('BanquemondialeBundle:SecteurActivite')->find($request->get('id'));
            $secteurTraduction = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->find($request->get('traduction'));


            if (!$secteurTraduction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'paysTraduction not null'));

            if (!$secteur)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'pays not null'));

            $em->remove($secteurTraduction);
            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'langue' => $secteurTraduction->getLangue()->getLibelle(),
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/update-traduction-langue", name="update_traduction_secteur")
     */
    public function updateTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
            $secteur = $em->getRepository('BanquemondialeBundle:SecteurActivite')->find($request->get('id'));
            $secteurTraduction = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->find($request->get('traduction'));

            $libelle = $request->get('libelle');

            if (!$langue)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'langue not null'));

            if (!$secteurTraduction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'secteurTraduction not null'));

            if (!$secteur)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'secteur not null'));

            if ($libelle == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'libelle not null'));

            $secteurTraduction->setLibelle($libelle);
            $secteurTraduction->setSecteurActivite($secteur);
            $secteurTraduction->setLangue($langue);

            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'langue' => $secteurTraduction->getLangue()->getLibelle(),
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/update-code", name="update_code_secteur")
     */
    public function updateCodeAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $code = $request->get('code');
			$categorieId = $request->get('categorieId');
			
            $em = $this->getDoctrine()->getManager();
            $secteur = $em->getRepository("BanquemondialeBundle:SecteurActivite")->find($id);
			$categorie = $em->getRepository('BanquemondialeBundle:CategorieActivite')->find($categorieId);

            $secteur->setCode($code);
			$secteur->setCategorieActivite($categorie);
            $em->persist($secteur);
            $em->flush();
            return new JsonResponse(array(
                'error' => '0'));
        } else
            return new JsonResponse(array(
                'error' => '1',
                'message' => 'Error'));
    }
	
	 /**
     * @Route("/verifier-suppression-secteur", name="verifier_suppression_secteur")
     */
    public function verifierSuppressionSecteurAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
			$secteur = $em->getRepository('BanquemondialeBundle:SecteurActivite')->find($request->get('id'));
			$listeDossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findBySecteurActivite($secteur);
			//die($id);
			//$listeDossier = null;
			
			if($listeDossier)
			{
				return new JsonResponse(array(
                'cas' => '0',
				'message' => 'desactiver'));
			}
			
		
            return new JsonResponse(array(
                'cas' => '1',
				'message' =>  'supprimer'));
        } else
            return new JsonResponse(array(
               
                'message' => 'Error'));
    }

}
