<?php

namespace ParametrageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ParametrageBundle\Entity\News;
use ParametrageBundle\Entity\NewsTraduction;
use ParametrageBundle\Form\NewsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormError;

/**
 * news controller.
 *
 * @Route("/portails/news")
 * @Security("has_role('ROLE_USER')")
 */
class NewsController extends Controller {

    /**
     * Lists all news entities.
     *
     * @Route("/", name="news_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($this->get('request')->getLocale());
        if ($this->get('request')->getLocale() == 'fr') {
            $news = $em->getRepository('ParametrageBundle:News')->findAll();
        } else
            $news = $em->getRepository('ParametrageBundle:NewsTraduction')->findByLangue($langue);

        return $this->render('ParametrageBundle:news:index.html.twig', array(
                    'news' => $news,
        ));
    }

    /**
     * Creates a new news entity.
     *
     * @Route("/new", name="news_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('BanquemondialeBundle:Langue');
        $langues = $repository->getOtherLanguages('fr');
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode('fr');
        $news = new news();

        foreach ($langues as $l) {
            $trad = new NewsTraduction();
            $trad->setLangue($l);
            $trad->setNews($news);
            $news->addTraduction($trad);
        }
        $form = $this->createForm('ParametrageBundle\Form\NewsType', $news);
        $form->handleRequest($request);
        $erreur_titre = '';
        $erreur_contenu = '';

		if ($request->getMethod() == 'POST') {
			
			if($news->getContenu()==null)
				{
					$translated = $this->get('translator')->trans('renseigner_contenu');
					$form->get('contenu')->addError(new FormError($translated));
				}
				
			if ($form->isSubmitted() && $form->isValid()) {
				
				
				
				$i = 1;
				foreach ($news->getTraduction() as $traduction) {

					$titre = $request->get('titre' . $i);
					$contenu = $request->get('contenu' . $i);
					if ($titre == '') {
						$translated = $this->get('translator')->trans('titre_nulle');
						$erreur_titre = $translated;
					}
					if ($contenu == '') {
						$translated = $this->get('translator')->trans('contenu_nulle');
						$erreur_contenu = $translated;
					}
					if ($erreur_titre != '' or $erreur_contenu != '') {
						$translated = $this->get('translator')->trans('news.add_error');
						$this->get('session')->getFlashBag()->add('error', $translated);
						return $this->render('ParametrageBundle:news:new.html.twig', array(
									'form' => $form->createView(),
									'entity' => $news,
									'erreur_titre' => $erreur_titre,
									'erreur_contenu' => $erreur_contenu,
									'langue' => $langue
						));
					}
					$i++;
				}


				$news->setDateModification(new \Datetime());
				$news->setAdresseIp($this->container->get('request')->getClientIp());

				$em->persist($news);
				$em->flush();
				$translated = $translated = $this->get('translator')->trans('news.message_ajouter');
				$this->get('session')->getFlashBag()->add('info', $translated);

				return $this->redirectToRoute('news_index');
			}
		}
        return $this->render('ParametrageBundle:news:new.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $news,
                    'erreur_titre' => $erreur_titre,
                    'erreur_contenu' => $erreur_contenu,
                    'langue' => $langue
        ));
    }

    /**
     * Finds and displays a news entity.
     *
     * @Route("/{id}", name="news_show")
     * @Method("GET")
     */
    public function showAction(News $page) {
        $deleteForm = $this->createDeleteForm($page);

        return $this->render('ParametrageBundle:news:show.html.twig', array(
                    'page' => $page,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/{id}/edit", name="news_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, News $news) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('BanquemondialeBundle:Langue');
        $langues = $repository->getOtherLanguages('fr');
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode('fr');
        $editForm = $this->createForm('ParametrageBundle\Form\NewsType', $news);
        $editForm->handleRequest($request);
        $deleteForm = $this->createDeleteForm($news);


        $erreur_titre = '';
        $erreur_contenu = '';

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $i = 1;
            foreach ($news->getTraduction() as $traduction) {

                $titre = $request->get('titre' . $i);
                $contenu = $request->get('contenu' . $i);
                $traduction->setTitre($titre);
                $traduction->setContenu($contenu);
                if ($titre == '') {
                    $translated = $this->get('translator')->trans('titre_nulle');
                    $erreur_titre = $translated;
                }
                if ($contenu == '') {
                    $translated = $this->get('translator')->trans('contenu_nulle');
                    $erreur_contenu = $translated;
                }
                if ($erreur_titre != '' or $erreur_contenu != '') {
                    $translated = $this->get('translator')->trans('news.add_error');
                    $this->get('session')->getFlashBag()->add('error', $translated);
                    return $this->render('ParametrageBundle:news:edit.html.twig', array(
                                'form' => $editForm->createView(),
                                'delete_form' => $deleteForm->createView(),
                                'entity' => $news,
                                'erreur_titre' => $erreur_titre,
                                'erreur_contenu' => $erreur_contenu,
                                'langue' => $langue
                    ));
                }
                $i++;
            }
            $news->setDateModification(new \Datetime());
            $news->setAdresseIp($this->container->get('request')->getClientIp());

            $em->persist($news);
            $em->flush();
            $translated = $translated = $this->get('translator')->trans('news.message_modifier');
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirectToRoute('news_index');
        }

        return $this->render('ParametrageBundle:news:edit.html.twig', array(
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'entity' => $news,
                    'erreur_titre' => $erreur_titre,
                    'erreur_contenu' => $erreur_contenu,
                    'langue' => $langue
        ));
    }

    /**
     * Deletes a news entity.
     *
     * @Route("/delete/{id}", name="news_delete")
     *  * @Method("DELETE")
     */
    public function deleteAction(Request $request,News $news) {
        $form = $this->createDeleteForm($news);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($news);
            $em->flush();
            $translated = $this->get('translator')->trans('news.delete_success');
            $this->get('session')->getFlashBag()->add('info', $translated);

            return $this->redirect($this->generateUrl('news_index'));
        } else
            return $this->redirectToRoute('news_edit', array('id' => $news->getId()));
    }

    /**
     * Displaye a Page .
     *
     * @Route("/display/{id}", name="news_display")
     * @Method("GET")
     */
    public function displayAction(News $news) {
        if (!$news) {
            throw $this->createNotFoundException("Cette page n'existe pas!");
        }

        return $this->render("ParametrageBundle:news:display.html.twig", array('news' => $news));
    }

    /**
     * Creates a form to delete a news entity.
     *
     * @param news $page The news entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(News $news) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('news_delete', array('id' => $news->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
