<?php

namespace ParametrageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BanquemondialeBundle\Entity\Departement;
use BanquemondialeBundle\Form\DepartementType;
use BanquemondialeBundle\Entity\Region;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Departement controller.
 *
 * @Route("/departement")
 * @Security("has_role('ROLE_USER')")
 */
class DepartementController extends Controller {

    /**
     * Lists all Departement entities.
     *
     * @Route("/", name="departement_index")
     * @Method("GET")
     */
    public function indexAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $em = $this->getDoctrine()->getManager();
      
        $departements = $em->getRepository('BanquemondialeBundle:Departement')->findAll();

        return $this->render('ParametrageBundle:Parametrage:Departement/index.html.twig', array(
                    'departements' => $departements,
        ));
    }

    /**
     * Creates a new Departement entity.
     *
     * @Route("/new", name="departement_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $departement = new Departement();
          $codLang = $request->getLocale();
         $em = $this->getDoctrine()->getManager();
       $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);


        $form = $this->createForm('BanquemondialeBundle\Form\DepartementType', $departement,array('locale' => $request->getLocale()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($departement);
            $em->flush();

            $translated = $translated = $this->get('translator')->trans('departement.ajout_succes');
            $this->get('session')->getFlashBag()->add('info', $translated);

            // return $this->redirectToRoute('departement_show', array('id' => $departement->getId()));
            return $this->redirectToRoute('departement_index');
        }

        return $this->render('ParametrageBundle:Parametrage:Departement/new.html.twig', array(
                    'departement' => $departement,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Departement entity.
     *
     * @Route("/{id}", name="departement_show")
     * @Method("GET")
     */
    public function showAction(Departement $departement) {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $deleteForm = $this->createDeleteForm($departement);

        return $this->render('ParametrageBundle:Parametrage:Departement/show.html.twig', array(
                    'departement' => $departement,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Departement entity.
     *
     * @Route("/{id}/edit", name="departement_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Departement $departement) {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $deleteForm = $this->createDeleteForm($departement);
        $editForm = $this->createForm('BanquemondialeBundle\Form\DepartementType', $departement,array('locale' => $request->getLocale(),'pays'=>$departement->getPays()->getId()));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($departement);
            $em->flush();
            $translated = $translated = $this->get('translator')->trans('departement.modification_succes');
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirectToRoute('departement_index');
        }

        return $this->render('ParametrageBundle:Parametrage:Departement/edit.html.twig', array(
                    'departement' => $departement,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Departement entity.
     *
     * @Route("/supprimer/{id}", name="departement_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Departement $departement) {
       $em = $this->getDoctrine()->getManager();


        try {
            $em->remove($departement);
            $em->flush();
            $translated = $this->get('translator')->trans('message_suppression_entity_succes');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $translated = $this->get('translator')->trans('message_suppression_entity_fail');
        }



        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('departement_index');
    }

    /**
     * Creates a form to delete a Departement entity.
     *
     * @param Departement $departement The Departement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Departement $departement) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('departement_delete', array('id' => $departement->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * @Route("departements/actions/",name="action_departements")
     */
    public function actionUsersAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->get('id');
            if ($id != '') {
                $source = (string) $request->get('nom');

                if ($source != '') {
                    $code = (string) $request->get('code');
                    if ($code != '') {
                        $departement = new Departement();

                        $region = $em->getRepository('BanquemondialeBundle:Region')->find($id);

                        if (!$region)
                            return new JsonResponse(array(
                                'error' => '1',
                                'message' => 'Error'));

                        $departement->setLibelle($source);

                        $departement->setRegion($region);
                        $departement->setCode($code);

                        $em->persist($departement);
                        $em->flush();

                        return new JsonResponse(array(
                            'error' => '0',
                            'departementID' => $departement->getId(),
                            'departementCode' => $departement->getCode(),
                            'departementLibelle' => $departement->getLibelle(),
                            'message' => 'Done'));
                    } else
                        return new JsonResponse(array(
                            'error' => '1',
                            'message' => 'id not null'));
                } else
                    return new JsonResponse(array(
                        'error' => '1',
                        'message' => 'id not null'));
            } else
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Errors'));
    }

}
