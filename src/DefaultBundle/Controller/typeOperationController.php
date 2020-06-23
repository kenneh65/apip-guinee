<?php

namespace DefaultBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use DefaultBundle\Entity\typeOperation;
use DefaultBundle\Form\typeOperationType;

/**
 * typeOperation controller.
 *
 *@Route("/{_locale}/typeoperationEncours")
 */
class typeOperationController extends Controller
{
    /**
     * Lists all typeOperation entities.
     *
     * @Route("/", name="typeoperationEncours_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $typeOperations = $em->getRepository('DefaultBundle:typeOperation')->findAll();

        return $this->render('typeoperation/index.html.twig', array(
            'typeOperations' => $typeOperations,
        ));
    }

    /**
     * Creates a new typeOperation entity.
     *
     * @Route("/new", name="typeoperationEncours_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $typeOperation = new typeOperation();
        $form = $this->createForm('DefaultBundle\Form\typeOperationType', $typeOperation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($typeOperation);
            $em->flush();

            return $this->redirectToRoute('typeoperationEncours_show', array('id' => $typeOperation->getId()));
        }

        return $this->render('typeoperation/new.html.twig', array(
            'typeOperation' => $typeOperation,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a typeOperation entity.
     *
     * @Route("/{id}", name="typeoperationEncours_show")
     * @Method("GET")
     */
    public function showAction(typeOperation $typeOperation)
    {
        $deleteForm = $this->createDeleteForm($typeOperation);

        return $this->render('typeoperation/show.html.twig', array(
            'typeOperation' => $typeOperation,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing typeOperation entity.
     *
     * @Route("/{id}/edit", name="typeoperationEncours_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, typeOperation $typeOperation)
    {
        $deleteForm = $this->createDeleteForm($typeOperation);
        $editForm = $this->createForm('DefaultBundle\Form\typeOperationType', $typeOperation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($typeOperation);
            $em->flush();

            return $this->redirectToRoute('typeoperationEncours_edit', array('id' => $typeOperation->getId()));
        }

        return $this->render('typeoperation/edit.html.twig', array(
            'typeOperation' => $typeOperation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a typeOperation entity.
     *
     * @Route("/{id}", name="typeoperationEncours_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, typeOperation $typeOperation)
    {
        $form = $this->createDeleteForm($typeOperation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($typeOperation);
            $em->flush();
        }

        return $this->redirectToRoute('typeoperationEncours_index');
    }

    /**
     * Creates a form to delete a typeOperation entity.
     *
     * @param typeOperation $typeOperation The typeOperation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(typeOperation $typeOperation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('typeoperationEncours_delete', array('id' => $typeOperation->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
