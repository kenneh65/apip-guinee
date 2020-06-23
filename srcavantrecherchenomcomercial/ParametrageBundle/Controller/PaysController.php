<?php

namespace ParametrageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BanquemondialeBundle\Entity\PaysTraduction;
use BanquemondialeBundle\Entity\Pays;
use BanquemondialeBundle\Form\PaysType;
use BanquemondialeBundle\Form\PaysTraductionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Pays controller.
 *
 * @Route("/pays")
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class PaysController extends Controller {

    /**
     * Lists all Pays entities.
     *
     * @Route("/", name="pays_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
      
		$request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
		$idCodeLangue = $langue->getId();

        //$fonctions = $em->getRepository('BanquemondialeBundle:Fonction')->findAll();
		$pays = $em->getRepository('BanquemondialeBundle:PaysTraduction')->getListPaysAndTraduction($idCodeLangue);

		
        return $this->render('ParametrageBundle:Parametrage:Pays/index.html.twig', array('pays' => $pays));
    }

    /**
     * Creates a new Pays entity.
     *
     * @Route("/new", name="pays_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();
        $pays = $em->getRepository('BanquemondialeBundle:Pays')->findAll();
        $resultat = -1;
        if ($request->getMethod() == 'POST') {
            if ($request->get('code_pays') != '') {

                $pays = new Pays();
                $code = $request->get('code_pays');
                if ($request->get('residence_pays')) {
                    $pays->setResidence(true);
                    //ON change l'état du pays qui était le pays par défaut
                    $payss = $em->getRepository('BanquemondialeBundle:Pays')->findByResidence(true);
                    foreach ($payss as $pay) {
                        $pay->setResidence(false);
                        $em->persist($pay);
                    }
                }
                if ($em->getRepository('BanquemondialeBundle:Pays')->count($code) == 0) {
                    $pays->setCode($code);

                    foreach ($langues as $langue) {
                        if ($request->get('libelle_' . $langue->getLibelle()) and $request->get('libelle_' . $langue->getLibelle()) != '') {
                            $paysTraduction = new PaysTraduction();
                            $paysTraduction->setLibelle($request->get('libelle_' . $langue->getLibelle()));
							$paysTraduction->setNationalite($request->get('nationalite_' . $langue->getLibelle()));
                            $paysTraduction->setPays($pays);
                            $paysTraduction->setLangue($langue);
                            $em->persist($pays);
                            $em->persist($paysTraduction);
                            $em->flush();
                        }
                    }
                    $translated = $translated = $this->get('translator')->trans('succes_ajout_pays');
                    $this->get('session')->getFlashBag()->add('info', $translated);

                    return $this->redirectToRoute('pays_index');
                }
                $resultat = -3;
            } else
                $resultat = -2;
        }


        return $this->render('ParametrageBundle:Parametrage:Pays/new.html.twig', array('langues' => $langues, 'pays' => $pays, 'resultat' => $resultat));
    }

    /**
     * Finds and displays a Pays entity.
     *
     * @Route("/{id}", name="pays_show")
     * @Method("GET")
     */
    public function showAction(Pays $pay) {
        $em = $this->getDoctrine()->getManager();
        $langueTraduit = $em->getRepository('BanquemondialeBundle:Langue')->getLangues($pay);
        $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();

        return $this->render('ParametrageBundle:Parametrage:Pays/show.html.twig', array(
                    'pays' => $pay, 'langues' => $langues, 'languesTraduit' => $langueTraduit
        ));
    }

    /**
     * Displays a form to edit an existing Pays entity.
     *
     * @Route("/{id}/edit", name="pays_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PaysTraduction $pay) {
        $deleteForm = $this->createDeleteForm($pay);
        $editForm = $this->createForm('BanquemondialeBundle\Form\PaysTraductionType', $pay);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();


            $em->persist($pay);
            $em->flush();

            return $this->redirectToRoute('pays_edit', array('id' => $pay->getId()));
        }

        return $this->render('ParametrageBundle:Parametrage:Pays/edit.html.twig', array(
                    'pay' => $pay,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Pays entity.
     *
     * @Route("/delete/{id}", name="pays_delete")
     * @Method("GET")
     */
    public function deleteAction(Pays $pay) {
        $em = $this->getDoctrine()->getManager();
        $translated = "";

        try {
            $em->remove($pay);
            $em->flush();
            $translated = $this->get('translator')->trans('message_suppression_entity_succes');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $translated = $this->get('translator')->trans('message_suppression_entity_fail');
        }



        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('pays_index');
    }

    /**
     * Creates a form to delete a Pays entity.
     *
     * @param Pays $pay The Pays entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PaysTraduction $pay) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('pays_delete', array('id' => $pay->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * @Route("/add-traduction-langue", name="add_traduction_pays")
     */
    public function addTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
            $pays = $em->getRepository('BanquemondialeBundle:Pays')->find($request->get('id'));
            $libelle = $request->get('libelle');
			$nationalite = $request->get('nationalite_');

            if (!$langue)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));

            if (!$pays)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));

            if ($libelle == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));


            $paysTraduction = new PaysTraduction();
            $paysTraduction->setLibelle($libelle);
			$fonctionTraduction->setNationalite($nationalite);
            $paysTraduction->setPays($pays);
            $paysTraduction->setLangue($langue);


            $em->persist($pays);
            $em->persist($paysTraduction);
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
     * @Route("/delete-traduction-langue", name="delete_traduction_pays")
     */
    public function deleteTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
            $pays = $em->getRepository('BanquemondialeBundle:Pays')->find($request->get('id'));
            $paysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->find($request->get('traduction'));


            if (!$paysTraduction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'paysTraduction not null'));

            if (!$pays)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'pays not null'));

            $em->remove($paysTraduction);
            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'langue' => $paysTraduction->getLangue()->getLibelle(),
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/update-traduction-langue", name="update_traduction_pays")
     */
    public function updateTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
            $pays = $em->getRepository('BanquemondialeBundle:Pays')->find($request->get('id'));
            $paysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->find($request->get('traduction'));

            $libelle = $request->get('libelle');
			$nationalite = $request->get('nationalite');

            if (!$langue)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'langue not null'));

            if (!$paysTraduction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'paysTraduction not null'));

            if (!$pays)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'pays not null'));

            if ($libelle == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'libelle not null'));
					
			if ($nationalite == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'nationalite not null'));

            $paysTraduction->setLibelle($libelle);
            $paysTraduction->setPays($pays);
			$paysTraduction->setNationalite($nationalite);
            $paysTraduction->setLangue($langue);

            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'langue' => $paysTraduction->getLangue()->getLibelle(),
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/update-code", name="update_code_pays")
     */
    public function updateCodeAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $code = $request->get('code');
            $em = $this->getDoctrine()->getManager();
            $pays = $em->getRepository("BanquemondialeBundle:Pays")->find($id);
            if ($request->get('residence_pays')) {
                $pays->setResidence(true);
                //ON change l'état du pays qui était le pays par défaut
                $payss = $em->getRepository('BanquemondialeBundle:Pays')->findByResidence(true);
                foreach ($payss as $pay) {
                    $pay->setResidence(false);
                    $em->persist($pay);
                }
            } else {
                $pays->setResidence(false);
            }
            $pays->setCode($code);
            $em->persist($pays);
            $em->flush();
            return new JsonResponse(array(
                'error' => '0'));
        } else
            return new JsonResponse(array(
                'error' => '1',
                'message' => 'Error'));
    }
	
	/**
     * @Route("/{id}/toggle",name="Pays_toggle")
     */
    public function toggleAction(Pays $pays) {
        $em = $this->getDoctrine()->getManager();
				
		if(!$pays->isActif())
		{
			$pays->setActif(true);
			$em->flush();
			$translated =  $this->get('translator')->trans('activation_message');
		}
		else{
			$pays->setActif(false);
			$em->flush();
			$translated =  $this->get('translator')->trans('desactivation_message');
		}

		 //echo "<script>console.log( 'Debug Objects: ' );</script>";
		 
        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('pays_index');
    }


}
