<?php

namespace DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use DefaultBundle\Form\DiscussionType;
use DefaultBundle\Entity\Message;
use DefaultBundle\Entity\Discussion;
use Symfony\Component\HttpFoundation\JsonResponse;

class MessageController extends Controller {

    /**

     * @Route("/{_locale}/messages/recus",name="mes-messages")

     */
    public function mesMessagesAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($user && $user->getFirstLog()) {


            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }


        $discussions = $user->getDiscussionsNonBloquer();

        //$em->flush();

        return $this->render('DefaultBundle:messages:messages.html.twig', array('discussions' => $discussions));
    }

    /**

     * @Route("/{_locale}/messages/envoyés",name="mes_messages_envoyer")

     */
    public function mesMessagesRecusAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user->getFirstLog()) {

            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }

        $messagesEnvoyee = $user->getEmailsEnvoyesNonBloquer();
        return $this->render('DefaultBundle:messages:messagesSent.html.twig', array('messages' => $messagesEnvoyee));
    }

    /**

     * @Route("/{_locale}/message/bloquer/{id}", name = "message_bloquer",requirements={"id" = "\d+"})

     */
    public function bloquerMessageAction($id) {

        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('DefaultBundle:Message')->find($id);

        $user = $this->container->get('security.context')->getToken()->getUser();

       if ($user->getFirstLog()) {


            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        if ($message->getDeleteBy() == null) {
            $message->setIsLocked(true);
            $message->setDeleteBy($user);
        } else
        if ($message->getDeleteBy() != $user) {
            $em->remove($message);
        }
          $translated = $this->get('translator')->trans('message.supression_sucess');
            $this->get('session')->getFlashBag()->add('info', $translated);
        $em->flush();
        return $this->redirectToRoute('mes-messages', array('messages' => $user->getDiscussionsNonBloquer()));
    }
    
     /**

     * @Route("/{_locale}/discussion/bloquer/{id}", name = "discussion_bloquer",requirements={"id" = "\d+"})

     */
    public function bloquerDiscussionAction(Discussion $discussion) {

        $em = $this->getDoctrine()->getManager();
      
        $user = $this->container->get('security.context')->getToken()->getUser();

       if ($user->getFirstLog()) {


            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        if ($discussion->getLockedBy() == null) {
            $discussion->setIsLocked(true);
            $discussion->setLockedBy($user);
        } else
        if ($discussion->getDeleteBy() != $user) {
            $em->remove($discussion);
        }
         $translated = $this->get('translator')->trans('discussion.supression_sucess');
            $this->get('session')->getFlashBag()->add('info', $translated);
        $em->flush();
        return $this->redirectToRoute('mes-messages', array('messages' => $user->getDiscussionsNonBloquer()));
    }

    /**

     * @Route("/message/response",name="ajax_reponse")

     */
    public function emailGerantAction(Request $request) {

        if ($request->isXmlHttpRequest()) {

            $user = $this->container->get('security.context')->getToken()->getUser();
            if ($request->get('message') != '') {

                if ($request->get('id') != '') {

                    $em = $this->getDoctrine()->getManager();

                    $discussion = $em->getRepository('DefaultBundle:Discussion')->find($request->get('id'));

                    $userr = null;
                    if ($discussion->getExpediteur() == $user)
                        $userr = $discussion->getDestinataire();
                    else
                        $userr = $discussion->getExpediteur();

                    $pers = null;

                    $pers1 = null;




                    $mess = new Message();
                    $mess->setAuteur($user);
                    $discussion->setIsLocked(false);

                    $mess->setDiscussion($discussion);
                    $discussion->setLockedBy(null);
                    $mess->setContenu($request->get('message'));
                    $em->persist($discussion);
                    $em->persist($mess);
                    $em->flush();
                    $translated = $this->get('translator')->trans('message.envoye_succes');
                    return new JsonResponse(array(
                        'error' => '0',
                        'message' => $translated));
                } else
                    return new JsonResponse(array(
                        'error' => '1',
                        'message' => 'id!'));
            } else {
                $translated = $this->get('translator')->trans('message.reseigner_message');
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => $translated));
            }
        }
        $translated = $this->get('translator')->trans('erreur_survenue');
        return new JsonResponse(array(
            'error' => '1',
            'message' => $translated));
    }

    /**

     * @Route("/{_locale}/messages/nouveau",name="message_new")

     */
    public function newAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user->getFirstLog()) {

            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
		
        $discussion = new Discussion();
        $form = $this->createCreateForm($discussion);
        $form->handleRequest($request);
        $erreur_email = 0;
        $erreur_contenu = 0;
        $destinataire = null;
        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $email = $request->get('destinataire');
			$idPole = $request->get('discussion')['pole'];
			$numeroDossier = $request->get('discussion')['numeroDossier'];
			$listeUtilisateurPole = $em->getRepository('UtilisateursBundle:Utilisateurs')->findByPole($idPole);
			//die(dump($listeUtilisateurPole));
			
			if($listeUtilisateurPole)
			{
				//die(dump($listeUtilisateurPole));
				foreach ($listeUtilisateurPole as $utilisateur) 
				{	
					$destinataire = $utilisateur;
					$discussionTemp = new Discussion();
				
					$discussionTemp->setExpediteur($user);
					$discussionTemp->setDestinataire($destinataire);
					$discussionTemp->setNumeroDossier($numeroDossier);
					$discussionTemp->setObjet($discussion->getObjet());
						
					
					//echo "<script>console.log( 'Debug Objects: '+".$destinataire->getUsername()." );</script>";
					//die("test");
					$contenu = $request->get('contenu');
					if ($contenu == null)
						$erreur_contenu = 1;
					if ($erreur_contenu == 0 and $erreur_email == 0) {
						$message = new Message();
						$message->setContenu($contenu);
						$message->setDiscussion($discussionTemp);
						$message->setAuteur($user);
						
						//$discussion->setExpediteur($user);
						//$discussion->setDestinataire($destinataire);
						//$discussion->setNumeroDossier($numeroDossier);
						$em->persist($discussionTemp);
						$em->persist($message);
						$em->flush();
						
					} else {
						
						$translated = $this->get('translator')->trans('message.envoye_echec');
					$this->get('session')->getFlashBag()->add('error', $translated);
						return $this->render('DefaultBundle:messages:new.html.twig', array('form' => $form->createView(),
									'discussion' => $discussion, 'erreur_contenu' => $erreur_contenu));
					}
				}
					
				
					$translated = $this->get('translator')->trans('message.envoye_succes');
					$this->get('session')->getFlashBag()->add('info', $translated);
					//return $this->redirectToRoute('mes-messages');
					
					
					$discussions = $user->getDiscussionsNonBloquer();
					return $this->render('DefaultBundle:messages:messages.html.twig', array('discussions' => $discussions));
			}
			else{
				
				$translated = $this->get('translator')->trans('message.envoye_echec');
				$this->get('session')->getFlashBag()->add('error', $translated);
			}
			
			
			
        }
        return $this->render('DefaultBundle:messages:new.html.twig', array('form' => $form->createView(),
                    'discussion' => $discussion, 'erreur_contenu' => $erreur_contenu));
    }

    private function createCreateForm(Discussion $entity) {
        $form = $this->createForm(new \DefaultBundle\Form\DiscussionType(), $entity);


        return $form;
    }

    /**

     * @Route("/{_locale}/messages/details/{id}",name="details-discussion")

     */
    public function detailAction(Discussion $discussion) {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!$user) {

            throw $this->createNotFoundException('Aucun utilisateur avec l\'identifiant ' . $id);
        }


        $em = $this->getDoctrine()->getManager();
        $message = $discussion->getMessagesNonLus();
        if ($message and $message->getAuteur() != $user) {

            $message->setIsRead(true);
            $em->persist($message);
            $em->flush();
        }



        return $this->render('DefaultBundle:messages:details-messages.html.twig', array('discussion' => $discussion));
    }

    

}
