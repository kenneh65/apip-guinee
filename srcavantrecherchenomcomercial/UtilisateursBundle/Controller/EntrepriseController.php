<?php

namespace UtilisateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use BanquemondialeBundle\Entity\Entreprise;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Utilisateurs controller.
 *
 * @Route("/entreprise")
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class EntrepriseController extends Controller {

    /**
     * @Route("/", name="entreprise_index")
     */
    public function indexAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }


        $em = $this->getDoctrine()->getManager();
        $entreprises = $em->getRepository('BanquemondialeBundle:Entreprise')->findBy(array(),array('id'=>'DESC')) ;//->findAll();
        return $this->render('UtilisateursBundle:Entreprise:index.html.twig', array('entreprises' => $entreprises));
    }

    /**
     * @Route("/edit/{id}",name="entreprise_edit")
     */
    public function editAction(Request $request, Entreprise $entreprise) {
        $form = $this->createForm('BanquemondialeBundle\Form\EntrepriseType', $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entreprise);
            $em->flush();
            $translated = $translated = $this->get('translator')->trans("message_modification_entreprise");
            $this->get('session')->getFlashBag()->add('info', $translated);
            return $this->redirectToRoute('entreprise_index');
        }
        $deleteForm = $this->createDeleteForm($entreprise);

        return $this->render('UtilisateursBundle:Entreprise:edit.html.twig', array('form' => $form->createView(), 'entreprise' => $entreprise, 'delete_form' => $deleteForm->createView()
                        // ...
        ));
    }

    /**
     * @Route("/new",name="entreprise_new")
     */
    public function createAction(Request $request) {

        $entreprise = new Entreprise();
        $form = $this->createForm('BanquemondialeBundle\Form\EntrepriseType', $entreprise);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entreprise->setActif(true);
            $entreprise->setIsSiege(false);
            $em->persist($entreprise);
            $em->flush();
            $translated = $translated = $this->get('translator')->trans("message_ajout");
            $this->get('session')->getFlashBag()->add('info', $translated);
            return $this->redirectToRoute('entreprise_index');
        }


        return $this->render('UtilisateursBundle:Entreprise:create.html.twig', array('form' => $form->createView(), 'entreprise' => $entreprise
                        // ...
        ));
    }

    /**
     * Deletes a enterprise entity.
     *
     * @Route("/delete/{id}", name="entreprise_remove")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Entreprise $entreprise) {

        /* $user = $this->container->get('security.context')->getToken()->getUser();

          if (is_object($user) && $user->getFirstLog()) {
          return $this->redirectToRoute('utilisateur_profil-updatepassword');
          } */
        $em = $this->getDoctrine()->getManager();
        try {
            $entreprise->setActif(false);
            $em->flush();
            $translated = $this->get('translator')->trans('desactivation_message');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $translated = $this->get('translator')->trans('message_suppression_entity_fail');
        }

        $this->get('session')->getFlashBag()->add('info', $translated);


        return $this->redirectToRoute('entreprise_index');
    }

    /**
     * activate a enterprise entity.
     *
     * @Route("/activer/{id}", name="entreprise_activer")
     * @Method("GET")
     */
    public function activerAction(Request $request, Entreprise $entreprise) {

        /* $user = $this->container->get('security.context')->getToken()->getUser();

          if (is_object($user) && $user->getFirstLog()) {
          return $this->redirectToRoute('utilisateur_profil-updatepassword');
          } */
        $em = $this->getDoctrine()->getManager();
        try {
            $entreprise->setActif(true);
            $em->flush();
            $translated = $this->get('translator')->trans('activation_message');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $translated = $this->get('translator')->trans('message_suppression_entity_fail');
        }

        $this->get('session')->getFlashBag()->add('info', $translated);


        return $this->redirectToRoute('entreprise_index');
    }

    /**
     * @Route("/show/{id}",name="entreprise_show")
     */
    public function showAction(Request $request, Entreprise $entreprise) {
        $form = $this->createForm('BanquemondialeBundle\Form\EntrepriseType', $entreprise);
        $form->handleRequest($request);
        return $this->render('UtilisateursBundle:Entreprise:show.html.twig', array('form' => $form->createView(), 'entreprise' => $entreprise
        ));
    }

    /**
     * Creates a form to delete a Utilisateurs entity.
     *
     * @param Utilisateurs $utilisateur The Utilisateurs entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Entreprise $entreprise) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('entreprise_remove', array('id' => $entreprise->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
