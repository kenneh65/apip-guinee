<?php

namespace UtilisateursBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use BanquemondialeBundle\Entity\Entreprise;
use BanquemondialeBundle\Entity\Particulier;
use BanquemondialeBundle\Entity\Administration;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use UtilisateursBundle\Entity\Utilisateurs;
use UtilisateursBundle\Form\RegistrationFormType;

/**
 * Controller managing the registration
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class RegistrationController extends BaseController {

    public function registerAction(Request $request) {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm(array('locale' => $this->get('request')->getLocale()));
        $passwordGenerated = 'B_' . uniqid();
        $user->setPlainPassword($passwordGenerated);
        $form->setData($user);
        $em = $this->getDoctrine()->getManager();

        $paysDeResidence = $em->getRepository('BanquemondialeBundle:Pays')->findOneByResidence(true);

        $form->handleRequest($request);
        $erreur = 0;
        if ($form->isValid()) {
            $event = new FormEvent($form, $request);

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            //$pays = $em->getRepository('BanquemondialeBundle:Pays')->findOneByResidence(true);
            // $user->setPaysResidence($pays);

            $type = $user->getType();
            $type = 'particulier';
            $entreprise = null;
            if ($type == 'entreprise') {
                $user->setParticulier(null);
                $entreprise = $user->getEntreprise();
                $user->getEntreprise()->setAdministrateur($user);
                $user->setPole(null);
                $user->setRoles(array('ROLE_ADMIN'));
            } else
            if ($type == 'particulier') {

                $user->setPole(null);

                //$paysSaisi = $request->get('fos_user_registration_form_paysResidence');
                $paysSaisi = $request->request->all()['fos_user_registration_form']['paysResidence'];
                //die(dump($paysSaisi));
                if ($paysDeResidence->getId() != $paysSaisi) {
                    $poleDispora = $em->getRepository('ParametrageBundle:Pole')->getPoleByName('diaspora');
                    $user->setPole($poleDispora);
                    $profilUser = $em->getRepository('UtilisateursBundle:Profile')->findOneBy(array('pole' => $poleDispora->getId()));
                    $user->setProfile($profilUser);
                } else {
                    $polePromoteur = $em->getRepository('ParametrageBundle:Pole')->findOneBy(array('sigle' => 'INV'));
                    $user->setPole($polePromoteur);
                    $profilUser = $em->getRepository('UtilisateursBundle:Profile')->findOneBy(array('pole' => $polePromoteur->getId()));
                    $user->setProfile($profilUser);
                }

                $user->setEntreprise(null);
                $user->setRoles(array('ROLE_CREATEUR'));
            }

            $userManager->updateUser($user);
            $tokenGenerator = $this->get('fos_user.util.token_generator');

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }

			 $this->get('fos_user.user_manager')->updateUser($user);
			 
            //$url = $this->generateUrl('fos_user_registration_confirm', array('token' => $user->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);

			//changement de l'url envoyé par mail
			$nomDeDomaine = $em->getRepository('ParametrageBundle:Chemins')->find(3)->getNom();			
			$originalUrl = $this->generateUrl('fos_user_registration_confirm', array('token' => $user->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_PATH);
			//on recupere le texte apres l'occurence utilisateur puis on l'ajoute au nom de domaine prit dans la bdd
			$subUrl = substr($originalUrl, strpos($originalUrl, "utilisateurs")+strlen("utilisateurs"));
			$url = $nomDeDomaine."/".$request->getLocale()."/utilisateurs".$subUrl;
			//die(dump($newUrl));
			
            $messagerie = $em->getRepository('ParametrageBundle:Messagerie')->find(1);

            if ($messagerie && $messagerie->getMailerHost() != "0") {

                $transport = \Swift_SmtpTransport::newInstance($messagerie->getMailerHost(), $messagerie->getMailerPort())
                                ->setUsername($messagerie->getMailerUser())
                                ->setPassword($messagerie->getMailerPassword())->setEncryption($messagerie->getEncryption());

                $mailer = \Swift_Mailer::newInstance($transport);

                $message = \Swift_Message::newInstance($transport);
                $translated = $translated = $this->get('translator')->trans("utilisateur.confirmation_incription");

                $message->setSubject($translated)
                        ->setFrom(array($messagerie->getExpediteurEmail() => $messagerie->getExpediteurName()))
                        ->setTo($user->getEmail())
                        ->setBody(
                                $this->renderView(
                                        'ParametrageBundle:Parametrage:email\confirmeInscription.email.twig', array('user' => $user, 'password' => $passwordGenerated, 'confirmationUrl' => $url, 'logo' => $message->embed(\Swift_Image::fromPath('front-office/gainde2000.jpg')))
                                ), 'text/html'
                );

                $mailer->send($message);
            }

            $erreur_denomination = '0';
            $erreur_ninea = '0';
            if ($type == 'entreprise') {
                $em = $this->getDoctrine()->getManager();

                if ($entreprise->getNinea() == null) {
                    $erreur_ninea = '2';
                } else {
                    $entrep = $em->getRepository('BanquemondialeBundle:Entreprise')->findOneByNinea($entreprise->getNinea());
                    if ($entrep != null)
                        $erreur_ninea = '1';
                }
                if ($entreprise->getDenomination() == null) {
                    $erreur_denomination = '2';
                } else {
                    $entrep = $em->getRepository('BanquemondialeBundle:Entreprise')->findOneByNinea($entreprise->getDenomination());
                    if ($entrep != null)
                        $erreur_denomination = '1';
                }

                if ($erreur_ninea == '0' and $erreur_denomination == '0') {

                    $em->persist($entreprise);
                    $em->flush();
                } else {
                    $translated = $this->get('translator')->trans('utilisateur.subscribe_error');
                    $this->get('session')->getFlashBag()->add('error', $translated);
                    return $this->render('FOSUserBundle:Registration:register.html.twig', array(
                                'form' => $form->createView(), 'erreur_ninea' => $erreur_ninea, 'erreur_denomination' => $erreur_denomination
                    ));
                }
            }


            $message = $this->get('translator')->trans('corps_sms_inscription');
            $indicateur = $em->getRepository('ParametrageBundle:Chemins')->find(2);
            $telephone = '' . $indicateur->getNom() . '' . $user->getTelephone();


            //$this->sendSMS($request->getLocale(), $telephone, $user->getUsername(), $passwordGenerated);

            $session = new Session();

            $session->set('fos_user_send_confirmation_email/email', $user->getEmail());


            $url = $this->generateUrl('fos_user_registration_check_email');

            return new RedirectResponse($url);
        }

        $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach ($langues as $langue) {
            $lgs [] = $langue->getCode();
        }


        return $this->render('FOSUserBundle:Registration:register.html.twig', array(
                    'form' => $form->createView(), 'erreur_ninea' => '0', 'erreur_denomination' => '0', 'langues' => $lgs
        ));
    }

    public function editAction(Utilisateurs $utilisateur) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $paysTraduit = null;
        if ($utilisateur->getPaysResidence()) {
            $paysTraduit = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('langue' => $langue, 'pays' => $utilisateur->getPaysResidence()->getId()));
        }
        $form = $this->createForm(new RegistrationFormType(array('locale' => $request->getLocale(), 'paysTraduit' => $paysTraduit)), $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //$form->bind($request);
            if($utilisateur->getPole() && $utilisateur->getPole()->getSigle()=="DP"){
                $userProfil=$em->getRepository('UtilisateursBundle:Profile')->findOneBy( array('pole'=>$utilisateur->getPole()));
                $utilisateur->setProfile($userProfil);
            }else{
                $userProfil=$em->getRepository('UtilisateursBundle:Profile')->findOneBy( array('pole'=>$utilisateur->getPole()));
                $utilisateur->setProfile($userProfil);
            }
            $em->persist($utilisateur);
            $em->flush();
            return $this->redirectToRoute('utilisateurs_index');
        }
        return $this->render('UtilisateursBundle:Registration:edit.html.twig', array(
                    'form' => $form->createView(), 'utilisateur' => $utilisateur
        ));
    }

    private function sendSMS($langue, $telephone, $username, $password) {

        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Accept-language: fr\r\n"
            )
        );

        $retour = "";

        $context = stream_context_create($opts);


        /*
          if ($langue == 'fr') {
          $sms = 'Merci+de+confirmer+votre+inscription.+Login+:+' . $username . '+Mot+de passe+:+' . $password;
          $url = "http://192.168.1.164/sms/traitersms.php?username=kannel&password=kannel&to=" . $telephone . "&text=" . $sms . "&sms_platform=4&signature=BRTS";
          } else {
          $sms = 'Please confirm your registration. Login : ' . $username . ' Password : ' . $password;
          $url = "http://192.168.1.164/sms/traitersms.php?username=kannel&password=kannel&to=" . $telephone . "&text=" . $sms . "&sms_platform=4&signature=SySCE";
          }
         */
        $sms = $this->get('translator')->trans('texte_sms_inscription');

        if ($langue == 'fr') {
            $url = "http://192.168.1.164/sms/traitersms.php?username=kannel&password=kannel&to=" . $telephone . "&text=" . $sms . "&sms_platform=4&signature=SySCE";
        } else {
            $url = "http://192.168.1.164/sms/traitersms.php?username=kannel&password=kannel&to=" . $telephone . "&text=" . $sms . "&sms_platform=4&signature=BRTS";
        }

        /*
          if ($langue == 'fr') {
          $sms = 'SySCE.+Login+:+' . $username . '+Mdp+:+' . $password;
          $url = "http://192.168.1.164/sms/traitersms.php?username=kannel&password=kannel&to=" . $telephone . "&text=" . $sms . "&sms_platform=4&signature=SySCE";
          } else {
          $sms = 'BRTS.+Login+:+' . $username . '+Pwd+:+' . $password;
          $url = "http://192.168.1.164/sms/traitersms.php?username=kannel&password=kannel&to=" . $telephone . "&text=" . $sms . "&sms_platform=4&signature=BRTS";
          }
         */
        //die(dump($url));
        try {
            $retour = file_get_contents($url, false, $context);
        } catch (\Exception $e) {
            //\Doctrine\Common\Util\Debug::dump($e);
        }
        return $retour;
    }

}
