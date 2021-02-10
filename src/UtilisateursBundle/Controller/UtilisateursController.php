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
use UtilisateursBundle\Entity\Message;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

/**
 * Utilisateurs controller.
 *
 * @Route("/")
 */
class UtilisateursController extends Controller {

    /**
     * Lists all Utilisateurs entities.
     *
     * @Route("/", name="utilisateurs_index")
     * @Method("GET")
     */
    public function indexAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }


        $em = $this->getDoctrine()->getManager();
        $utilisateurs = array();
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            $utilisateurs = $em->getRepository('UtilisateursBundle:Utilisateurs')->findBy(array(), array('dateCreation' => 'DESC'));
        } else
        if ($this->get('security.context')->isGranted('ROLE_POLE')) {
            $utilisateurs = $em->getRepository('UtilisateursBundle:Utilisateurs')->findByPole($user->getPole());
        } else if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $utilisateurs = $em->getRepository('UtilisateursBundle:Utilisateurs')->findByEntreprise($user->getEntreprise());
        } else {
            $utilisateurs = $em->getRepository('UtilisateursBundle:Utilisateurs')->findBy(array(), array('dateCreation' => 'DESC'));
            if ($user->getUsername()=='sidibesuperviseur'){
                $utilisateurs=$em->getRepository('UtilisateursBundle:Utilisateurs')->findBy(['entreprise'=>2,'profile'=>[1,2]]);
            }
        }
        return $this->render('UtilisateursBundle:utilisateurs:index.html.twig', array(
                    'utilisateurs' => $utilisateurs,
        ));
    }

    /**
     * Lists online users.
     *
     * @Route("/online", name="users_connected")
     * @Method("GET")
     */
    public function connectedUserAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }


        $em = $this->getDoctrine()->getManager(); 
        $dateDuJour=new \DateTime();
        $usersEnligne=$em->getRepository('BanquemondialeBundle:DocumentCollected')->findByLastLogin($dateDuJour);        

        return $this->render('UtilisateursBundle:utilisateurs:usersOnline.html.twig', array(
                    'utilisateurs' => $usersEnligne,'dateChoisi'=>$dateDuJour
        ));
    }

    /**
     * Creates a new Utilisateurs entity.
     *
     * @Route("/new", name="utilisateurs_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {

        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }

        $utilisateur = new Utilisateurs();
        $form = $this->createForm('UtilisateursBundle\Form\Utilisateurs2Type', $utilisateur, array('locale' => $request->getLocale()));
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            //$pole = $request->get('poles');
            $pole = $request->get('frmUtilisateur')['pole'];
            //$utilisateur->setEnabled(true);
            $passwordGenerated = 'B_' . uniqid();
            $utilisateur->setPlainPassword($passwordGenerated);
//              $passwordGenerated
            if ($pole != null) {
                $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
                $poleNotaire = $em->getRepository('ParametrageBundle:Pole')->getPoleByName("Notaire");
                $poleSumited = $em->getRepository('ParametrageBundle:Pole')->find($pole);
                if ($poleSumited == $poleAPIP) {
                    $utilisateur->setRoles(array('ROLE_CREATEUR'));
                    //$utilisateur->setEntreprise($em->getRepository('BanquemondialeBundle:Entreprise')->findOneById($poleAPIP));
                } elseif ($poleSumited == $poleNotaire) {
                    $utilisateur->setRoles(array('ROLE_CREATEUR'));
                    // $utilisateur->setEntreprise($em->getRepository('BanquemondialeBundle:Entreprise')->findOneById($poleNotaire));
                } else {
                    $utilisateur->setRoles(array('ROLE_POLE'));
                    $utilisateur->setPole($em->getRepository('ParametrageBundle:Pole')->findOneById($pole));
                }
                /* if ($this->get('security.context')->isGranted('ROLE_POLE') or $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
                  $utilisateur->setRoles(array('ROLE_POLE'));
                  $utilisateur->setPole($em->getRepository('ParametrageBundle:Pole')->findOneById($pole));
                  } else {

                  } */
            } else
                $utilisateur->setRoles(array('ROLE_CREATEUR'));

            $utilisateur->setFirstLog(true);
            $tokenGenerator = $this->get('fos_user.util.token_generator');

            if (null === $utilisateur->getConfirmationToken()) {
                $utilisateur->setConfirmationToken($tokenGenerator->generateToken());
            }
            //changement de l'url envoyé par mail
            $nomDeDomaine = $em->getRepository('ParametrageBundle:Chemins')->find(3)->getNom();
            $originalUrl = $this->generateUrl('fos_user_registration_confirm', array('token' => $utilisateur->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_PATH);
            //on recupere le texte apres l'occurence utilisateur puis on l'ajoute au nom de domaine prit dans la bdd
            $subUrl = substr($originalUrl, strpos($originalUrl, "utilisateurs") + strlen("utilisateurs"));
            $url = $nomDeDomaine . "/" . $request->getLocale() . "/utilisateurs" . $subUrl;
            //die(dump($newUrl));	

            $em = $this->getDoctrine()->getManager();

            $messagerie = $em->getRepository('ParametrageBundle:Messagerie')->find(1);
            $transport = \Swift_SmtpTransport::newInstance($messagerie->getMailerHost(), $messagerie->getMailerPort())
                            ->setUsername($messagerie->getMailerUser())
                            ->setPassword($messagerie->getMailerPassword())->setEncryption($messagerie->getEncryption());
            $mailer = \Swift_Mailer::newInstance($transport);

            $message = \Swift_Message::newInstance($transport);

            $translated = $translated = $this->get('translator')->trans("utilisateur.confirmation_incription");
            $email = $utilisateur->getEmail();
            // ...

            $emailConstraint = new EmailConstraint();
            //$emailConstraint->message = 'Your customized error message';
            $errors = $this->get('validator')->validateValue(
                    $email, $emailConstraint
            );
            if (count($errors) > 0) {
                //$errorsString = (string) $errors;
                $translated = $translated = $this->get('translator')->trans("utilisateur.InvalideEmail");
                $this->get('session')->getFlashBag()->add('error', $translated);
            }
            //end validate e-mail
            //die(dump($errors));
            else {
                $message->setSubject($translated)
                        ->setFrom(array($messagerie->getExpediteurEmail() => $messagerie->getExpediteurName()))
                        ->setTo($utilisateur->getEmail())
                        ->setBody(
                                $this->renderView(
                                        'ParametrageBundle:Parametrage:email\confirmeInscription.email.twig', array('user' => $utilisateur, 'password' => $passwordGenerated, 'confirmationUrl' => $url, 'logo' => $message->embed(\Swift_Image::fromPath('front-office/LOGO_final.png')))
                                ), 'text/html'
                );
                try {
                    $mailer->send($message);
                } catch (\Exception $e) {
                    $translated = $this->get('translator')->trans("error_send_mail");
                    $translated = $this->get('translator')->trans($e);
                    $this->get('session')->getFlashBag()->add('error', $translated);
                    $this->get('logger')->error($e->getMessage());
                }


                $em->persist($utilisateur);
                $em->flush();
                $translated = $translated = $this->get('translator')->trans("utilisateur.message_ajouter");
                $this->get('session')->getFlashBag()->add('info', $translated);

                return $this->redirectToRoute('utilisateurs_index');
            }
        }
        if ($form->isSubmitted()) {
            $translated = $translated = $this->get('translator')->trans("erreur_ajout_utilisateur");
            $this->get('session')->getFlashBag()->add('error', $translated);
        }
        $poles = $em->getRepository('ParametrageBundle:Pole')->findAll();
        $polesUtilises = $em->getRepository('ParametrageBundle:Pole')->getPoles();
        return $this->render('UtilisateursBundle:utilisateurs:new.html.twig', array(
                    'utilisateur' => $utilisateur,
                    'form' => $form->createView(),
                    'poles' => $poles,
                    'polesUtilises' => $polesUtilises
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
     * @Route("/{id}/edit", name="utilisateurs_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Utilisateurs $utilisateur) {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }


        if ($utilisateur->getPole() && ($utilisateur->getPole()->getSigle() === "DP" || $utilisateur->getPole()->getSigle() === "INV")) {
            return $this->redirectToRoute('register_edit', array('id' => $utilisateur->getId()));
        }


        $em = $this->getDoctrine()->getManager();

        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($request->getLocale());
        //$definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $utilisateur->getPaysResidence(), 'langue' => $langue));



        $editForm = $this->createForm('UtilisateursBundle\Form\Utilisateurs2Type', $utilisateur, array('locale' => $request->getLocale()));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $pole = $request->get('frmUtilisateur')['pole'];

            $entreprise = $request->get('entreprise');
            //  $utilisateur->setPlainPassword('passer');

            if ($pole != null) {
                $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
                $poleNotaire = $em->getRepository('ParametrageBundle:Pole')->getPoleByName("Notaire");
                $poleAPIPId = $poleAPIP->getId();
                //die(dump($poleAPIPId));
                $poleSumited = $em->getRepository('ParametrageBundle:Pole')->find($pole);

                if ($poleSumited == $poleAPIP) {
                    $utilisateur->setRoles(array('ROLE_CREATEUR'));
                    //$utilisateur->setEntreprise($em->getRepository('BanquemondialeBundle:Entreprise')->findOneById($poleAPIP));
                } elseif ($poleSumited == $poleNotaire) {

                    $utilisateur->setRoles(array('ROLE_CREATEUR'));
                    //$utilisateur->setEntreprise($em->getRepository('BanquemondialeBundle:Entreprise')->findOneById($poleNotaire));
                } else {
                    $utilisateur->setRoles(array('ROLE_POLE'));
                    $utilisateur->setPole($em->getRepository('ParametrageBundle:Pole')->findOneById($pole));
                }
            } else {
                $utilisateur->setPole(null);
                $utilisateur->setRoles(array('ROLE_CREATEUR'));
            }
            $email = $utilisateur->getEmail();
            // ...

            $emailConstraint = new EmailConstraint();
            $errors = $this->get('validator')->validateValue(
                    $email, $emailConstraint
            );
            if (count($errors) > 0) {
                //$errorsString = (string) $errors;
                $translated = $translated = $this->get('translator')->trans("utilisateur.InvalideEmail");
                $this->get('session')->getFlashBag()->add('error', $translated);
            }
            //end validate e-mail
            //die(dump($errors));
            else {
                $em->persist($utilisateur);
                $em->flush();
                $translated = $translated = $this->get('translator')->trans("utilisateur.message_modifier");
                $this->get('session')->getFlashBag()->add('info', $translated);

                return $this->redirectToRoute('utilisateurs_index');
            }
        }
        $deleteForm = $this->createDeleteForm($utilisateur);
        $poles = $em->getRepository('ParametrageBundle:Pole')->findAll();
        $polesUtilises = $em->getRepository('ParametrageBundle:Pole')->getPoles();

        return $this->render('UtilisateursBundle:utilisateurs:edit.html.twig', array(
                    'utilisateur' => $utilisateur,
                    'form' => $editForm->createView(),
                    'poles' => $poles,
                    'polesUtilises' => $polesUtilises,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Utilisateurs entity.
     *
     * @Route("/{id}", name="utilisateurs_delete")
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
            $translated = $translated = $this->get('translator')->trans("message_suppression_utilisateur");
            $this->get('session')->getFlashBag()->add('info', $translated);
        }

        return $this->redirectToRoute('utilisateurs_index');
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
                        ->setAction($this->generateUrl('utilisateurs_delete', array('id' => $utilisateur->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * Finds and displays a Utilisateurs entity.
     *
     * @Route("/profil/", name="utilisateurs_profil")
     * @Method("GET")
     */
    public function profilAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }


        $utilisateur = $this->container->get('security.context')->getToken()->getUser();
        $erreurs = array();
        $erreurs['username'] = 1;
        $erreurs['email'] = 1;
        $erreurs['password'] = 1;
        return $this->render('UtilisateursBundle:utilisateurs:profil.html.twig', array(
                    'utilisateur' => $utilisateur, 'erreurs' => $erreurs
        ));
    }

    /**
     * Displays a form to edit an existing Utilisateurs entity.
     *
     * @Route("/password", name="utilsateur_password")
     * @Method({"GET", "POST"})
     */
    public function setPasswordAction(Request $request) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $password = $request->get('password');
        $password1 = $request->get('password1');
        $old = $user->getPassword();
        $erreurs = array();
        $erreurs['username'] = 1;
        $erreurs['email'] = 1;
        $erreurs['password'] = 1;
        if ($password != $password1) {
            $erreurs['password'] = -1;
        } else
        if ($password != null) {

            if (!preg_match("#^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[\da-zA-Z_@\*\-\.;,!\$\#]{6,}$#", $password)) {
                $erreurs['password'] = -2;
                return $this->render('UtilisateursBundle:utilisateurs:profil.html.twig', array(
                            'utilisateur' => $user, 'erreurs' => $erreurs));
            }
            $em = $this->getDoctrine()->getManager();
            $userManager = $this->container->get('fos_user.user_manager');
            $user->setPlainPassword($password);
            $userManager->updatePassword($user);
            if (strcmp($old, $user->getPlainPassword()) == 0) {
                $erreurs['password'] = -3;
                return $this->render('UtilisateursBundle:utilisateurs:profil.html.twig', array(
                            'utilisateur' => $user, 'erreurs' => $erreurs));
            }
            $em->persist($user);
            $em->flush();
            $translated = $translated = $this->get('translator')->trans("profil.message_modifier_mot_passe");
            $this->get('session')->getFlashBag()->add('info', $translated);
        }

        return $this->render('UtilisateursBundle:utilisateurs:profil.html.twig', array(
                    'utilisateur' => $user, 'erreurs' => $erreurs));
    }

    /**
     * Displays a form to edit an existing Utilisateurs entity.
     *
     * @Route("/info", name="utilisateurs_infos")
     * @Method({"GET", "POST"})
     */
    public function updateInfoAction(Request $request) {

        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }


        $user = $this->container->get('security.context')->getToken()->getUser();
        $nom = $request->get('nom');
        $prenom = $request->get('prenom');
        $username = $request->get('username');
        $email = $request->get('email');
        $adresse = $request->get('adresse');
        $erreurs = array();
        $erreurs['username'] = 1;
        $erreurs['email'] = 1;
        $erreurs['password'] = 1;


        $em = $this->getDoctrine()->getManager();
        $userExits = $em->getRepository('UtilisateursBundle:Utilisateurs')->findOneByUsername($username);
        if ($username == null) {
            $erreurs['username'] = -2;
        }
        if ($email == null) {
            $erreurs['email'] = -1;
            return $this->render('UtilisateursBundle:utilisateurs:profil.html.twig', array(
                        'utilisateur' => $user, 'erreurs' => $erreurs));
        }
        if ($userExits && $userExits->getId() != $user->getId()) {
            $erreurs['username'] = -1;
            return $this->render('UtilisateursBundle:utilisateurs:profil.html.twig', array(
                        'utilisateur' => $user, 'erreurs' => $erreurs));
        } else {
            $userExits = $em->getRepository('UtilisateursBundle:Utilisateurs')->findOneByEmail($email);
            if ($userExits && $userExits->getId() != $user->getId()) {
                $erreurs['email'] = 1;
            } else {
                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setUsername($username);
                $user->setAdresse($adresse);
                $user->setEmail($email);
                $em->persist($user);
                $em->flush();
                $translated = $translated = $this->get('translator')->trans("profil.message_modifier");
                $this->get('session')->getFlashBag()->add('info', $translated);
            }
            $erreurs['password'] = 1;
            return $this->render('UtilisateursBundle:utilisateurs:profil.html.twig', array(
                        'utilisateur' => $user, 'erreurs' => $erreurs
            ));
        }
    }

    /**
     * Finds and displays a Utilisateurs entity.
     *
     * @Route("/filtre", name="profilfiltre")
     * @Method("POST")
     */
    public function filtreAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $pole = $request->get('pole');
            $em = $this->getDoctrine()->getManager();
            $pole = $em->getRepository('ParametrageBundle:Pole')->find($pole);
            $profils = $em->getRepository('UtilisateursBundle:Profile')->findByPole($pole);
            $tab = array();
            $i = 0;
            foreach ($profils as $profil) {
                $tab[$i++] = $profil->getNom();
            }
            return new JsonResponse($tab);
        } else
            return new JsonResponse();
    }

    /**
     * @Route("/utilisateur/modification-du-mot-de-passe", name = "utilisateur_profil-updatepassword")
     */
    public function editProfilPasswordAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }


        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);

        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $formFactory = $this->get('fos_user.change_password.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $userCourant = $this->container->get('security.context')->getToken()->getUser();
//            if (!preg_match("#^[\w$@%*+\-_!]{6,15}$#", $user->getPlainPassword())) {
//
//                return $this->render('UtilisateursBundle:Security:editPassword.html.twig', array('form' => $form->createView(), 'password' => '1'));
//            }
            $userCourant->setFirstLog(false);
            $em->persist($userCourant);
            $em->flush();

            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('accueil');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        } else {
            //die(dump($form->getErrorsAsString()));
        }

        return $this->render('UtilisateursBundle:Security:editPassword.html.twig', array('form' => $form->createView(), 'langues' => $lgs, 'password' => '0'));
    }

    /**
     * @Route("/{id}/show",name="utilisateurs_show")
     */
    public function showAction(Utilisateurs $utilisateur) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idCodeLangue = $langue->getId();

        $definedPaysTraduction = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $utilisateur->getPaysResidence(), 'langue' => $langue));


        return $this->render('UtilisateursBundle:utilisateurs:show.html.twig', array('idCodeLangue' => $idCodeLangue, 'utilisateur' => $utilisateur, 'definedPaysTraduction' => $definedPaysTraduction));
    }

    /**
     * @Route("/lire/message",name="message_show")
     */
    public function showMessageAction(Request $request) {
        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();

            $message = $em->getRepository('UtilisateursBundle:Message')->find($request->get('message'));
            if ($message != null) {
                $message->setIsRead(true);
                $em->persist($message);
                $em->flush();
                return new JsonResponse(array('resultat' => '1'));
            } else
                return new JsonResponse(array('resultat' => '-1'));
        }
        else {
            return array('resulat' => '0');
        }
    }

    /**
     * @Route("/{id}/toggle",name="utilisateur_toggle")
     */
    public function toggleAction(Utilisateurs $utilisateur) {
        $em = $this->getDoctrine()->getManager();
        //die("test");
        echo "<script>console.log( 'Debug Objects: ' );</script>";

        if (!$utilisateur->isEnabled()) {
            $utilisateur->setEnabled(true);
            $em->flush();
            $translated = $this->get('translator')->trans('activation_message');
        } else {
            $utilisateur->setEnabled(false);
            $em->flush();
            $translated = $this->get('translator')->trans('desactivation_message');
        }

        echo "<script>console.log( 'Debug Objects: ' );</script>";

        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('utilisateurs_index');
    }

}
