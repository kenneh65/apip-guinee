<?php

namespace DefaultBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use DefaultBundle\Entity\ServiceJurique;

/**
 * ServiceJurique controller.
 *
 * @Route("/servicejurique")
 */
class ServiceJuriqueController extends Controller
{
        /**
     * Creates a new servicejurique entity.
     *
     * @Route("/verification-conformite-des-statuts-sarl-sarlu", name="verification-conformite-des-statuts-sarl-sarlu")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $serviceJurique = new Detailreservation();
        $form = $this->createForm('DefaultBundle\Form\ServiceJuriqueType', $serviceJurique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($serviceJurique);
            $em->flush();
            return $this->redirectToRoute('servicejurique_index', []);
        }

        return $this->render('detailreservation/new.html.twig', array(
            'detailreservation' => $detailreservation,
            'form' => $form->createView(),
        ));
    }
    /**
     * Lists all ServiceJurique entities.
     *
     * @Route("/", name="servicejurique_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $serviceJuriques = $em->getRepository('DefaultBundle:ServiceJurique')->findAll();

        return $this->render('servicejurique/index.html.twig', array(
            'serviceJuriques' => $serviceJuriques,
        ));
    }

    /**
     * Finds and displays a ServiceJurique entity.
     *
     * @Route("/{id}", name="servicejurique_show")
     * @Method("GET")
     */
    public function showAction(ServiceJurique $serviceJurique)
    {

        return $this->render('servicejurique/show.html.twig', array(
            'serviceJurique' => $serviceJurique,
        ));
    }
}
