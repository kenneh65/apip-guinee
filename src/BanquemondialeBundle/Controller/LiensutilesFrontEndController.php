<?php

namespace BanquemondialeBundle\Controller;

use BanquemondialeBundle\Entity\Liensutiles;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;




class LiensutilesFrontEndController extends Controller
{
    public function liensutilesFrontEndAction(\BanquemondialeBundle\Entity\Liensutiles $entity = null)
    {
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        
        
            $entity = $em->getRepository('BanquemondialeBundle:Liensutiles')->findAll();
        

       
          
        return $this->render('BanquemondialeBundle:Front:Liensutiles/layout/index.html.twig', array('entity' => $entity,
                                                                                                
                                                                                                 ));
    }
   
   
    
   
}

?>
