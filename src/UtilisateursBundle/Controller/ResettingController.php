<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UtilisateursBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\UserBundle\Controller\ResettingController as BaseController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller managing the resetting of the password
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ResettingController extends BaseController
{
    /**
     * Request reset user password: show form
     */
    public function requestAction()
    {
          $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach($langues as $langue)
        {
            $lgs [] = $langue->getCode();
        }
       
        return $this->render('FOSUserBundle:Resetting:request.html.twig',array('langues'=>$lgs));
    }

    /**
     * Request reset user password: submit form and send email
     */
    public function sendEmailAction(Request $request)
    {
          $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach($langues as $langue)
        {
            $lgs [] = $langue->getCode();
        }
       
        $username = $request->request->get('username');

        /** @var $user UserInterface */
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return $this->render('FOSUserBundle:Resetting:request.html.twig', array(
                'invalid_username' => $username,'langues'=>$lgs
            ));
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return $this->render('FOSUserBundle:Resetting:passwordAlreadyRequested.html.twig',array('langues'=>$lgs));
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }
		
		
		//$url = $this->generateUrl('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);
		
		//changement de l'url envoyé par mail
		$nomDeDomaine = $em->getRepository('ParametrageBundle:Chemins')->find(3)->getNom();			
		$originalUrl = $this->generateUrl('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_PATH);
		//on recupere le texte apres l'occurence utilisateur puis on l'ajoute au nom de domaine prit dans la bdd
		$subUrl = substr($originalUrl, strpos($originalUrl, "utilisateurs")+strlen("utilisateurs"));
		$url = $nomDeDomaine."/".$request->getLocale()."/utilisateurs".$subUrl;
		//die(dump($newUrl));	
			
		$em = $this->getDoctrine()->getManager();
				
		$messagerie = $em->getRepository('ParametrageBundle:Messagerie')->find(1);
		
		$transport = \Swift_SmtpTransport::newInstance($messagerie->getMailerHost(),$messagerie->getMailerPort())
		->setUsername($messagerie->getMailerUser())
		->setPassword($messagerie->getMailerPassword())->setEncryption($messagerie->getEncryption());
		
		$mailer = \Swift_Mailer::newInstance($transport);
		
		$message = \Swift_Message::newInstance($transport);
		$translated = $this->get('translator')->trans("utilisateur.reinitialisation_mot_passe");
		$message->setSubject($translated)
		->setFrom(array($messagerie->getExpediteurEmail() => $messagerie->getExpediteurName()))
		->setTo($user->getEmail())
		->setBody(
			$this->renderView(
				'ParametrageBundle:Parametrage:email\password_resetting.email.twig',
				 array('user' => $user,'confirmationUrl'=>$url,'logo'=>$message->embed(\Swift_Image::fromPath('front-office/LOGO_final.png')))
			),
			'text/html'
		);
		
		$mailer->send($message);/*						
			$message->setSubject('Inscription')
			->setFrom(array('webmaster@bmd.com' => 'Banque mondiale'))
			->setTo($user->getEmail())
			->setBody(
				'<html>' .
				' <head></head>' .
				' <body>' .
				'  <a href = '.$url.'>Cliquz ici</a>'.
				'  Rest of message' .
				' </body>' .
				'</html>',
				  'text/html' // Mark the content-type as HTML
			);
			
		$mailer->send($message);
		
		*/

		
        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);

        return new RedirectResponse($this->generateUrl('fos_user_resetting_check_email',
            array('email' => $this->getObfuscatedEmail($user))
        ));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction(Request $request) {		
        $email = $request->query->get('email');

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('fos_user_resetting_request'));
        }
  $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach($langues as $langue)
        {
            $lgs [] = $langue->getCode();
        }
       
        return $this->render('FOSUserBundle:Resetting:checkEmail.html.twig', array(
            'email' => $email,'langues'=>$lgs
        ));
    }

    /**
     * Reset user password
     */
    public function resetAction(Request $request, $token)
    {		
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);
  $em = $this->getDoctrine()->getManager();
        $langues = $em->getRepository("BanquemondialeBundle:Langue")->findAll();
        $lgs = array();
        foreach($langues as $langue)
        {
            $lgs [] = $langue->getCode();
        }
       
        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);
             if (!preg_match("#^[\w$@%*+\-_!]{8,15}$#", $user->getPlainPassword())) {

                return $this->render('FOSUserBundle:Resetting:reset.html.twig', array(
            'token' => $token,
            'form' => $form->createView(),
                    'password'=>'1','langues'=>$lgs
             ));
                
             }
           $user->setFirstLog(false);
            $userManager->updateUser($user);
            
            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('administration');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('FOSUserBundle:Resetting:reset.html.twig', array(
            'token' => $token,
            'form' => $form->createView(),'password'=>'0','langues'=>$lgs
        ));
    }

    /**
     * Get the truncated email displayed when requesting the resetting.
     *
     * The default implementation only keeps the part following @ in the address.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     *
     * @return string
     */
    protected function getObfuscatedEmail(UserInterface $user)
    {
        $email = $user->getEmail();
        if (false !== $pos = strpos($email, '@')) {
            $email = '...' . substr($email, $pos);
        }

        return $email;
    }
}
