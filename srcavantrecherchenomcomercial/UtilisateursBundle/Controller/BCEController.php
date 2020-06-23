<?php

namespace UtilisateursBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UtilisateursBundle\Entity\Utilisateurs;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * BCE controller.
 *
 * @Route("/createur")
 *  * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class BCEController extends Controller {

    /**
     * Lists all Utilisateurs entities.
     *
     * @Route("/", name="bce_index")
     * @Method("GET")
     */
    public function indexAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }


        $em = $this->getDoctrine()->getManager();
        $query = $this->getDoctrine()->getManager()
                        ->createQuery(
                                'SELECT u FROM UtilisateursBundle:Utilisateurs u WHERE u.roles LIKE :role'
                        )->setParameter('role', '%"ROLE_BCE"%');

        $utilisateurs = $query->getResult();
        return $this->render('UtilisateursBundle:bce:index.html.twig', array(
                    'utilisateurs' => $utilisateurs,
        ));
    }

    /**
     * Creates a new Utilisateurs entity.
     *
     * @Route("/new", name="bce_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {

        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }

        $utilisateur = new Utilisateurs();
        $form = $this->createForm('UtilisateursBundle\Form\UtilisateursType', $utilisateur, array('locale' => $request->getLocale()));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setEnabled(false);
            $passwordGenerated = uniqid();
            $utilisateur->setPlainPassword($passwordGenerated);
//              $passwordGenerated

            $utilisateur->setRoles(array('ROLE_BCE'));

            $utilisateur->setFirstLog(true);
            $tokenGenerator = $this->get('fos_user.util.token_generator');

            if (null === $utilisateur->getConfirmationToken()) {
                $utilisateur->setConfirmationToken($tokenGenerator->generateToken());
            }


            $url = $this->generateUrl('fos_user_registration_confirm', array('token' => $utilisateur->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);

            $em = $this->getDoctrine()->getManager();

            $messagerie = $em->getRepository('ParametrageBundle:Messagerie')->find(1);
            $transport = \Swift_SmtpTransport::newInstance($messagerie->getMailerHost(), $messagerie->getMailerPort())
                            ->setUsername($messagerie->getMailerUser())
                            ->setPassword($messagerie->getMailerPassword())->setEncryption($messagerie->getEncryption());
            $mailer = \Swift_Mailer::newInstance($transport);

            $message = \Swift_Message::newInstance($transport);

            $translated = $translated = $this->get('translator')->trans("utilisateur.confirmation_incription");

            $message->setSubject($translated)
                    ->setFrom(array($messagerie->getExpediteurEmail() => $messagerie->getExpediteurName()))
                    ->setTo($utilisateur->getEmail())
                    ->setBody(
                            $this->renderView(
                                    'ParametrageBundle:Parametrage:email\confirmeInscription.email.twig', array('user' => $utilisateur, 'password' => $passwordGenerated, 'confirmationUrl' => $url, 'logo' => $message->embed(\Swift_Image::fromPath('front-office/gainde2000.jpg')))
                            ), 'text/html'
            );

     


            $em->persist($utilisateur);
            $em->flush();
             $mailer->send($message);
            $translated = $translated = $this->get('translator')->trans("createur.message_ajouter");
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirectToRoute('bce_index');
        }
        if ($form->isSubmitted()) {
            $translated = $translated = $this->get('translator')->trans("erreur_ajout_createur");
            $this->get('session')->getFlashBag()->add('error', $translated);
        }
        return $this->render('UtilisateursBundle:bce:new.html.twig', array(
                    'utilisateur' => $utilisateur,
                    'form' => $form->createView()
        ));
    }

//    public function showAction(Utilisateurs $utilisateur) {
//
//        $user = $this->container->get('security.context')->getToken()->getUser();
//
//        if (is_object($user) && $user->getFirstLog()) {
//            return $this->redirectToRoute('utilisateur_profil-updatepassword');
//        }
//
//
//        $deleteForm = $this->createDeleteForm($utilisateur);
//
//        return $this->render('UtilisateursBundle:utilisateurs:show.html.twig', array(
//                    'utilisateur' => $utilisateur,
//                    'delete_form' => $deleteForm->createView(),
//        ));
//    }

    /**
     * Displays a form to edit an existing Utilisateurs entity.
     *
     * @Route("/{id}/edit", name="bce_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Utilisateurs $utilisateur) {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }





        $em = $this->getDoctrine()->getManager();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($request->getLocale());
        $definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $utilisateur->getPaysResidence(), 'langue' => $langue));



        $editForm = $this->createForm('UtilisateursBundle\Form\UtilisateursType', $utilisateur, array('locale' => $request->getLocale(), 'definedPaysTraduction' => $definedPaysTraduction));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($utilisateur);
            $em->flush();
            $translated = $translated = $this->get('translator')->trans("createur.message_modifier");
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirectToRoute('bce_index');
        }
        $deleteForm = $this->createDeleteForm($utilisateur);

        return $this->render('UtilisateursBundle:bce:edit.html.twig', array(
                    'utilisateur' => $utilisateur,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Utilisateurs entity.
     *
     * @Route("/{id}", name="bce_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Utilisateurs $utilisateur) {

        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }


        $form = $this->createDeleteForm($utilisateur);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($utilisateur);
            $em->flush();
            $translated = $translated = $this->get('translator')->trans("message_suppression_createur");
            $this->get('session')->getFlashBag()->add('info', $translated);
        }

        return $this->redirectToRoute('bce_index');
    }

    /**
     * Creates a form to delete a Utilisateurs entity.
     *
     * @param Utilisateurs $utilisateur The Utilisateurs entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Utilisateurs $utilisateur) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('bce_delete', array('id' => $utilisateur->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    
}
