<?php

namespace ParametrageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BanquemondialeBundle\Entity\Departement;
use BanquemondialeBundle\Entity\FormeJuridiqueTraduction;
use BanquemondialeBundle\Entity\FormeJuridique;
use BanquemondialeBundle\Form\DepartementType;
use BanquemondialeBundle\Entity\Region;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Departement controller.
 *
 * @Route("/formejuridique")
 * @Security("has_role('ROLE_USER')")
 */
class FormeJuridiqueController extends Controller {

    /**
     * Lists all Departement entities.
     *
     * @Route("/", name="formejuridique_index")
     * @Method("GET")
     */
    public function indexAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }


        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction');
        $formejuridiques = $em->getRepository('BanquemondialeBundle:FormeJuridique')->findAll();

        return $this->render('ParametrageBundle:Parametrage:FormeJuridique/index.html.twig', array(
                    'formejuridiques' => $formejuridiques,
                    'repository' => $repository
        ));
    }

    /**
     * @Route("/add", name="add_formjuridique")
     */
    public function addFormeJuridiqueLangueAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();
        $formes = $em->getRepository('BanquemondialeBundle:FormeJuridique')->findAll();
        $resultat = -1;
        $forme = new FormeJuridique();		 
               
        if ($request->getMethod() == 'POST') {
		//die(dump($request->request->all()));
            if ($request->get('code_forme') != '') {

                $sigle = $request->get('code_forme');

                if ($em->getRepository('BanquemondialeBundle:FormeJuridique')->count($sigle) == 0) {
                    $forme->setSigle($sigle);
                    $em->persist($forme);
					
                    foreach ($langues as $langue) {
                        if ($request->get('libelle_' . $this->get('translator')->trans($langue->getLibelle()) ) and $request->get('sigle_' . $this->get('translator')->trans($langue->getLibelle())) != '') {
                            $formeTraduction = new FormeJuridiqueTraduction();
                            $formeTraduction->setLibelle($request->get('libelle_' . $this->get('translator')->trans($langue->getLibelle())));
                            $formeTraduction->setFormeJuridique($forme);
                            $formeTraduction->setLangue($langue);
                            $formeTraduction->setSigle($request->get('sigle_' . $this->get('translator')->trans($langue->getLibelle())));

                            $em->persist($formeTraduction);
							
							//die(dump($formeTraduction));
                           
                        }
                    }
                    $em->flush();
                    $translated = $translated = $this->get('translator')->trans('forme.ajout_success');
                    $this->get('session')->getFlashBag()->add('info', $translated);

                    return $this->redirectToRoute('formejuridique_index');
                }
                $resultat = -3;
            } else
                $resultat = -2;
        }


        return $this->render('ParametrageBundle:Parametrage:FormeJuridique/new.html.twig', array('langues' => $langues, 'forme' => $forme, 'resultat' => $resultat));
    }

    /**
     * @Route("/delete-formjuridique", name="delete_formjuridique")
     */
    public function deleteFormeJuridiqueLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $FormeJuridique = $em->getRepository('BanquemondialeBundle:FormeJuridique')->find($request->get('formejuridique'));

            if (!$FormeJuridique)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'FormeJuridique not null'));

            $em->remove($FormeJuridique);
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
     * @Route("/update-formjuridique", name="update_formjuridique")
     */
    public function updateFormeJuridiqueLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $formeJuridique = $em->getRepository('BanquemondialeBundle:FormeJuridique')->find($request->get('formejuridique'));

            $sigle = $request->get('sigle');


            if ($sigle == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'sigle not null'));

            $formeJuridique->setSigle($sigle);

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
     * @Route("/details_formejuridique/{id}", name="details_formejuridique")
     */
    public function detailsFormeJuridiqueLangueAction(FormeJuridique $formeJuridique) {
        $em = $this->getDoctrine()->getManager();

        $langueTraduit = $em->getRepository('BanquemondialeBundle:Langue')->getLanguesByFormeJuridique($formeJuridique);
        $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();

        return $this->render('ParametrageBundle:Parametrage:FormeJuridique/show.html.twig', array(
                    'formeJuridique' => $formeJuridique, 'langues' => $langues, 'languesTraduit' => $langueTraduit
        ));
    }

    /**
     * @Route("/add-traduction-langue", name="add_traduction_forme_juridique")
     */
    public function addTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
            $formeJuridique = $em->getRepository('BanquemondialeBundle:FormeJuridique')->find($request->get('id'));
            $sigle = $request->get('sigle');
            $libelle = $request->get('libelle');

            if (!$langue)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));

            if (!$formeJuridique)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));

            if ($libelle == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'libelle not null'));

            if ($libelle == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'sigle not null'));

            $formeJuridiqueTraduction = new FormeJuridiqueTraduction();
            $formeJuridiqueTraduction->setLibelle($libelle);
            $formeJuridiqueTraduction->setSigle($sigle);
            $formeJuridiqueTraduction->setFormeJuridique($formeJuridique);
            $formeJuridiqueTraduction->setLangue($langue);

            $em->persist($formeJuridique);
            $em->persist($formeJuridiqueTraduction);
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
     * @Route("/delete-traduction-langue", name="delete_traduction_forme_juridique")
     */
    public function deleteTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $formeJuridiqueTraduction = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->find($request->get('traduction'));

            if (!$formeJuridiqueTraduction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'formeJuridiqueTraduction not null'));

            $em->remove($formeJuridiqueTraduction);
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
     * @Route("/update-traduction-langue", name="update_traduction_forme_juridique")
     */
    public function updateTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
            $formeJuridique = $em->getRepository('BanquemondialeBundle:FormeJuridique')->find($request->get('id'));
            $formeJuridiqueTraduction = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->find($request->get('traduction'));

            $sigle = $request->get('sigle');

            $libelle = $request->get('libelle');

            if (!$langue)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'langue not null'));

            if (!$formeJuridiqueTraduction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'formeJuridiqueTraduction not null'));

            if (!$formeJuridique)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'formeJuridique not null'));

            if ($libelle == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'libelle not null'));

            $formeJuridiqueTraduction->setLibelle($libelle);
            $formeJuridiqueTraduction->setSigle($sigle);
            $formeJuridiqueTraduction->setFormeJuridique($formeJuridique);
            $formeJuridiqueTraduction->setLangue($langue);

            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'langue' => $formeJuridiqueTraduction->getLangue()->getLibelle(),
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/update-code", name="update_formjuridique_p")
     */
	 public function updateCodeAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $sigle = $request->get('sigle');
            $em = $this->getDoctrine()->getManager();
            $formeJuridique = $em->getRepository("BanquemondialeBundle:FormeJuridique")->find($id);

            $formeJuridique->setSigle($sigle);
            $em->persist($formeJuridique);
            $em->flush();
            return new JsonResponse(array(
                'error' => '0'));
        } else
            return new JsonResponse(array(
                'error' => '1',
                'message' => 'Error'));
    }
	 
	 /*
    public function updateCodeAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $libelle = $request->get('libelle');
            $sigle = $request->get('sigle');

            if (!$sigle)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'sigle not null'));

            if (!$libelle)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'libelle not null'));

            $em = $this->getDoctrine()->getManager();
            $formeJuridique = $em->getRepository("BanquemondialeBundle:FormeJuridique")->find($id);

            $formeJuridique->setLibelle($libelle);
            $formeJuridique->setSigle($sigle);

            $em->persist($formeJuridique);

            $em->flush();
            return new JsonResponse(array(
                'error' => '0'));
        } else
            return new JsonResponse(array(
                'error' => '1',
                'message' => 'Error'));
    }
	*/
	
	/**
     * @Route("/{id}/toggle",name="formeJuridique_toggle")
     */
    public function toggleAction(FormeJuridique $formeJuridique) {
        $em = $this->getDoctrine()->getManager();
				
		if(!$formeJuridique->isActif())
		{
			$formeJuridique->setActif(true);
			$em->flush();
			$translated =  $this->get('translator')->trans('activation_message');
		}
		else{
			$formeJuridique->setActif(false);
			$em->flush();
			$translated =  $this->get('translator')->trans('desactivation_message');
		}

		 //echo "<script>console.log( 'Debug Objects: ' );</script>";
		 
        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('formejuridique_index');
    }

}
