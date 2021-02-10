<?php

namespace DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ExceptionController extends Controller
{
    /**
     * @Route("/showException")
     */
    public function showExceptionAction()
    {
        return $this->render('DefaultBundle:Exception:show_exception.html.twig', array(
            // ...
        ));
    }

}
