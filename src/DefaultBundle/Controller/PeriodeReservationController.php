<?php

namespace DefaultBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use DefaultBundle\Entity\PeriodeReservation;
use DefaultBundle\Form\PeriodeReservationType;

/**
 * PeriodeReservation controller.
 *
 * @Route("/{_locale}/periodereservation")
 */
class PeriodeReservationController extends Controller
{
    /**
     * Lists all PeriodeReservation entities.
     *
     * @Route("/", name="periodereservation_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        $resulta =null;
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }
        $this->get('monservices')->updatePerideReservation();
        $formj=$em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->getListFormeJuridiqueByLanque(1)->getQuery()->getResult();
        $periodeReservations = $em->getRepository('DefaultBundle:PeriodeReservation')->findBylocalAndFormeJurique(1);

        return $this->render('periodereservation/index.html.twig', array(
            'periodeReservations' => $periodeReservations,'langues' => $lgs,'formj'=>$formj
        ));
    }
    /**
     * Creates a new PeriodeReservation entity.
     *
     * @Route("/new", name="periodereservation_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $periodeReservation = new PeriodeReservation();
        $form = $this->createForm('DefaultBundle\Form\PeriodeReservationType', $periodeReservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($periodeReservation);
            $em->flush();

            return $this->redirectToRoute('periodereservation_show', array('id' => $periodeReservation->getId()));
        }

        return $this->render('periodereservation/new.html.twig', array(
            'periodeReservation' => $periodeReservation,
            'form' => $form->createView(),
        ));
    }
    /**
     * Finds and displays a PeriodeReservation entity.
     *
     * @Route("/{id}", name="periodereservation_show")
     * @Method("GET")
     */
    public function showAction(PeriodeReservation $periodeReservation)
    {
        $deleteForm = $this->createDeleteForm($periodeReservation);

        return $this->render('periodereservation/show.html.twig', array(
            'periodeReservation' => $periodeReservation,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing PeriodeReservation entity.
     *
     * @Route("/{id}/edit", name="periodereservation_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PeriodeReservation $periodeReservation)
    {
        $deleteForm = $this->createDeleteForm($periodeReservation);
        $editForm = $this->createForm('DefaultBundle\Form\PeriodeReservationType', $periodeReservation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($periodeReservation);
            $em->flush();

            return $this->redirectToRoute('periodereservation_edit', array('id' => $periodeReservation->getId()));
        }

        return $this->render('periodereservation/edit.html.twig', array(
            'periodeReservation' => $periodeReservation,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a PeriodeReservation entity.
     *
     * @Route("/{id}", name="periodereservation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PeriodeReservation $periodeReservation)
    {
//        $form = $this->createDeleteForm($periodeReservation);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($periodeReservation);
//            $em->flush();
//        }
        $form = $this->createDeleteForm($periodeReservation);
        $form->handleRequest($request);
        if ($this->isCsrfTokenValid('delete'.$periodeReservation->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($periodeReservation);
            $em->flush();
        }
        $mserror = "Operation effectuée avec success";
        $errorMessage = '<span style="color: #ffb069;font-weight: bold ;font-size: larger">' . $mserror . '</span>';
        $this->get('session')->getFlashBag()->add('success', $errorMessage);
        return $this->redirectToRoute('periodereservation_index');
    }
    /**
     * Creates a form to delete a PeriodeReservation entity.
     *
     * @param PeriodeReservation $periodeReservation The PeriodeReservation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PeriodeReservation $periodeReservation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('periodereservation_delete', array('id' => $periodeReservation->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
