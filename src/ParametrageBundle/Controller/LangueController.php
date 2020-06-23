<?php

namespace ParametrageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BanquemondialeBundle\Entity\Langue;
use BanquemondialeBundle\Form\LangueType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Langue controller.
 *
 * @Route("/langue") 
 *  @Security("has_role('ROLE_ADMIN')")
 */
class LangueController extends Controller
{
    /**
     * Lists all Langue entities.
     *
     * @Route("/", name="langue_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();

        return $this->render('ParametrageBundle:Parametrage:Langue/index.html.twig', array(
            'langues' => $langues,
        ));
    }

    /**
     * Creates a new Langue entity.
     *
     * @Route("/new", name="langue_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $langue = new Langue();
        $form = $this->createForm('BanquemondialeBundle\Form\LangueType', $langue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if($request->get('courante')){
                $langue->setCourante(true);
             $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();
                foreach($langues as $l)
                {
                    $l->setCourante(false);
                    $em->persist($l);
                }
            }
            else
                $langue->setCourante (false);
            $em->persist($langue);
            $em->flush();
              $translated =  $this->get('translator')->trans('langue.ajouter_succes');
       $this->get('session')->getFlashBag()->add('info', $translated);
      
            // return $this->redirectToRoute('langue_show', array('id' => $langue->getId()));
            return $this->redirectToRoute('langue_index');
        }

        return $this->render('ParametrageBundle:Parametrage:Langue/new.html.twig', array(
            'langue' => $langue,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Langue entity.
     *
     * @Route("/{id}", name="langue_show")
     * @Method("GET")
     */
    public function showAction(Langue $langue)
    {
        $deleteForm = $this->createDeleteForm($langue);

        return $this->render('ParametrageBundle:Parametrage:Langue/show.html.twig', array(
            'langue' => $langue,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Langue entity.
     *
     * @Route("/{id}/edit", name="langue_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Langue $langue)
    {
        $deleteForm = $this->createDeleteForm($langue);
        $editForm = $this->createForm('BanquemondialeBundle\Form\LangueType', $langue,array('select'=>$langue->getCode()));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
             if($request->get('courante')){
                $langue->setCourante(true);
                $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();
                foreach($langues as $l)
                {
                    $l->setCourante(false);
                    $em->persist($l);
                }
             }
            else
                $langue->setCourante (false);
            $em->persist($langue);
            $em->flush();
        $translated =  $translated = $this->get('translator')->trans('langue.modifier_succes');
       $this->get('session')->getFlashBag()->add('info', $translated);
      
           
            return $this->redirectToRoute('langue_index');
        }

        return $this->render('ParametrageBundle:Parametrage:Langue/edit.html.twig', array(
            'langue' => $langue,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Langue entity.
     *
     * @Route("/supprimer/{id}", name="langue_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Langue $langue)
    {
         try {
            $em->remove($langue);
            $em->flush();
            $translated = $this->get('translator')->trans('message_suppression_entity_succes');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $translated = $this->get('translator')->trans('message_suppression_entity_fail');
        }
     
		 $this->get('session')->getFlashBag()->add('info', $translated);
      
        return $this->redirectToRoute('langue_index');
    }
	
		

    /**
     * Creates a form to delete a Langue entity.
     *
     * @param Langue $langue The Langue entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Langue $langue)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('langue_delete', array('id' => $langue->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
