<?php

namespace ParametrageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ParametrageBundle\Entity\Contact;
use ParametrageBundle\Entity\ContactTraduction;
use ParametrageBundle\Form\ContactType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Contact controller.
 *
 * @Route("/contact")
 * @Security("has_role('ROLE_USER')")
 */
class ContactController extends Controller {

    /**
     * Lists all Contact entities.
     *
     * @Route("/", name="contact_index")
     * @Method("GET")
     */
    public function indexAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $em = $this->getDoctrine()->getManager();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($this->get('request')->getLocale());

        if ($this->get('request')->getLocale() == 'fr') {
            $contacts = $em->getRepository('ParametrageBundle:Contact')->findAll();
        } else
            $contacts = $em->getRepository('ParametrageBundle:ContactTraduction')->findByLangue($langue);

        return $this->render('ParametrageBundle:contact:index.html.twig', array(
                    'contacts' => $contacts,
        ));
    }

    /**
     * Creates a new Contact entity.
     *
     * @Route("/new", name="contact_new")
     */
    public function newAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $contact = new Contact();

        $repository = $em->getRepository('BanquemondialeBundle:Langue');
        $langues = $repository->getOtherLanguages('fr');
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode('fr');
        foreach ($langues as $l) {
            $contact1 = new ContactTraduction();
            $contact1->setLangue($l);
            $contact->addTraduction($contact1);
        }

        $form = $this->createCreateForm($contact);
        $form->handleRequest($request);
        $erreur_nom = '';
        //$erreur_fonction = '';
        //$erreur_telephone = '';
        //$erreur_email = '';
        //$erreur_adresse = '';
        if ($form->isValid()) {
            $i = 1;
			
			$nom = $request->get('nom' . $i);
			//if (($nom != null) && ($nom != '')) {
            foreach ($contact->getTraduction() as $traduction) {
                /*$contact0 = $request->get('contact');
				$nom = $contact0['nom'];
				$fonction = $contact0['fonction'];
                $adresse = $contact0['adresse'];
                $telephone = $contact0['telephone'];
				$telephone2 = $contact0['telephone2'];
                $email = $contact0['email'];
				$siteWeb = $contact0['siteWeb'];*/
				$nom = $request->get('nom' . $i);
                $fonction = $request->get('fonction' . $i);
                $adresse = $request->get('adresse' . $i);
                $telephone = $request->get('telephone' . $i);
				$telephone2 = $request->get('telephone2' . $i);
                $email = $request->get('email' . $i);
				$siteWeb = $request->get('siteWeb' . $i);
				
                $traduction->setFonction($fonction);
                $traduction->setTelephone($telephone);
				$traduction->setTelephone2($telephone2);
                $traduction->setAdresse($adresse);
                $traduction->setEmail($email);
                $traduction->setNom($nom);
				$traduction->setSiteWeb($siteWeb);

                $traduction->setContact($contact);
                if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) {
                    $translated = $this->get('translator')->trans('email_invalide');
                    $erreur_email = $translated;
                }
                if (!preg_match("#^(7[6780]|3[30])[-. ]?[0-9]{3}([-. ]?[0-9]{2}){2}$#", $telephone)) {
                    $translated = $this->get('translator')->trans('telephone_invalide');
                    $erreur_telephone = $translated;
                }
				if (!preg_match("#^(7[6780]|3[30])[-. ]?[0-9]{3}([-. ]?[0-9]{2}){2}$#", $telephone2)) {
                    $translated = $this->get('translator')->trans('telephone_invalide');
                    $erreur_telephone = $translated;
                }
                if ($nom == null) {
                    $translated = $this->get('translator')->trans('nom_nul');
                    $erreur_nom = $translated;
                }
                /*if ($fonction == null) {
                    $translated = $this->get('translator')->trans('fonction_nulle');
                    $erreur_fonction = $translated;
                }
                if ($adresse == null) {
                    $translated = $this->get('translator')->trans('adresse_nulle');
                    $erreur_adresse = $translated;
                }
				 if ($erreur_adresse != '' or $erreur_nom != '' or $erreur_nom != '' or $erreur_email != '' or $erreur_telephone != '') {
				*/
                if ($erreur_nom != '') {
                    $translated = $this->get('translator')->trans('contact.add_error');
                    $this->get('session')->getFlashBag()->add('error', $translated);

                    return $this->render('ParametrageBundle:Contact:new.html.twig', array(
                                'form' => $form->createView(),
                                'entity' => $contact,
                                'erreur_nom' => $erreur_nom,
                                /*'erreur_fonction' => $erreur_fonction,
                                'erreur_telephone' => $erreur_telephone,
                                'erreur_email' => $erreur_email,
                                'erreur_adresse' => $erreur_adresse,*/
                                'langues' => $langues,
                                'langue' => $langue
                    ));
                }
                $contact->setDateModification(new \DateTime());
            }
			//}
			
            $contact->setAdresseIp($request->getClientIp());
            $em->persist($contact);
            $em->flush();
            $translated = $this->get('translator')->trans('contact.add_success');
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirectToRoute('contact_index');
        }


        return $this->render('ParametrageBundle:Contact:new.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $contact,
                    'erreur_nom' => $erreur_nom,
                    //'erreur_fonction' => $erreur_fonction,
                    //'erreur_telephone' => $erreur_telephone,
                    //'erreur_email' => $erreur_email,
                    //'erreur_adresse' => $erreur_adresse,
                    'langues' => $langues,
                    'langue' => $langue
        ));
    }

    /**
     * Finds and displays a Contact entity.
     *
     * @Route("/show", name="contact_show")
     */
    public function showAction(Request $request) {

        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $contact = $this->getDoctrine()->getManager()->getRepository('ParametrageBundle:Contact')->find($id);
            return new JsonResponse(array('resultat' => '1',
                'nom' => $contact->getNom(),
                'fonction' => $contact->getFonction(),
                'telephone' => $contact->getTelephone(),
                'email' => $contact->getEmail(),
                'adresse' => $contact->getAdresse()));
        }
        return new JsonResponse(array('resultat' => '0'));
    }

    /**
     * Displays a form to edit an existing Contact entity.
     *
     * @Route("/edit/{id}", name="contact_edit")
     * @Method({"GET","POST"})
     */
    public function editAction(Request $request, Contact $contact) {
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createEditForm($contact);
        $deleteForm = $this->createDeleteForm($contact);
        $erreur_nom = '';
        //$erreur_fonction = '';
        //$erreur_telephone = '';
        //$erreur_email = '';
        //$erreur_adresse = '';
        $editForm->handleRequest($request);
        $langues = $em->getRepository('BanquemondialeBundle:Langue')->getOtherLanguages('fr');
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode('fr');


        if ($editForm->isValid()) {
            $i = 1;
			
			$nom = $request->get('nom' . $i);
            
			 //if (($nom != null) && ($nom != '')) {
            foreach ($contact->getTraduction() as $traduction) {

                
				$nom = $request->get('nom' . $i);
				$fonction = $request->get('fonction' . $i);
				$adresse = $request->get('adresse' . $i);
				$telephone = $request->get('telephone' . $i);
				$telephone2 = $request->get('telephone2' . $i);
				$email = $request->get('email' . $i);
				$siteWeb = $request->get('siteWeb' . $i);
				/*$contact0 = $request->get('contact');
				$nom0 = $contact0->getNom();
				$fonction0 = $contact0->getFonction();
                $adresse0 = $contact0->getAdresse();
                $telephone0 = $contact0->getTelephone();
				$telephone20 = $contact0->getTelephone2();
                $email0 = $contact0->getEmail();
				$siteWeb0 = $contact0->getSiteWeb();*/

                $traduction->setFonction($fonction);
                $traduction->setTelephone($telephone);
				$traduction->setTelephone2($telephone2);
                $traduction->setAdresse($adresse);
                $traduction->setEmail($email);
                $traduction->setNom($nom);
				$traduction->setSiteWeb($siteWeb);

                $contact->setDateModification(new \DateTime());
                if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) {
                    $translated = $this->get('translator')->trans('email_invalide');
                    $erreur_email = $translated;
                }
                if (!preg_match("#^(7[6780]|3[30])[-. ]?[0-9]{3}([-. ]?[0-9]{2}){2}$#", $telephone)) {
                    $translated = $this->get('translator')->trans('telephone_invalide');
                    $erreur_telephone = $translated;
                }
                if ($nom == null) {
                    $translated = $this->get('translator')->trans('nom_nul');
                    $erreur_nom = $translated;
                }
                /*if ($fonction == null) {
                    $translated = $this->get('translator')->trans('fonction_nulle');
                    $erreur_fonction = $translated;
                }
                if ($adresse == null) {
                    $translated = $this->get('translator')->trans('adresse_nulle');
                    $erreur_adresse = $translated;
                }
				if ($erreur_adresse != '' or $erreur_nom != '' or $erreur_nom != '' or $erreur_email != '' or $erreur_telephone != '') {
				*/
                if ($erreur_nom != '') {
                    $translated = $this->get('translator')->trans('contact.update_error');
                    $this->get('session')->getFlashBag()->add('error', $translated);

                    return $this->render('ParametrageBundle:Contact:edit.html.twig', array(
                                'edit_form' => $editForm->createView(),
                                'delete_form' => $deleteForm->createView(),
                                'entity' => $contact,
                                'erreur_nom' => $erreur_nom,
                                //'erreur_fonction' => $erreur_fonction,
                                //'erreur_telephone' => $erreur_telephone,
                                //'erreur_email' => $erreur_email,
                                //'erreur_adresse' => $erreur_adresse,
                                'langue' => $langue
                    ));
                }
			}
			//}
            $contact->setAdresseIp($this->container->get('request')->getClientIp());
            $em->flush();
            $translated = $this->get('translator')->trans('succes_modification');
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirectToRoute('contact_index');
        }
        return $this->render('ParametrageBundle:Contact:edit.html.twig', array(
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $contact,
                    'erreur_nom' => $erreur_nom,
                    //'erreur_fonction' => $erreur_fonction,
                    //'erreur_telephone' => $erreur_telephone,
                    //'erreur_email' => $erreur_email,
                    //'erreur_adresse' => $erreur_adresse,
                    'langue' => $langue
        ));
    }

    /**
     * Deletes a Contact entity.
     *
     * @Route("/delete/{id}", name="contact_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Contact $contact) {
        $form = $this->createDeleteForm($contact);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($contact);
            $em->flush();
            $translated = $this->get('translator')->trans('contact.delete_success');
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirect($this->generateUrl('contact_index'));
        } else
            return $this->redirectToRoute('contact_edit', array('id' => $contact->getId()));
    }

    /**
     * Creates a form to delete a Contact entity.
     *
     * @param Contact $contact The Contact entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Contact $contact) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('contact_delete', array('id' => $contact->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * Creates a form to create a Produits entity.
     *
     * @param Produits $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Contact $entity) {
        $form = $this->createForm(new ContactType(), $entity);


        return $form;
    }

    /**
     * Creates a form to edit a Produits entity.
     *
     * @param Produits $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Contact $entity) {
        $form = $this->createForm(new ContactType(), $entity);


        return $form;
    }

}
