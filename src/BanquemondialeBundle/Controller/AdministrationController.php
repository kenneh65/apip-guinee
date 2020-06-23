<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BanquemondialeBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use BanquemondialeBundle\Entity\Administration;
use BanquemondialeBundle\Form\AdministrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
/**
 * Description of AdministrationController
 *
 * @author DELL
 */
class AdministrationController extends Controller {
    //put your code here
    
    public function listerAdminAction($id = null, $idd = null) {
        $message='';
        $idDossier=$idd;
        if ((isset($id)&&($id!=0)))  {  // modification d'un acteur existant : on recherche ses données
        $message='Modification en cours.';
        $em = $this->container->get('doctrine')->getManager();
        $creationAdmin = $em->find('BanquemondialeBundle:Administration', $id);
        if (!$creationAdmin)  {   $message='Aucune Administration trouvée.';  }
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $definedFonctionTraduction =$em->getRepository("BanquemondialeBundle:FonctionTraduction")->getFonctionTraduction($creationAdmin->getFonction(),$langue->getId());
        $form = $this->container->get('form.factory')->create(new AdministrationType(array('langue' => $langue)), $creationAdmin, array('definedFonctionTraduction' =>$definedFonctionTraduction));
        $request = $this->container->get('request');       
        if ($request->getMethod() == 'POST')  {            
            $form->bind($request);
            //if ($form->isValid())  {
                //$message= 'OK2';
                $doc = $em->find('BanquemondialeBundle:DossierDemande', $idd);
                $creationAdmin->setDossierDemande($doc);
                $em->persist($creationAdmin);  $em->flush();  if ((isset($id))&&($id!=0))
                {   $message=  $this->get('translator')->trans('administration_modifiee');  }  else
                {   $message=  $this->get('translator')->trans('administration_ajoutée');   }                
               // }
                }       
            }
        else  {
        $creationAdmin = new Administration();
        //$creationAdmin.idDossierDemande = $id;
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $em = $this->container->get('doctrine')->getManager();                
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);        
        $form = $this->container->get('form.factory')->create(new AdministrationType(array('langue' => $langue)), $creationAdmin);
        $request = $this->container->get('request');
        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $doc = $em->find('BanquemondialeBundle:DossierDemande', $idd);
                $creationAdmin->setDossierDemande($doc);
                $em->persist($creationAdmin);
                $em->flush();
                $message=  $this->get('translator')->trans('administration_ajoutée');
                $creationAdmin = new Administration();
                $form = $this->container->get('form.factory')->create(new AdministrationType(array('langue' => $langue)), $creationAdmin);
            }
        }        
        }
         
        $em = $this->container->get('doctrine')->getManager();
        //$listeradmin= $em->getRepository('BanquemondialeBundle:Administration')->findAll();
        //$listeradmin= $em->getRepository('BanquemondialeBundle:Administration')->getListSearchAdministration($idDossier);
        $listeradmin= $em->getRepository('BanquemondialeBundle:Administration')->findByDossierDemande($idd);
        //$listeradmin= $em->getRepository('BanquemondialeBundle:Administration')->search($idDossier);
        // $query = $this->getDoctrine()->getRepository('AcmeDemoBundle:Pony')->search($form->getData());
        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:Administration/layout/administrateur.html.twig',  
                                    array(  'form' => $form->createView(),    
                                        'message' => $message, 
                                        'listeradmin' => $listeradmin, 
                                        'idDossier' => $idd,
                                        'admin' => $creationAdmin));
    }
    
    public function supprimerAction($id)
    {
        $em = $this->container->get('doctrine')->getManager();
        $admin = $em->find('BanquemondialeBundle:Administration', $id);
        if (!$admin)
        {
            throw new NotFoundHttpException("Administration non trouvé");
        }
        $em->remove($admin);
        $em->flush();
        return new RedirectResponse($this->container->get('router')->generate('Administration_listeradmin', array('id' =>0)));
    }
	
	 public function ListeAdministrationDossierAction($idd) 
    {
      $em = $this->getDoctrine()->getManager();
      $request = $this->get('request');
      $codLang = $request->getLocale();
      $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
      $entities = $em->getRepository('BanquemondialeBundle:Administration')->rechercheByDossierDemande($idd);
      $fonctionTraduit = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findByLangue($langue->getId());
      return $this->render('BanquemondialeBundle:Default:Administration/layout/listeAdministration.html.twig', 
              array('listeradmin' => $entities, 
                  'fonctionTraduit' => $fonctionTraduit));
    }
}
