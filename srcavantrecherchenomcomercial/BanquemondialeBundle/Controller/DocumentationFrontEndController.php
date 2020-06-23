<?php

namespace BanquemondialeBundle\Controller;

use BanquemondialeBundle\Entity\Documentation;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;




class DocumentationFrontEndController extends Controller
{
    public function AffichedocumentationFrontEndAction(Documentation $entity = null)
    {
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        
        
            $entity = $em->getRepository('BanquemondialeBundle:Documentation')->findAll();
        
 $entity = $this->get('knp_paginator')->paginate($entity,$this->getRequest('request')->query->get('page', 1), 3);

       
          
        return $this->render('BanquemondialeBundle:Front:Documentation/layout/index.html.twig', array('entity' => $entity,
                                                                                                
                                                                                                 ));
    }
   
   
    
   
}

?>
