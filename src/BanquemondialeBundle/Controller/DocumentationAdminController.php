<?php

namespace BanquemondialeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BanquemondialeBundle\Entity\Documentation;
use BanquemondialeBundle\Form\DocumentationType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use BanquemondialeBundle\Entity\DocumentationTraduction;

/**
* Produits controller.
*
*/
class DocumentationAdminController extends Controller {

	/**
	* Lists all Produits entities.
	*
	*/
	public function indexAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		//$taille = 3;
		$langue = $request->getLocale();
		$chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);

		if ($langue == 'fr')
		$entities = $em->getRepository('BanquemondialeBundle:Documentation')->findAll();
		else
		$entities = $em->getRepository('BanquemondialeBundle:DocumentationTraduction')->getAllDocumentations($langue);
		return $this->render('BanquemondialeBundle:Default:Documentation/layout/index.html.twig', array(
		'entities' => $entities,
		'chemin' => ucfirst($chemin->getNom() . 'Guides\\'),
		));
	}

	/**
	* Creates a new Produits entity.
	*
	*/
	public function createAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('BanquemondialeBundle:Langue');
		$langues = $repository->getOtherLanguages('fr');
		$langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode('fr');
		//Récupération du chemin vers le répertoire ou sera stocké les documents
		$chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);

		$documentation = new Documentation();
		$documentation->setUpdateAt(new \DateTime());
		foreach ($langues as $l) {
			$trad = new DocumentationTraduction();
			$trad->setLangue($l);
			$trad->setDocumentation($documentation);
			$documentation->addTraduction($trad);
		}
		$form = $this->createCreateForm($documentation);
		$form->handleRequest($request);
		$erreur_titre = '';
		$erreur_description = '';
		$erreur_fichier = '';
		if ($form->isValid()) {
			$i = 1;
			foreach ($documentation->getTraduction() as $traduction) {

				$titre = $request->get('titre' . $i);
				$description = $request->get('description' . $i);
				$fichier = $_FILES['fichier' . $i];
				$uploaddir = $chemin->getNom() . '\Guides\\';
				$uploadfile = $uploaddir . basename($titre . '_other.pdf');
				if ($titre == '') {
					$translated = $this->get('translator')->trans('titre_nulle');
					$erreur_titre = $translated;
				}
				if ($description == '') {
					$translated = $this->get('translator')->trans('description_nulle');
					$erreur_description = $translated;
				}
				if (!$fichier) {
					$translated = $this->get('translator')->trans('fichier_nulle');
					$erreur_fichier = $translated;
				}
				if ($erreur_titre != '' or $erreur_description != '' or $erreur_fichier != '') {
					$translated = $this->get('translator')->trans('documentation.add_error');
					$this->get('session')->getFlashBag()->add('error', $translated);
					return $this->render('BanquemondialeBundle:Default:Documentation/layout/new.html.twig', array(
					'form' => $form->createView(),
					'entity' => $documentation,
					'erreur_titre' => $erreur_titre,
					'erreur_description' => $erreur_description,
					'erreur_fichier' => $erreur_fichier,
					'langue' => $langue
					));
				}
				move_uploaded_file($_FILES['fichier' . $i]['tmp_name'], $uploadfile);
				$traduction->setTitre($titre);
				$traduction->setDescription($description);
				$i++;
			}
			$documentation->getFichier()->setCheminUpload($chemin->getNom());

			$documentation->getFichier()->setDocumentation($documentation);

			$em->persist($documentation);
			$em->flush();

			$translated = $translated = $this->get('translator')->trans('succes_add');
			$this->get('session')->getFlashBag()->add('info', $translated);

			return $this->redirect($this->generateUrl('adminDocumentation'));
		}

		return $this->render('BanquemondialeBundle:Default:Documentation/layout/new.html.twig', array(
		'form' => $form->createView(),
		'entity' => $documentation,
		'erreur_titre' => $erreur_titre,
		'erreur_description' => $erreur_description,
		'erreur_fichier' => $erreur_fichier,
		'langue' => $langue
		));
	}

	/**
	* Creates a form to create a Produits entity.
	*
	* @param Produits $entity The entity
	*
	* @return \Symfony\Component\Form\Form The form
	*/
	private function createCreateForm(Documentation $entity) {
		$form = $this->createForm(new DocumentationType(), $entity);


		return $form;
	}

	/**
	* Displays a form to create a new Produits entity.
	*
	*/
	public function newAction() {
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('BanquemondialeBundle:Langue');
		$langues = $repository->getOtherLanguages('fr');
		$langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode('fr');
		$documentation = new Documentation();

		foreach ($langues as $l) {
			$trad = new DocumentationTraduction();
			$trad->setLangue($l);
			$trad->setDocumentation($documentation);
			$documentation->addTraduction($trad);
		}
		$form = $this->createCreateForm($documentation);
		$erreur_titre = '';
		$erreur_description = '';
		$erreur_fichier = '';

		return $this->render('BanquemondialeBundle:Default:Documentation/layout/new.html.twig', array(
		'entity' => $documentation,
		'form' => $form->createView(),
		'erreur_titre' => $erreur_titre,
		'erreur_description' => $erreur_description,
		'erreur_fichier' => $erreur_fichier,
		'langue' => $langue
		));
	}

	/**
	* Finds and displays a Produits entity.
	*
	*/
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('BanquemondialeBundle:Documentation')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('document_entite_non_trouve');
		}

		$deleteForm = $this->createDeleteForm($id);

		return $this->render('BanquemondialeBundle:Default:Documentation/layout/show.html.twig', array(
		'entity' => $entity,
		'delete_form' => $deleteForm->createView(),
		));
	}

	/**
	* Displays a form to edit an existing Produits entity.
	*
	*/
	public function editAction(Documentation $entity) {
		$em = $this->getDoctrine()->getManager();

		$langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode('fr');
		$erreur_titre = '';
		$erreur_description = '';
		$erreur_fichier = '';

		if (!$entity) {
			throw $this->createNotFoundException('document_entite_non_trouve');
		}

		$editForm = $this->createEditForm($entity);
		$deleteForm = $this->createDeleteForm($entity->getId());

		return $this->render('BanquemondialeBundle:Default:Documentation/layout/edit.html.twig', array(
		'entity' => $entity,
		'edit_form' => $editForm->createView(),
		'delete_form' => $deleteForm->createView(),
		'langue' => $langue,
		'erreur_titre' => $erreur_titre,
		'erreur_description' => $erreur_description,
		'erreur_fichier' => $erreur_fichier,
		));
	}

	/**
	* Creates a form to edit a Produits entity.
	*
	* @param Produits $entity The entity
	*
	* @return \Symfony\Component\Form\Form The form
	*/
	private function createEditForm(Documentation $entity) {
		$form = $this->createForm(new DocumentationType(), $entity);


		return $form;
	}

	/**
	* Edits an existing Produits entity.
	*
	*/
	public function updateAction(Request $request, Documentation $documentation) {
		$em = $this->getDoctrine()->getManager();
		
		$chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);					

		$deleteForm = $this->createDeleteForm($documentation->getId());
		$editForm = $this->createEditForm($documentation);
		$editForm->handleRequest($request);
		$erreur_titre = '';
		$erreur_description = '';
		$erreur_fichier = '';
		//$documentation->getFichier()->setCheminUpload($chemin->getNom());
		//die(dump($documentation->getTitre()));
		
		//die(dump(substr($documentation->getTitre(), 1)));
		
		if($documentation->getTitre()[0] == ".")
		{			
			$documentation->setTitre(substr($documentation->getTitre(), 1));
		}
		else
		{
			$documentation->setTitre(".".$documentation->getTitre());
		}
		
		
		if ($editForm->isValid()) {
			$i = 1;
			
			
			foreach ($documentation->getTraduction() as $traduction) {
				
				$titre = $request->get('titre' . $i);
				$description = $request->get('description' . $i);
				$fichier = $_FILES['fichier' . $i];
				
				
				if ($titre == '') {
					$translated = $this->get('translator')->trans('titre_nulle');
					$erreur_titre = $translated;
				}
				if ($description == '') {
					$translated = $this->get('translator')->trans('description_nulle');
					$erreur_description = $translated;
				}
				$uploaddir = $chemin->getNom() . '\guides\\';
				
				
				if ($_FILES['fichier' . $i]['tmp_name']) {
						
					$uploadfile = $uploaddir . basename($titre . '_other.pdf');
					if (file_exists($uploaddir . "" . $traduction->getTitre() . '_other.pdf'))
					unlink($uploaddir . "" . $traduction->getTitre() . '_other.pdf');
					move_uploaded_file($_FILES['fichier' . $i]['tmp_name'], $uploadfile);
					
				}

				if ($erreur_titre != '' or $erreur_description != '') {
					$translated = $this->get('translator')->trans('documentation.update_error');
					$this->get('session')->getFlashBag()->add('error', $translated);
					return $this->render('BanquemondialeBundle:Default:Documentation/layout/edit.html.twig', array(
					'edit_form' => $editForm->createView(),
					'delete_form' => $deleteForm->createView(),
					'entity' => $documentation,
					'erreur_titre' => $erreur_titre,
					'erreur_description' => $erreur_description,
					'erreur_fichier' => $erreur_fichier,
					'langue' => $langue
					));
				}

				$traduction->setTitre($titre);
				$traduction->setDescription($description);
				$i++;
			}
			
			
			$documentation->getFichier()->setCheminUpload($chemin->getNom());

			$documentation->getFichier()->setDocumentation($documentation);
			
			$documentation->setUpdateAt(new \DateTime());

			$em->persist($documentation);
			$em->flush();
			
			//die(dump($documentation->getFichier()));
			
			$translated = $translated = $this->get('translator')->trans('succes_modification');
			$this->get('session')->getFlashBag()->add('info', $translated);

			return $this->redirect($this->generateUrl('adminDocumentation'));
		}

		return $this->render('BanquemondialeBundle:Default:Documentation/layout/edit.html.twig', array(
		'entity' => $entity,
		'edit_form' => $editForm->createView(),
		'delete_form' => $deleteForm->createView(),
		'erreur_titre' => $erreur_titre,
		'erreur_description' => $erreur_description,
		'erreur_fichier' => $erreur_fichier,
		'langue' => $langue
		));
	}

	/**
	* Deletes a Produits entity.
	*
	*/
	public function deleteAction(Request $request, $id) {
		$form = $this->createDeleteForm($id);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$entity = $em->getRepository('BanquemondialeBundle:Documentation')->find($id);

			if (!$entity) {
				throw $this->createNotFoundException('entite_non_trouve');
			}
			$fichier = $entity->getFichier();
			$fichier->setDocumentation(null);
			$chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);

			foreach ($entity->getTraduction() as $traduction) {
				$uploaddir = $chemin->getNom() . 'Guides\\';
				if (file_exists($uploaddir . "" . $traduction->getTitre() . '_other.pdf'))
				unlink($uploaddir . "" . $traduction->getTitre() . '_other.pdf');
			}
			$entity->setFichier(null);
			$em->remove($fichier);
			$em->flush();
			$em->remove($entity);
			$em->flush();
			$translated = $translated = $this->get('translator')->trans('message_suppression_entity_succes
	');
			$this->get('session')->getFlashBag()->add('info', $translated);
		}

		return $this->redirect($this->generateUrl('adminDocumentation'));
	}

	/**
	* Creates a form to delete a ocumentation entity by id.
	*
	* @param mixed $id The entity id
	*
	* @return \Symfony\Component\Form\Form The form
	*/
	private function createDeleteForm($id) {
		return $this->createFormBuilder()
		->setAction($this->generateUrl('adminDocumentation_delete', array('id' => $id)))
		->setMethod('DELETE')
		->getForm()
		;
	}

	public function downloadGuideAction($guideName = null) {
		//die(dump($guideName));
		$em = $this->getDoctrine()->getManager();
		$chemin = $em->getRepository('ParametrageBundle:Chemins')->find(1);
		$cheminUpload = $chemin->getNom();
		$cheminUpload = str_replace("\\\\", "\\", $cheminUpload);
		$response = new BinaryFileResponse($cheminUpload . 'Guides\\' . $guideName);
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'guide.pdf');
		return $response;
	}

}
