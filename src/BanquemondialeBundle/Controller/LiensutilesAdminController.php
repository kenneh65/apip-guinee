<?php

namespace BanquemondialeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BanquemondialeBundle\Entity\Liensutiles;
use BanquemondialeBundle\Form\LiensutilesType;
use BanquemondialeBundle\Entity\LiensutilesTraduction;

/**
 * Produits controller.
 *
 */
class LiensutilesAdminController extends Controller {

    /**
     * Lists all Produits entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($this->get('request')->getLocale());
        if ( strtolower($langue->getCode())== 'fr') {
            $entities = $em->getRepository('BanquemondialeBundle:Liensutiles')->findAll();
        } else
            $entities = $em->getRepository('BanquemondialeBundle:LiensutilesTraduction')->findByLangue($langue);
//        $entities = $this->get('knp_paginator')->paginate($entities, $this->getRequest('request')->query->get('page', 1), 3);

        return $this->render('BanquemondialeBundle:Default:Liensutiles/layout/index.html.twig', array(
                    'entities' => $entities
        ));
    }

    /**
     * Creates a new Produits entity.
     *
     */
    public function createAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('BanquemondialeBundle:Langue');
        $langues = $repository->getOtherLanguages('fr');
        $entity = new Liensutiles();
        foreach ($langues as $l) {
            $trad = new LiensutilesTraduction();
            $trad->setLien($entity);
            $trad->setLangue($l);
            $trad->setLien($entity);
            $entity->addTraduction($trad);
        }
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $erreur_url = '';
        $erreur_titre = '';
        $erreur_description = '';
        if ($form->isValid()) {
            $i = 1;
            foreach ($entity->getTraduction() as $traduction) {
                $url = $request->request->get('url' . $i);
                $description = $request->request->get('description' . $i);
                $titre = $request->request->get('titre' . $i);
                $traduction->setUrl($url);
                $traduction->setLien($entity);
                $traduction->setTitre($titre);
                $traduction->setDescription($description);

                if (!$url) {
                    $translated = $this->get('translator')->trans('lien_url_erreur');
                    $erreur_url = $translated;
                } else

                if (!$description) {
                    $translated = $this->get('translator')->trans('lien_description_erreur');
                    $erreur_description = $translated;
                } else
                if (!$titre) {
                    $translated = $this->get('translator')->trans('lien_titre_erreur');
                    $erreur_titre = $translated;
                }
                if ($erreur_url != '' or $erreur_titre != '' or $erreur_description != '') {
                    $translated = $this->get('translator')->trans('liens.add_error');
                    $this->get('session')->getFlashBag()->add('error', $translated);


                    return $this->render('BanquemondialeBundle:Default:Liensutiles/layout/new.html.twig', array(
                                'entity' => $entity,
                                'form' => $form->createView(),
                                'erreur_titre' => $erreur_titre,
                                'erreur_description' => $erreur_description,
                                'erreur_url' => $erreur_url,
                                'langues' => $langues,
                                'langue' => $repository->findOneByCode('fr')));
                }
                $i++;
            }
            $em->persist($entity);
            $em->flush();
            $translated = $translated = $this->get('translator')->trans('lien.add_success');
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirect($this->generateUrl('adminLiensutiles'));
        }
        return $this->render('BanquemondialeBundle:Default:Liensutiles/layout/new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'erreur_titre' => '',
                    'erreur_description' => '',
                    'erreur_url' => '',
                    'langues' => $langues,
                    'langue' => $repository->findOneByCode('fr')
        ));
    }

    /**
     * Creates a form to create a Produits entity.
     *
     * @param Produits $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Liensutiles $entity) {
        $form = $this->createForm(new LiensutilesType(), $entity);

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Produits entity.
     *
     */
    public function newAction() {
        $entity = new Liensutiles();
        $form = $this->createCreateForm($entity);
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('BanquemondialeBundle:Langue');
        $langues = $em->getRepository('BanquemondialeBundle:Langue')->getOtherLanguages('fr');
        foreach ($langues as $l) {
            $trad = new LiensutilesTraduction();
            $trad->setLien($entity);
            $trad->setLangue($l);
            $trad->setLien($entity);
            $entity->addTraduction($trad);
        }
        $entities = $em->getRepository('BanquemondialeBundle:Liensutiles')->findAll();

        return $this->render('BanquemondialeBundle:Default:Liensutiles/layout/new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'erreur_titre' => '',
                    'erreur_description' => '',
                    'erreur_url' => '',
                    'langues' => $langues,
                    'langue' => $repository->findOneByCode('fr')
        ));
    }

    /**
     * 
     * Finds and displays a Produits entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BanquemondialeBundle:Liensutiles')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Liensutiles entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BanquemondialeBundle:Default:Liensutiles/layout/show.html.twig', array(
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Produits entity.
     *
     */
    public function editAction(Liensutiles $entity) {
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('BanquemondialeBundle:Langue');


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Liens utiles entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($entity);
        return $this->render('BanquemondialeBundle:Default:Liensutiles/layout/edit.html.twig', array(
                    'entity' => $entity,
                   'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'erreur_titre' => '',
                    'erreur_description' => '',
                    'erreur_url' => '',
                    'langue' => $repository->findOneByCode('fr')
        ));
    }

    /**
     * Creates a form to edit a Produits entity.
     *
     * @param Produits $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Liensutiles $entity) {
        $form = $this->createForm(new LiensutilesType(), $entity);

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Produits entity.
     *
     */
    public function updateAction(Request $request, Liensutiles $entity) {
        $em = $this->getDoctrine()->getManager();

        $langues = $em->getRepository('BanquemondialeBundle:Langue')->getOtherLanguages($request->getLocale());
        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $erreur_url = '';
        $erreur_titre = '';
        $erreur_description = '';

        if ($editForm->isValid()) {
            $lien = $entity->getTraduction();
            $translated = $translated = $this->get('translator')->trans('message_lien_modifier');
            $this->get('session')->getFlashBag()->add('info', $translated);
            $i = 1;
            foreach ($entity->getTraduction() as $trad) {
                $url = $request->request->get('url' . $i);
                $description = $request->request->get('description' . $i);
                $titre = $request->request->get('titre' . $i);
                if (!$url) {
                    $translated = $this->get('translator')->trans('message_lien_ajoute');
                    $erreur_url = $translated;
                } else

                if (!$description) {
                    $translated = $this->get('translator')->trans('message_lien_ajoute');
                    $erreur_description = $translated;
                } else
                if (!$titre) {
                    $translated = $this->get('translator')->trans('message_lien_ajoute');
                    $erreur_titre = $translated;
                }
                if ($erreur_url != '' or $erreur_titre != '' or $erreur_description != '') {
                    $translated = $this->get('translator')->trans('liens.update_error');
                    $this->get('session')->getFlashBag()->add('error', $translated);

                    return $this->render('BanquemondialeBundle:Default:Liensutiles/layout/new.html.twig', array(
                                'entity' => $entity,
                                'form' => $form->createView(),
                                'erreur_titre' => $erreur_titre,
                                'erreur_description' => $erreur_description,
                                'erreur_url' => $erreur_url,
                                'langues' => $langues));
                }
                $trad->setUrl($url);
                $trad->setLien($entity);
                $trad->setTitre($titre);
                $trad->setDescription($description);
                $i++;
            }
            $em->flush();

            return $this->redirect($this->generateUrl('adminLiensutiles'));
        }
        $entities = $em->getRepository('BanquemondialeBundle:Liensutiles')->findAll();
        return $this->render('BanquemondialeBundle:Default:Liensutiles/layout/edit.html.twig', array(
                    'entity' => $entity,
                    'entities' => $entities,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'erreur_titre' => '',
                    'erreur_description' => '',
                    'erreur_url' => '',
                    'langues' => $langues
        ));
    }

    /**
     * Deletes a Produits entity.
     *
     */
    public function deleteAction(Request $request,  Liensutiles $entity) {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
                      $translated = $this->get('translator')->trans('liens.delete_success');
            $this->get('session')->getFlashBag()->add('info', $translated);


            $em->remove($entity);
            $em->flush();
             return $this->redirect($this->generateUrl('adminLiensutiles'));
        }
        else
        {
                  $translated = $this->get('translator')->trans('liens.delete_error');
                    $this->get('session')->getFlashBag()->add('error', $translated);
                    return $this->redirectToRoute("adminLiensutiles_edit",array('id'=>$entity->getId()));
        }

       
    }

    /**
     * Creates a form to delete a ocumentation entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($entity) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('adminLiensutiles_delete', array('id' => $entity->getId())))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
