<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace BanquemondialeBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of RegionController
 *
 * @author casa
 */
class DepartementFiltreController extends Controller {
    //put your code here
     public function departementfiltreAction(Request $request)
    {
        $id_region = $request->get('region');
        $em = $this->getDoctrine()->getManager();
        $region = $em->getRepository('BanquemondialeBundle:Region')->find($id_region);
        $departements= $em->getRepository('BanquemondialeBundle:Departement')->findByRegion($region);
        $deps = array();
        $i = 0;
        foreach ($departements as $dep) {
            $deps[$i++] = $dep->getDepartement();
        }
        return new JsonResponse($deps); 
                                          
    }
    
    public function regionAction()
    {
        $em = $this->getDoctrine()->getManager();
       $regions = $em->getRepository('BanquemondialeBundle:Region')->findAll();
       $departements=$em->getRepository('BanquemondialeBundle:Departement')->findAll();
        return $this->render('BanquemondialeBundle:Default:DemandeCreation/layout/regionfiltre.html.twig',array('regions'=>$regions,'departements'=>$departements));
        
    }
}
