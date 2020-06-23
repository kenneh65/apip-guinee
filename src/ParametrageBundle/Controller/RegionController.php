<?php

namespace ParametrageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BanquemondialeBundle\Entity\Region;
use BanquemondialeBundle\Form\RegionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Region controller.
 *
 * @Route("/region")
 * @Security("has_role('ROLE_USER')")
 */
class RegionController extends Controller {

    /**
     * Lists all Region entities.
     *
     * @Route("/", name="region_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('BanquemondialeBundle:Region');
        //$regions = $repository->findByActif(TRUE);		
        $regions = $em->getRepository('BanquemondialeBundle:Region')->findAll();


        return $this->render('ParametrageBundle:Parametrage:Region/index.html.twig', array(
                    'regions' => $regions, 'repository' => $repository
        ));
    }

    /**
     * Creates a new Region entity.
     *
     * @Route("/new", name="region_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $region = new Region();
        $form = $this->createForm('BanquemondialeBundle\Form\RegionType', $region, array('locale' => $request->getLocale()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			
			$regionTemp = $em->getRepository('BanquemondialeBundle:region')->findBy(array('pays'=>$region->getPays(),'libelle'=>$region->getLibelle()));
			if(!$regionTemp)
			{
				//$pays=$em->getRepository('BanquemondialeBundle:Pays')->findByResidence(true);
				//$region->setPays($pays->getId());
				//die(dump($region));
				$em->persist($region);
				$em->flush();			
				$translated = $translated = $this->get('translator')->trans('region.message_ajouter');
				$this->get('session')->getFlashBag()->add('info', $translated);
			}
			else
			{
				$translated = $translated = $this->get('translator')->trans('message_combinaison_existe');
				$this->get('session')->getFlashBag()->add('info', $translated);
				
				 return $this->redirectToRoute('region_new');
			}


            return $this->redirectToRoute('region_index');
            // return $this->redirectToRoute('region_show', array('id' => $region->getId()));
        }

        return $this->render('ParametrageBundle:Parametrage:Region/new.html.twig', array(
                    'region' => $region,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Region entity.
     *
     * @Route("/{id}", name="region_show")
     * @Method("GET")
     */
    public function showAction(Region $region) {
        $deleteForm = $this->createDeleteForm($region);

        return $this->render('ParametrageBundle:Parametrage:Region/show.html.twig', array(
                    'region' => $region,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Region entity.
     *
     * @Route("/{id}/edit", name="region_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Region $region) {
        $deleteForm = $this->createDeleteForm($region);
        $em = $this->getDoctrine()->getManager();
        //$request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $paysTraduit = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('langue' => $langue, 'pays' => $region->getPays()->getId()));
        //die($paysTraduit);
        $editForm = $this->createForm(new RegionType(array('locale' => $request->getLocale(), 'paysTraduit' => $paysTraduit)), $region);
        $editForm->handleRequest($request);
        
        $pays = $em->getRepository('BanquemondialeBundle:PaysTraduction')->getListPays($request->getLocale())->getQuery()->getResult();
        if ($editForm->isSubmitted() && $editForm->isValid()) {
			
			$regionTemp = $em->getRepository('BanquemondialeBundle:region')->findBy(array('pays'=>$region->getPays(),'libelle'=>$region->getLibelle()));
			if(!$regionTemp)
			{
				$em->persist($region);
				$em->flush();
				$translated = $translated = $this->get('translator')->trans('succes_modification');
				$this->get('session')->getFlashBag()->add('info', $translated);
			}
			else
			{
				$translated = $translated = $this->get('translator')->trans('message_combinaison_existe');
				$this->get('session')->getFlashBag()->add('info', $translated);
				
				 return $this->redirectToRoute('region_edit' , array('id' => $region->getId()));
			}

            return $this->redirectToRoute('region_index');
        }

        return $this->render('ParametrageBundle:Parametrage:Region/edit.html.twig', array(
                    'region' => $region,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'pays' => $pays
        ));
    }

    /**
     * Deletes a Region entity.
     *
     * @Route("/supprimer/{id}", name="region_delete")
     * @Method("GET")
     */
    public function deleteAction(Region $region) {

        $em = $this->getDoctrine()->getManager();
		$translated = "";
		try{
			$em->remove($region);
			$em->flush();
			$translated = $this->get('translator')->trans('message_suppression_entity_succes');
		}
		catch(\Exception $e){
			error_log($e->getMessage());
			$translated = $this->get('translator')->trans('message_suppression_entity_fail');
		}


        $this->get('session')->getFlashBag()->add('info', $translated);
        return $this->redirectToRoute('region_index');
    }

    /**
     * Creates a form to delete a Region entity.
     *
     * @param Region $region The Region entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Region $region) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('region_delete', array('id' => $region->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * @Route("regions/actions/",name="action_regions")
     */
    public function actionUsersAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->get('id');
            if ($id != '') {
                $source = (string) $request->get('nom');

                if ($source != '') {
                    $region = new Region();

                    $pays = $em->getRepository('BanquemondialeBundle:Pays')->find($id);

                    if (!$pays)
                        return new JsonResponse(array(
                            'error' => '1',
                            'message' => 'Error'));

                    $region->setRegion($source);

                    $region->setPays($pays);

                    $em->persist($region);
                    $em->flush();

                    return new JsonResponse(array(
                        'error' => '0',
                        'regionID' => $region->getId(),
                        'regionLibelle' => $region->getLibelle(),
                        'message' => 'Done'));
                } else
                    return new JsonResponse(array(
                        'error' => '1',
                        'message' => 'id not null'));
            } else
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Errors'));
    }

    /**
     * @Route("regions/update/",name="update_region")
     */
    public function updateRegionAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->get('id');
            if ($id != '') {
                $source = (string) $request->get('nom');

                if ($source != '') {
                    $region = $em->getRepository('BanquemondialeBundle:Region')->find($id);

                    if (!$region)
                        return new JsonResponse(array(
                            'error' => '1',
                            'message' => 'Error'));

                    $region->setRegion($source);

                   
                    $em->persist($region);
                    $em->flush();

                    return new JsonResponse(array(
                        'error' => '0',
                        'regionID' => $region->getId(),
                        'regionLibelle' => $region->getLibelle(),
                        'message' => 'Done'));
                } else
                    return new JsonResponse(array(
                        'error' => '1',
                        'message' => 'id not null'));
            } else
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Errors'));
    }
	
	
	/**
     * @Route("/{id}/toggle",name="Region_toggle")
     */
    public function toggleAction(Region $region) {
        $em = $this->getDoctrine()->getManager();
				
		if(!$region->isActif())
		{
			$region->setActif(true);
			$em->flush();
			$translated =  $this->get('translator')->trans('activation_message');
		}
		else{
			$region->setActif(false);
			$em->flush();
			$translated =  $this->get('translator')->trans('desactivation_message');
		}

		 //echo "<script>console.log( 'Debug Objects: ' );</script>";
		 
        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('region_index');
    }

}
