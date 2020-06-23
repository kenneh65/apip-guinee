<?php

namespace DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Writer_Excel2007;
use Doctrine\Common\Collections\ArrayCollection;

class ImportationController extends Controller {
    /**
     * @Route("/{_locale}/parametrage/importer-dossier",name="importer_dossier")
     */
    public function ImporterDossierAction(Request $request) {
	
		$em = $this->getDoctrine()->getManager();
		$request = $this->get('request');
		$session = $this->getRequest()->getSession();
		
		$dossierDemandes = new ArrayCollection();
		$representants = new ArrayCollection();
		$administrateurs = new ArrayCollection();
		$associes = new ArrayCollection();
		$commissaires = new ArrayCollection();
		$createurs = new ArrayCollection();
		
		
		$pays = null;	
		$decompte = null;
		$message = null;	
				  
		//$form = $this->createForm(new VillagesFilterType());

		if($request->getMethod() == 'POST')
		{

			$file = $this->get('kernel')->getRootDir().'/../web/import/listedossiers';
	    
			if (!file_exists($file)) {
				$message = "Erreur: fichier excel non trouv� !";
				
				$translated = $this->get('translator')->trans("fichier_excel_non_trouve");
				$this->get('session')->getFlashBag()->add('info', $translated);
				//exit("fichier non trouve" );
				return $this->render('OiseBundle:Default:importvillage.html.twig',array('message'=>$message,'villages'=>$villages, 'decompte' => $decompte, 'pays' => $pays, 'form' => $form->createView()) );
			
			}
	   
			else
			{
				$data = $request->request->all()['villages'];
				$pays = $em->getRepository('OiseBundle:Pays')->find( $data['pays']);				
				
				$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject($file);
				
				$rowIterator = $phpExcelObject->getActiveSheet()->getRowIterator();
				foreach($rowIterator as $row) {
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);
					$rowIndex = $row->getRowIndex ();
					if($rowIndex > 1)
					{
						$village = new Villages();
						foreach ($cellIterator as $cell) {
							
							if('A' == $cell->getColumn()) {						
								$array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();								
							} else if('B' == $cell->getColumn()) {
								$village->setNom($cell->getValue());
							} else if('C' == $cell->getColumn()) {
								$village->setCoordX($cell->getCalculatedValue());
							} else if('D' == $cell->getColumn()) {
								$village->setCoordY($cell->getCalculatedValue());
							} else if('E' == $cell->getColumn()) {
								$village->setCacId($cell->getCalculatedValue());
							} else if('F' == $cell->getColumn()) {
								$village->setUniteadminId($cell->getCalculatedValue());
							}					
						}
						$village->setPays($pays);
						//$villages[$rowIndex-2] = $village;					
						$villages->add($village);					
					}
				}
				//die(dump($data['cac']));
				$newerCriteria = Criteria::create();
				if($data['nom'])
				{
					$newerCriteria->andWhere(Criteria::expr()->eq("nom", $data['nom']));
				}
				if($data['coordXMin'])
				{
					$newerCriteria->andWhere(Criteria::expr()->gte("coordX", $data['coordXMin']));
				}
				if($data['coordXMax'])
				{
					$newerCriteria->andWhere(Criteria::expr()->lte("coordX", $data['coordXMax']));
				}
				if($data['coordYMin'])
				{
					$newerCriteria->andWhere(Criteria::expr()->gte("coordX", $data['coordYMin']));
				}
				if($data['coordYMax'])
				{
					$newerCriteria->andWhere(Criteria::expr()->lte("coordX", $data['coordYMax']));
				}
				
				$villages = $villages->matching($newerCriteria);

			}										
		}
                        
        else
		{
			die(dump('test'));
		}
       
    }
	/**
     * @Route("/{_locale}/parametrage/upload",name="upload_fichier")
     */
    public function uploadFichierAction()
    {
		$em = $this->getDoctrine()->getManager();
		$request = $this->get('request');		
		$message = null;
		//die(Dump($pays));		
		
		if ($request->getMethod() == 'POST') {		
			$nomFichier = "dossiers";
		
             if (isset($_FILES['fichierexcel']))
			 {
				if ($_FILES['fichierexcel']['type'] != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" && $_FILES['fichierexcel']['type'] != "application/vnd.ms-excel") 
				{					
					$translated = $this->get('translator')->trans("erreur_format_fichier_excel");
					$this->get('session')->getFlashBag()->add('info', $translated);
					
                    return $this->render('DefaultBundle:Default:importation.html.twig');
                }
				else
				{
					$cheminUpload = "../web/import";

					if (!is_dir($cheminUpload) && $_FILES['fichierexcel']['tmp_name']) {
						mkdir($cheminUpload);
						
					}
					
					$resultat = move_uploaded_file($_FILES['fichierexcel']['tmp_name'], $cheminUpload ."/".$nomFichier);
					
					if($resultat == 1)
					{
						$translated = $this->get('translator')->trans("file_uploaded");
						$this->get('session')->getFlashBag()->add('info', $translated);
						return $this->redirectToRoute('upload_fichier');
					}
					else
					{
						$translated = $this->get('translator')->trans("erreur_upload");
						$this->get('session')->getFlashBag()->add('info', $translated);
					}
					
				}
				
			 }
			 else
			 {
				$translated = $this->get('translator')->trans("aucun_fichier_excel_trouve");
				$this->get('session')->getFlashBag()->add('info', $translated);
			 }					
			
        }		
   
        return $this->render('DefaultBundle:Default:importation.html.twig');
    }

}
