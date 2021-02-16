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
