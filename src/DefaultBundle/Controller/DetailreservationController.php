<?php

namespace DefaultBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use DefaultBundle\Entity\Detailreservation;
use DefaultBundle\Form\DetailreservationType;

/**
 * Detailreservation controller.
 *
 * @Route("/detailreservation")
 */
class DetailreservationController extends Controller
{
    /**
     * Lists all Detailreservation entities.
     *
     * @Route("/", name="detailreservation_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $detailreservations = $em->getRepository('DefaultBundle:Detailreservation')->findAll();

        return $this->render('detailreservation/index.html.twig', array(
            'detailreservations' => $detailreservations,
        ));
    }

    /**
     * Creates a new Detailreservation entity.
     *
     * @Route("/new", name="detailreservation_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $detailreservation = new Detailreservation();
        $form = $this->createForm('DefaultBundle\Form\DetailreservationType', $detailreservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($detailreservation);
            $em->flush();

            return $this->redirectToRoute('detailreservation_show', array('id' => $detailreservation->getId()));
        }

        return $this->render('detailreservation/new.html.twig', array(
            'detailreservation' => $detailreservation,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Detailreservation entity.
     *
     * @Route("/{id}", name="detailreservation_show")
     * @Method("GET")
     */
    public function showAction(Detailreservation $detailreservation)
    {
        $deleteForm = $this->createDeleteForm($detailreservation);

        return $this->render('detailreservation/show.html.twig', array(
            'detailreservation' => $detailreservation,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Detailreservation entity.
     *
     * @Route("/{id}/edit", name="detailreservation_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Detailreservation $detailreservation)
    {
        $deleteForm = $this->createDeleteForm($detailreservation);
        $editForm = $this->createForm('DefaultBundle\Form\DetailreservationType', $detailreservation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($detailreservation);
            $em->flush();

            return $this->redirectToRoute('detailreservation_edit', array('id' => $detailreservation->getId()));
        }

        return $this->render('detailreservation/edit.html.twig', array(
            'detailreservation' => $detailreservation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Detailreservation entity.
     *
     * @Route("/{id}", name="detailreservation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Detailreservation $detailreservation)
    {
        $form = $this->createDeleteForm($detailreservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($detailreservation);
            $em->flush();
        }

        return $this->redirectToRoute('detailreservation_index');
    }

    /**
     * Creates a form to delete a Detailreservation entity.
     *
     * @param Detailreservation $detailreservation The Detailreservation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Detailreservation $detailreservation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('detailreservation_delete', array('id' => $detailreservation->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
