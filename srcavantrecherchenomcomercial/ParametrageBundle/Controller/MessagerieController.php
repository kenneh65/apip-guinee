<?php

namespace ParametrageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ParametrageBundle\Entity\Messagerie;

/**
 * Messagerie controller.
 *
 * @Route("/messagerie")
 */
class MessagerieController extends Controller {
	
	/**
     * @Route("/edit", name="messagerie_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request) {
        $user = $this->container->get('security.context')->getToken()->getUser();
			
		if ($user->getFirstLog())
		{
			return $this->redirectToRoute('utilisateur_profil-updatepassword');		
		}
		
          $em = $this->getDoctrine()->getManager();
          $messageries = $em->getRepository('ParametrageBundle:Messagerie')->findAll();
          $messagerie =null;
          if(count($messageries)>0)
              $messagerie=$messageries[0];
          if($messagerie!=null)
          {
                   $editForm = $this->createForm('ParametrageBundle\Form\MessagerieType', $messagerie);

		$editForm->handleRequest($request);
		   
        if ($editForm->isSubmitted() && $editForm->isValid()) {
          
            $em->persist($messagerie);
            $em->flush();
$translated = $translated = $this->get('translator')->trans("messagerie_modifiee.");
            $this->get('session')->getFlashBag()->add('info', $translated);
            return $this->redirectToRoute('messagerie_edit', array('id' => $messagerie->getId()));
        }

        return $this->render('ParametrageBundle:Parametrage:Messagerie/edit.html.twig', array(
                    'messagerie' => $messagerie,
                    'edit_form' => $editForm->createView()
        ));
          }
          else
              return $this->redirectToRoute('messagerie_new');   
    }
	
	/**
     * @Route("/new", name="messagerie_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
		$user = $this->container->get('security.context')->getToken()->getUser();
			
		if ($user->getFirstLog())
		{
			return $this->redirectToRoute('utilisateur_profil-updatepassword');		
		}
		
		$messagerie =  new Messagerie();

        $form = $this->createForm('ParametrageBundle\Form\MessagerieType', $messagerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($messagerie);
            $em->flush();
              $translated =  $translated = $this->get('translator')->trans('messagerie_ajoutée');
		   $this->get('session')->getFlashBag()->add('info', $translated);
		  
				return $this->redirectToRoute('messagerie_edit', array('id' => $messagerie->getId()));
			}

        return $this->render('ParametrageBundle:Parametrage:Messagerie/new.html.twig', array(
			'messagerie' => $messagerie,
            'form' => $form->createView(),
        ));
    }
	
	/**
     * @Route("/tester", name="messagerie_teste")
     * @Method({"GET", "POST"})
     */
    public function testerAction(Request $request)
    {
		if ($request->getMethod() == 'POST')
		{
			$email = $request->get('email');
			$TextMessage = $request->get('message');
			
			if ($email == '')
				return $this->render('ParametrageBundle:Parametrage:Messagerie/teste.html.twig');	
			
			if ($TextMessage == '')
				return $this->render('ParametrageBundle:Parametrage:Messagerie/teste.html.twig');		
			
			$em = $this->getDoctrine()->getManager();

            $messagerie = $em->getRepository('ParametrageBundle:Messagerie')->find(1);

            $transport = \Swift_SmtpTransport::newInstance($messagerie->getMailerHost(), $messagerie->getMailerPort())
                            ->setUsername($messagerie->getMailerUser())
                            ->setPassword($messagerie->getMailerPassword())->setEncryption($messagerie->getEncryption());

            $mailer = \Swift_Mailer::newInstance($transport);

            $message = \Swift_Message::newInstance($transport);

            $message->setSubject('Test')
                    ->setFrom(array($messagerie->getExpediteurEmail() => $messagerie->getExpediteurName()))
                    ->setTo($email)
					->setBody(
							'<html>' .
								'<head></head>'.
								'<body>'.$TextMessage.'</body>' .
							'</html>',
							'text/html'
						);
				if($messagerie->getMailerHost() != '0')
				{
					$mailer->send($message);
				}
			
			$translated = $translated = $this->get('translator')->trans('message_test_messagerie');
			$this->get('session')->getFlashBag()->add('info', $translated);
		}
		
		return $this->render('ParametrageBundle:Parametrage:Messagerie/teste.html.twig');	
	}
}