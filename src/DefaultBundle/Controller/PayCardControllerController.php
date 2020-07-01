<?php

namespace DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PayCardControllerController extends Controller
{
    /**
     * @Route("makePaiementPayCard")
     */
    public function makePaiementAction()
    {
        return $this->render('DefaultBundle:PayCardController:make_paiement.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("returning-after-paycard-paiement")
     */
    public function returningAction()
    {
        return $this->render('DefaultBundle:PayCardController:returning.html.twig', array(
            // ...
        ));
    }

}
