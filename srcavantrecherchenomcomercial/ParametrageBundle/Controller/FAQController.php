<?php

namespace ParametrageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ParametrageBundle\Entity\FAQ;
use ParametrageBundle\Form\FAQType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ParametrageBundle\Entity\FAQTraduction;

/**
 * FAQ controller.
 *
 * @Route("/portails/faq") 
 * @Security("has_role('ROLE_USER')")
 */
class FAQController extends Controller {

    /**
     * Lists all FAQ entities.
     *
     * @Route("/", name="faq_index")
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
            $fAQs = $em->getRepository('ParametrageBundle:FAQ')->findAll();
        } else
            $fAQs = $em->getRepository('ParametrageBundle:FAQTraduction')->findByLangue($langue);

        return $this->render('ParametrageBundle:FAQ:index.html.twig', array(
                    'fAQs' => $fAQs,
        ));
    }

    /**
     * Creates a new FAQ entity.
     *
     * @Route("/new", name="faq_new")
     */
    public function newAction(Request $request) {
        $fAQ = new FAQ();
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('BanquemondialeBundle:Langue');
        $langues = $repository->getOtherLanguages('fr');
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode('fr');
        foreach ($langues as $l) {
            $trad = new FAQTraduction();
            $trad->setLangue($l);
            $trad->setFaq($fAQ);
            $fAQ->addTraduction($trad);
        }
        $form = $this->createCreateForm($fAQ);
        $form->handleRequest($request);
        $erreur_question = '';
        $erreur_reponse = '';
        if ($form->isValid()) {
            $i = 1;
            foreach ($fAQ->getTraduction() as $traduction) {
                $question = $request->get('question' . $i);
                $reponse = $request->get('reponse' . $i);
                $traduction->setQuestion($question);
                $traduction->setReponse($reponse);

                if ($question == '') {
                    $translated = $this->get('translator')->trans('question_nulle');
                    $erreur_question = $translated;
                }
                if ($reponse == '') {
                    $translated = $this->get('translator')->trans('reponse_nulle');
                    $erreur_reponse = $translated;
                }
                if ($erreur_reponse != '' or $erreur_question != '') {
                    $translated = $this->get('translator')->trans('faq.add_error');
                    $this->get('session')->getFlashBag()->add('error', $translated);
                    return $this->render('ParametrageBundle:FAQ:new.html.twig', array(
                                'form' => $form->createView(),
                                'entity' => $fAQ,
                                'erreur_question' => $erreur_question,
                                'erreur_reponse' => $erreur_reponse,
                                'langue' => $langue
                    ));
                }
                $i++;
            }


            $fAQ->setAdresseIp($this->container->get('request')->getClientIp());
            $fAQ->setDateModification(new \DateTime());
            $em->persist($fAQ);
            $em->flush();
            return $this->redirectToRoute('faq_index');
        }
        return $this->render('ParametrageBundle:FAQ:new.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $fAQ,
                    'erreur_question' => $erreur_question,
                    'erreur_reponse' => $erreur_reponse,
                    'langue' => $langue
        ));
    }

    /**
     * Finds and displays a FAQ entity.
     *
     * @Route("/show", name="faq_show")
     */
    public function showAction(Request $request) {

        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $fAQ = $this->getDoctrine()->getManager()->getRepository('ParametrageBundle:FAQ')->find($id);
            return new JsonResponse(array('resultat' => '1',
                'question' => $fAQ->getQuestion(),
                'reponse' => $fAQ->getReponse()));
        }
        return new JsonResponse(array('resultat' => '0'));
    }

    /**
     * Displays a form to edit an existing FAQ entity.
     *
     * @Route("/edit/{id}", name="faq_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, FAQ $fAQ) {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('BanquemondialeBundle:Langue');
        $langues = $repository->getOtherLanguages('fr');
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode('fr');

        $form = $this->createCreateForm($fAQ);
        $deleteForm = $this->createDeleteForm($fAQ);

        $form->handleRequest($request);
        $erreur_question = '';
        $erreur_reponse = '';
        if ($form->isValid()) {
            $i = 1;
            foreach ($fAQ->getTraduction() as $traduction) {
                $question = $request->get('question' . $i);
                $reponse = $request->get('reponse' . $i);
                $traduction->setQuestion($question);
                $traduction->setReponse($reponse);
                if ($question == '') {
                    $translated = $this->get('translator')->trans('question_nulle');
                    $erreur_question = $translated;
                }
                if ($reponse == '') {
                    $translated = $this->get('translator')->trans('reponse_nulle');
                    $erreur_reponse = $translated;
                }
                if ($erreur_reponse != '' or $erreur_question != '') {
                    $translated = $this->get('translator')->trans('faq.add_error');
                    $this->get('session')->getFlashBag()->add('error', $translated);
                    return $this->render('ParametrageBundle:FAQ:new.html.twig', array(
                                'form' => $form->createView(),
                                'delete_form' => $deleteForm->createView(),
                                'entity' => $fAQ,
                                'erreur_question' => $erreur_question,
                                'erreur_reponse' => $erreur_reponse,
                                'langue' => $langue
                    ));
                }
                $i++;
            }


            $fAQ->setAdresseIp($this->container->get('request')->getClientIp());
            $fAQ->setDateModification(new \DateTime());
            $em->flush();
            return $this->redirectToRoute('faq_index');
        }
        return $this->render('ParametrageBundle:FAQ:edit.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $fAQ,
                    'erreur_question' => $erreur_question,
                    'erreur_reponse' => $erreur_reponse,
                    'langue' => $langue
        ));
    }

    /**
     * Deletes a FAQ entity.
     *
     * @Route("/delete/{id}", name="faq_delete")
     *  * @Method("DELETE")

     */
    public function deleteAction(Request $request, FAQ $fAQ) {
        $form = $this->createDeleteForm($fAQ);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
             $em->remove($fAQ);
            $em->flush();
            $translated = $this->get('translator')->trans('faq.delete_success');
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirect($this->generateUrl('faq_index'));
        } else
            return $this->redirectToRoute('faq_edit', array('id' => $fAQ->getId()));
    }

    /**
     * Creates a form to delete a FAQ entity.
     *
     * @param FAQ $fAQ The FAQ entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(FAQ $fAQ) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('faq_delete', array('id' => $fAQ->getId())))
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
    private function createCreateForm(FAQ $entity) {
        $form = $this->createForm(new FAQType(), $entity);


        return $form;
    }

}
