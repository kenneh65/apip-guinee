<?php

namespace ParametrageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BanquemondialeBundle\Entity\Departement;
use BanquemondialeBundle\Entity\FonctionTraduction;
use BanquemondialeBundle\Entity\Fonction;
use BanquemondialeBundle\Form\DepartementType;
use BanquemondialeBundle\Entity\Region;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Departement controller.
 *
 * @Route("/fonction")
  * @Security("has_role('ROLE_USER')")
 */
class FonctionController extends Controller {

    /**
     * Lists all Departement entities.
     *
     * @Route("/index", name="fonction_index")
     * @Method("GET")
     */
    public function indexAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $em = $this->getDoctrine()->getManager();
		$request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
		$idCodeLangue = $langue->getId();

        //$fonctions = $em->getRepository('BanquemondialeBundle:Fonction')->findAll();
		$fonctions = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->getListFonctionsAndTraduction($idCodeLangue);

        return $this->render('ParametrageBundle:Parametrage:Fonction/index.html.twig', array('fonctions' => $fonctions));
    }

	
	
      /**
     * Creates a new fonction entity.
     *
     * @Route("/new", name="fonction_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();
        $fonctions = $em->getRepository('BanquemondialeBundle:Fonction')->findAll();
        $resultat = -1;
        if ($request->getMethod() == 'POST') {
            if ($request->get('code_fonction') != '') {

                $fonction = new Fonction();
                $code = $request->get('code_fonction');
                if ($em->getRepository('BanquemondialeBundle:Fonction')->count($code) == 0) {
                    $fonction->setCode($code);

                    foreach ($langues as $langue) {
                        if ($request->get('libelle_' . $langue->getLibelle()) and $request->get('libelle_' . $langue->getLibelle()) != '') {
                            $fonctionTraduction = new FonctionTraduction();
                            $fonctionTraduction->setLibelle($request->get('libelle_' . $langue->getLibelle()));
							//die(dump($request->get('libelleFeminin_' . $langue->getLibelle())));
							$fonctionTraduction->setLibelleFeminin($request->get('libelleFeminin_' . $langue->getLibelle()));
							$fonctionTraduction->setDescription($request->get('description_' . $langue->getLibelle()));
                            $fonctionTraduction->setFonction($fonction);
                            $fonctionTraduction->setLangue($langue);
                            $em->persist($fonction);
                            $em->persist($fonctionTraduction);
                            $em->flush();
                        }
                    }
                    $translated = $translated = $this->get('translator')->trans('message_ajout_fonction');
                    $this->get('session')->getFlashBag()->add('info', $translated);

                    return $this->redirectToRoute('fonction_index');
                }
                $resultat = -3;
            } else
                $resultat = -2;
        }


        return $this->render('ParametrageBundle:Parametrage:Fonction/new.html.twig', array('langues' => $langues, 'fonctions' => $fonctions, 'resultat' => $resultat));
    }
	
	
	
	
    /**
     * Deletes a fonction entity.
     *
     * @Route("/delete/{id}", name="fonction_delete")
     * @Method("GET")
     */
    public function deleteAction(Fonction $fonction) {
        $em = $this->getDoctrine()->getManager();
		
		$listeAssocie = $em->getRepository('BanquemondialeBundle:Associe')->findByFonction($fonction);
		
		$listeRepresentant = $em->getRepository('BanquemondialeBundle:Representant')->findByFonction($fonction);
		
		
		//die(dump($listeDossier));
		if($listeAssocie)
		{
			$translated =  $this->get('translator')->trans('message_suppression_impossible_lien_associe');
		}
		

		else if($listeRepresentant)
		{
			$translated =  $this->get('translator')->trans('message_suppression_impossible_lien_representant');
		}
		
		else
		{
			$em->remove($fonction);
			$em->flush();
			$translated =  $this->get('translator')->trans('fonction.suppression_message');
		}

		
        
        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('fonction_index');
    }
	
	/**
     * @Route("/{id}/toggle",name="fonction_toggle")
     */
	public function toggleAction(Fonction $fonction) {
        $em = $this->getDoctrine()->getManager();
				
		if(!$fonction->isActif())
		{
			$fonction->setActif(true);
			$em->flush();
			$translated =  $this->get('translator')->trans('activation_message');
		}
		else{
			$fonction->setActif(false);
			$em->flush();
			$translated =  $this->get('translator')->trans('desactivation_message');
		}

        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('fonction_index');
    }

	
	
	
	
	
	 /**
     * @Route("/show/{id}",name="fonction_show")
     */
    public function showAction(Fonction $fonction)
    {
        $em = $this->getDoctrine()->getManager();
        $langueTraduit = $em->getRepository('BanquemondialeBundle:Langue')->getLanguesByFunction($fonction);
        $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();

        return $this->render('ParametrageBundle:Parametrage:Fonction/show.html.twig', array('languesTraduit' => $langueTraduit,'langues'=>$langues,'fonction'=>$fonction
            
        ));
    }
	
	
	
    /**
     * @Route("/add", name="add_fonction")
     */
    public function addFonctionLangueAction(Request $request) {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $em = $this->getDoctrine()->getManager();
 $code = $request->get('code');

     
        if ($code == '')
            return new JsonResponse(array(
                'error' => '1',
                'message' => 'code not null'));

        $fonction = new Fonction();
       $fonction->setCode($code);

        $em->persist($fonction);
        $em->flush();

        return new JsonResponse(array(
            'error' => '0',
            'message' => 'done'));
    }

    /**
     * @Route("/delete-fonction", name="delete_fonction")
     */
    public function deleteFonctionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $fonction = $em->getRepository('BanquemondialeBundle:Fonction')->find($request->get('fonction'));

            if (!$fonction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'Fonction not null'));

            $em->remove($fonction);
            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/update-fonction", name="update_fonction")
     */
    public function updateFonctionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $fonction = $em->getRepository('BanquemondialeBundle:Fonction')->find($request->get('fonction'));

            $code = $request->get('code');

         
            if ($code == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'code not null'));

            $fonction->setDescription($description);
            $fonction->setCode($code);

            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/details_fonction/{id}", name="details_fonction")
     */
    public function detailsFonctionLangueAction(Fonction $fonction) {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $em = $this->getDoctrine()->getManager();

        $langueTraduit = $em->getRepository('BanquemondialeBundle:Langue')->getLanguesByFunction($fonction);

        $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();

        return $this->render('ParametrageBundle:Parametrage:Fonction/show.html.twig', array(
                    'fonction' => $fonction, 'langues' => $langues, 'languesTraduit' => $langueTraduit
        ));
    }

    /**
     * @Route("/delete-traduction-langue", name="delete_traduction_fonction")
     */
    public function deleteTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $fonctionTraduction = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->find($request->get('traduction'));

            if (!$fonctionTraduction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'fonctionTraduction not null'));

            $em->remove($fonctionTraduction);
            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/update-code-ex", name="update_fonction")
     */
    public function updateCodeActionEx(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('fonction');
            $description = $request->get('description');
            $code = $request->get('code');

            if (!$code)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'code not null'));

            if (!$description)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'description not null'));

            $em = $this->getDoctrine()->getManager();
            $fonction = $em->getRepository("BanquemondialeBundle:Fonction")->find($id);

            $fonction->setDescription($description);
            $fonction->setCode($code);

            $em->persist($fonction);

            $em->flush();
            return new JsonResponse(array(
                'error' => '0'));
        } else
            return new JsonResponse(array(
                'error' => '1',
                'message' => 'Error'));
    }
	
	
	 /**
     * @Route("/update-code", name="update_code_fonction")
     */
    public function updateCodeAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $code = $request->get('code');
            $em = $this->getDoctrine()->getManager();
            $fonction = $em->getRepository("BanquemondialeBundle:Fonction")->find($id);

            $fonction->setCode($code);
            $em->persist($fonction);
            $em->flush();
            return new JsonResponse(array(
                'error' => '0'));
        } else
            return new JsonResponse(array(
                'error' => '1',
                'message' => 'Error'));
    }
	
	
	
    /**
     * @Route("/add-traduction-langue", name="add_traduction_fonction")
     */
    public function addTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
            $fonction = $em->getRepository('BanquemondialeBundle:Fonction')->find($request->get('id'));
            $libelle = $request->get('libelle');
			$libelleFeminin = $request->get('libelleFeminin_');
			$description = $request->get('description');

            if (!$langue)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));

            if (!$fonction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));

            if ($libelle == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));
					
			if ($description == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'description_cannot_null'));


            $fonctionTraduction = new FonctionTraduction();
            $fonctionTraduction->setLibelle($libelle);
			$fonctionTraduction->setLibelleFeminin($libelleFeminin);
			$fonctionTraduction->setDescription($description);
            $fonctionTraduction->setFonction($fonction);
            $fonctionTraduction->setLangue($langue);


            $em->persist($fonction);
            $em->persist($fonctionTraduction);
            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }
	
	
    
	
	/**
     * @Route("/update-traduction-langue", name="update_traduction_fonction")
     */
    public function updateTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {		

            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));

            $fonction = $em->getRepository('BanquemondialeBundle:Fonction')->find($request->get('id'));

            $fonctionTraduction = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->find($request->get('traduction'));


            $libelle = $request->get('libelle');
			$libelleFeminin = $request->get('libelleFeminin');
			$description = $request->get('description');

            if (!$langue)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'langue_canot_null'));
					
					

            if (!$fonctionTraduction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'fonctionTraduction_cannot_null'));
					
					

            if (!$fonction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'fonction_cannot_null'));
					
					

            if ($libelle == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'libelle_cannot_null'));
					
					
			
			if ($description == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'description_cannot_null'));
					

            $fonctionTraduction->setLibelle($libelle);
			$fonctionTraduction->setLibelleFeminin($libelleFeminin);
			$fonctionTraduction->setDescription($description);
            $fonctionTraduction->setFonction($fonction);
            $fonctionTraduction->setLangue($langue);

            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'langue' => $fonctionTraduction->getLangue()->getLibelle(),
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }
	
	
	
	
	

}
