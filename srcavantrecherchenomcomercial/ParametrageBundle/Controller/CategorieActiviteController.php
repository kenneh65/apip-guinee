<?php

namespace ParametrageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use BanquemondialeBundle\Entity\CategorieActivite as CategorieActivite;
use ParametrageBundle\Entity\CategorieActiviteTraduction;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Pays controller.
 *
 * @Route("/categories")
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class CategorieActiviteController extends Controller
{
    /**
     * @Route("/index",name="categorie_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

		$request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
		$idCodeLangue = $langue->getId();

        //$categories = $em->getRepository('BanquemondialeBundle:CategorieActivite')->findAll();
		$categories = $em->getRepository('ParametrageBundle:CategorieActiviteTraduction')->getListCategoriesAndTraduction($idCodeLangue);

        return $this->render('ParametrageBundle:Parametrage:CategorieActivite/index.html.twig', array('categories'=>$categories));
		

    }

      /**
     * Creates a new Categorie entity.
     *
     * @Route("/new", name="categorie_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();
        $categories = $em->getRepository('BanquemondialeBundle:CategorieActivite')->findAll();
        $resultat = -1;
        if ($request->getMethod() == 'POST') {
            if ($request->get('code_categorie') != '') {

                $categorie = new CategorieActivite();
                $code = $request->get('code_categorie');
                if ($em->getRepository('BanquemondialeBundle:CategorieActivite')->count($code) == 0) {
                    $categorie->setCode($code);
					$categorie->setActif(true);

                    foreach ($langues as $langue) {
                        if ($request->get('libelle_' . $langue->getLibelle()) and $request->get('libelle_' . $langue->getLibelle()) != '') {
                            $categorieTraduction = new CategorieActiviteTraduction();
                            $categorieTraduction->setLibelle($request->get('libelle_' . $langue->getLibelle()));
                            $categorieTraduction->setCategorieActivite($categorie);
                            $categorieTraduction->setLangue($langue);
                            $em->persist($categorie);
                            $em->persist($categorieTraduction);
                            $em->flush();
                        }
                    }
                    $translated = $translated = $this->get('translator')->trans('succes_add');
                    $this->get('session')->getFlashBag()->add('info', $translated);

                    return $this->redirectToRoute('categorie_index');
                }
                $resultat = -3;
            } else
                $resultat = -2;
        }


        return $this->render('ParametrageBundle:Parametrage:CategorieActivite/new.html.twig', array('langues' => $langues, 'categories' => $categories, 'resultat' => $resultat));
    }
    /**
     * @Route("/{id}/show",name="categorie_show")
     */
    public function showAction(CategorieActivite $categorie)
    {
         $em = $this->getDoctrine()->getManager();
        $langueTraduit = $em->getRepository('BanquemondialeBundle:Langue')->getLanguesByCategorie($categorie);
        $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();

        return $this->render('ParametrageBundle:Parametrage:CategorieActivite/show.html.twig', array('languesTraduit' => $langueTraduit,'langues'=>$langues,'categorie'=>$categorie
            // ...
        ));
    }

      /**
     * Displays a form to edit an existing Categorie entity.
     *
     * @Route("/{id}/edit", name="categorie_edit")
     * @Method({"GET", "POST"})
     *
    public function editAction(Request $request, CategorieTraduction $categorie) {
        $deleteForm = $this->createDeleteForm($pay);
        $editForm = $this->createForm('BanquemondialeBundle\Form\CategorieTraductionType', $categorie);
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
	*/

    /**
     * Deletes a CategorieActivite entity.
     *
     * @Route("/delete/{id}", name="categorie_delete")
     * @Method("GET")
     *
    public function deleteAction(CategorieActivite $categorie) {
        $em = $this->getDoctrine()->getManager();
		
		$listeDossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findByCategorieActivite($categorie);
		//die(dump($listeDossier));
		if($listeDossier)
		{			
			$categorie->setActif(false);
			$em->flush();
			$translated =  $this->get('translator')->trans('categorie.desactivation_message');
		}
		
		else
		{
			$em->remove($categorie);
			$em->flush();
			$translated =  $this->get('translator')->trans('categorie.suppression_message');
		}

        
        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('categorie_index');
    }
	*/
	
	/**
     * Deletes a CategorieActivite entity.
     *
     * @Route("/activate/{id}", name="categorie_activate")
     * @Method("GET")
     */
    public function activateAction(CategorieActivite $categorie) {
        $em = $this->getDoctrine()->getManager();
				
		if(!$categorie->getActif())
		{
			$categorie->setActif(true);
			$em->flush();
			$translated =  $this->get('translator')->trans('activation_message');
		}

        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('categorie_index');
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
                        ->setAction($this->generateUrl('categorie_delete', array('id' => $pay->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * @Route("/add-traduction-langue", name="add_traduction_categorie")
     */
    public function addTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
             $categorie = $em->getRepository('BanquemondialeBundle:CategorieActivite')->find($request->get('id'));
          $libelle = $request->get('libelle');

            if (!$langue)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));

            if (!$categorie)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));

            if ($libelle == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));


            $categorieTraduction = new CategorieActiviteTraduction();
            $categorieTraduction->setLibelle($libelle);
            $categorieTraduction->setCategorieActivite($categorie);
            $categorieTraduction->setLangue($langue);


            $em->persist($categorie);
            $em->persist($categorieTraduction);
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
     * @Route("/delete-traduction-langue", name="delete_traduction_categorie")
     */
    public function deleteTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
            $categorie = $em->getRepository('BanquemondialeBundle:CategorieActivite')->find($request->get('id'));
            $categorieTraduction = $em->getRepository('ParametrageBundle:CategorieActiviteTraduction')->find($request->get('traduction'));


            if (!$categorieTraduction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'categorieTraduction not null'));

            if (!$categorie)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'categorie not null'));

            $em->remove($categorieTraduction);
            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'langue' => $categorieTraduction->getLangue()->getLibelle(),
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/update-traduction-langue", name="update_traduction_categorie")
     */
    public function updateTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
            $categorie = $em->getRepository('BanquemondialeBundle:CategorieActivite')->find($request->get('id'));
            $categorieTraduction = $em->getRepository('ParametrageBundle:CategorieActiviteTraduction')->find($request->get('traduction'));

            $libelle = $request->get('libelle');

            if (!$langue)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'langue not null'));

            if (!$categorieTraduction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'categorieTraduction not null'));

            if (!$categorie)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'categorie not null'));

            if ($libelle == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'libelle not null'));

            $categorieTraduction->setLibelle($libelle);
            $categorieTraduction->setCategorieActivite($categorie);
            $categorieTraduction->setLangue($langue);

            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'langue' => $categorieTraduction->getLangue()->getLibelle(),
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/update-code", name="update_code_categorie")
     */
    public function updateCodeAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $code = $request->get('code');
            $em = $this->getDoctrine()->getManager();
            $categorie = $em->getRepository("BanquemondialeBundle:CategorieActivite")->find($id);

            $categorie->setCode($code);
            $em->persist($categorie);
            $em->flush();
            return new JsonResponse(array(
                'error' => '0'));
        } else
            return new JsonResponse(array(
                'error' => '1',
                'message' => 'Error'));
    }
	
	 /**
     * @Route("/verifier-suppression-categorie", name="verifier_suppression_categorie")
     */
    public function verifierSuppressionCategorieAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
			$categorie = $em->getRepository('BanquemondialeBundle:CategorieActivite')->find($request->get('id'));
			$listeDossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findByCategorieActivite($categorie);
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
	
	
	/**
     * @Route("/{id}/toggle",name="categorieActivite_toggle")
     */
    public function toggleAction(CategorieActivite $categorieActivite) {
        $em = $this->getDoctrine()->getManager();
				
		if(!$categorieActivite->isActif())
		{
			$categorieActivite->setActif(true);
			$em->flush();
			$translated =  $this->get('translator')->trans('activation_message');
		}
		else{
			$categorieActivite->setActif(false);
			$em->flush();
			$translated =  $this->get('translator')->trans('desactivation_message');
		}

		 //echo "<script>console.log( 'Debug Objects: ' );</script>";
		 
        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('categorie_index');
    }

}
