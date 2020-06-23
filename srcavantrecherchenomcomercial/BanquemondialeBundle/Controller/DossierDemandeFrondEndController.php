<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BanquemondialeBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BanquemondialeBundle\Form\RechercheType;
use BanquemondialeBundle\Form\Recherche1Type;
use BanquemondialeBundle\Form\Recherche2Type;
use BanquemondialeBundle\Repository\DossierdemandeRepository;
use BanquemondialeBundle\Entity\Dossierdemande;


class DossierDemandeFrondEndController  extends controller{
    //put your code here
    public function listerAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BanquemondialeBundle:Dossierdemande')->findAll();
     
        return $this->render('BanquemondialeBundle:Front:Dossierdemande/layout/index.html.twig', array(
            'entities' => $entities
        ));
        
    }
    public function rechercheAction() 
    {
        $form = $this->createForm(new RechercheType());
        return $this->render('BanquemondialeBundle:Front:Recherche/modulesUsed/recherche.html.twig', array('form' => $form->createView()));
    }
   
    public function rechercheTraitementAction(Request $request) 
    {
//        $form = $this->createForm(new RechercheType());
        
        if ($request->getMethod() == 'POST')
        {
//            $form->bind($this->get('request'));
            $formejuridique = $request->get('formejuridique');
            $denomination = $request->get('denomination');
            $em = $this->getDoctrine()->getManager();
            $entities = $em->getRepository('BanquemondialeBundle:Dossierdemande')->rechercheByCriteres($formejuridique,$denomination);
             
        } else {
            throw $this->createNotFoundException('La page n\'existe pas.');
        }
        
      return $this->render('BanquemondialeBundle:Front:Dossierdemande/layout/index.html.twig', array('entities' => $entities));
    }
    
    
    
}
