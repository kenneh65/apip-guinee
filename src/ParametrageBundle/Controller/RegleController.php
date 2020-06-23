<?php

namespace ParametrageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ParametrageBundle\Entity\Regle;
use ParametrageBundle\Form\RegleType;


/**
 * Regle controller.
 *
 * @Route("/regles")
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class RegleController extends Controller {

    /**
     * Lists all Regles.
     *
     * @Route("/", name="regles_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
				
        $regles = $em->getRepository('ParametrageBundle:Regle')->getListReglesByLanque($langue->getId());
        
        return $this->render('ParametrageBundle:Parametrage:Regle/index.html.twig', array('regles' => $regles));
    }
	
	
	/**
     * Creates a new Regle entity.
     *
     * @Route("/new", name="regle_new")
     * @Method({"GET", "POST"})
     */
    public function newAction() {
        $regle = new Regle();
		
		$em = $this->container->get('doctrine')->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
		
		
        $form = $this->createForm('ParametrageBundle\Form\RegleType', $regle, array('locale' => $codLang));
		$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($regle);
            $em->flush();
            $translated = $translated = $this->get('translator')->trans('succes_add');
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirectToRoute('regles_index');
            // return $this->redirectToRoute('regle_show', array('id' => $regle->getId()));
        }

        return $this->render('ParametrageBundle:Parametrage:Regle/new.html.twig', array(
                    'regle' => $regle,
                    'form' => $form->createView(),
        ));
    }
	
	/**
     * Displays a form to edit an existing Regle entity.
     *
     * @Route("/editerRegle/{id}", name="editer_regle")
     * @Method({"GET", "POST"})
     */
    public function editerAction(Request $request, Regle $regle) {
        
		
        $em = $this->getDoctrine()->getManager();
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
		
		$formJTraduit = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('formeJuridique'=>$regle->getFormeJuridique(),'langue'=>$langue));
		
		//die(dump($formJTraduit));
        $editForm = $this->createForm('ParametrageBundle\Form\RegleType', $regle, array('locale' => $request->getLocale(),'formeTraduit'=>$formJTraduit));
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em->persist($regle);
			
			//die(dump($regle));
            $em->flush();
            $translated = $translated = $this->get('translator')->trans('succes_modification');
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirectToRoute('regles_index');
        }

        return $this->render('ParametrageBundle:Parametrage:Regle/editer.html.twig', array(
                    'regle' => $regle,
                    'edit_form' => $editForm->createView(),                    
                    'formeTraduit' => $formJTraduit
        ));
    }
	
	
	
	
	 /**
     * Deletes a Regle entity.
     *
     * @Route("/supprimerRegle/{id}", name="regle_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $translated = "";
		$regle = $em->getRepository('ParametrageBundle:Regle')->find($id);
		//die(dump($regle));
        try {
            $em->remove($regle);
            $em->flush();
            $translated = $this->get('translator')->trans('message_suppression_entity_succes');
        } catch (\Exception $e) {
			die(dump($e->getMessage()));
            $translated = $this->get('translator')->trans('message_suppression_entity_fail');
        }



        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('regles_index');
    }


}
