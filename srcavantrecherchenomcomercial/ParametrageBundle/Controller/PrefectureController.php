<?php

namespace ParametrageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BanquemondialeBundle\Entity\Prefecture;
use BanquemondialeBundle\Entity\SousPrefectureCommune;
use BanquemondialeBundle\Entity\Quartier;
use BanquemondialeBundle\Form\PrefectureType;
use BanquemondialeBundle\Form\SousPrefectureType;

class PrefectureController extends Controller {

    public function listerPrefectureAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $em = $this->getDoctrine()->getManager();

        $prefectures = $em->getRepository('BanquemondialeBundle:Prefecture')->findByActif(true); //findAByActif(true);

        return $this->render('ParametrageBundle:Prefecture:lister_prefecture.html.twig', array(
                    'prefectures' => $prefectures,
        ));
    }

    public function ajoutPrefectureAction(Request $request) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $prefecture = new Prefecture();
        $codLang = $request->getLocale();
        $em = $this->getDoctrine()->getManager();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idR = $request->get('idR');
        if (isset($idR) && $idR != 0) {
            $region=$em->getRepository('BanquemondialeBundle:Region')->find($idR);
            $prefecture->setRegion($region);
            //$pays=$region->getPays();
            $paysTraduit = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('langue' => $langue, 'pays' => $region->getPays()->getId()));
            $form = $this->createForm(new PrefectureType(array('locale' => $request->getLocale(), 'paysTraduit' => $paysTraduit)), $prefecture);
        } else {
            $form = $this->createForm(new PrefectureType(array('locale' => $request->getLocale(), 'paysTraduit' => null)), $prefecture);
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($prefecture);
            $em->flush();

            $translated = $translated = $this->get('translator')->trans('prefecture.ajout_success');
            $this->get('session')->getFlashBag()->add('info', $translated);

// return $this->redirectToRoute('departement_show', array('id' => $departement->getId()));
            return $this->redirectToRoute('lister_prefecture');
        }
        return $this->render('ParametrageBundle:Prefecture:ajout_prefecture.html.twig', array(
                    'departement' => $prefecture,
                    'form' => $form->createView(),'idR'=>$idR
        ));
    }

    public function editPrefectureAction(Request $request, $idPerf) {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $codLang = $request->getLocale();
        $em = $this->getDoctrine()->getManager();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $prefecture = $em->getRepository('BanquemondialeBundle:Prefecture')->find($idPerf);
        $paysTraduit = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('langue' => $langue, 'pays' => $prefecture->getRegion()->getPays()->getId()));

        $editForm = $this->createForm(new PrefectureType(array('locale' => $request->getLocale(), 'paysTraduit' => $paysTraduit, 'region' => $prefecture->getRegion()->getId())), $prefecture
        );
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($prefecture);
            $em->flush();
            $translated = $translated = $this->get('translator')->trans('prefecture.modification_succes');
            $this->get('session')->getFlashBag()->add('info', $translated);
            return $this->redirectToRoute('lister_prefecture');
        }

        return $this->render('ParametrageBundle:Prefecture:edit_prefecture.html.twig', array(
                    'prefecture' => $prefecture,
                    'form' => $editForm->createView(),
        ));
    }

    public function deletePrefectureAction(Request $request, Prefecture $prefecture) {
        $em = $this->getDoctrine()->getManager();
        try {
            $prefecture->setActif(false);
            $em->flush();
            $translated = $this->get('translator')->trans('message_suppression_entity_succes');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $translated = $this->get('translator')->trans('message_suppression_entity_fail');
        }



        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('lister_prefecture');
    }

    public function listerSousPrefectureAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $em = $this->getDoctrine()->getManager();

        $sousPrefectures = $em->getRepository('BanquemondialeBundle:SousPrefectureCommune')->findByActif(true);
        return $this->render('ParametrageBundle:Prefecture:lister_sousPrefecture.html.twig', array(
                    'sousPrefectures' => $sousPrefectures
                        ,
        ));
    }

    public function ajoutSousPrefectureAction($idP) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $sousPrefecture = new SousPrefectureCommune();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $em = $this->getDoctrine()->getManager();
        $prefecture = $em->getRepository('BanquemondialeBundle:Prefecture')->find($idP);
//die(dump($prefecture));
//$langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $sousPrefecture->setPrefecture($prefecture);
        $form = $this->createForm(new \BanquemondialeBundle\Form\SousPrefectureType1(array('locale' => $request->getLocale(), 'pref' => $idP)), $sousPrefecture);
//$form->handleRequest($request);
        $region = $prefecture->getRegion();
//die(dump($region));
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $sousPrefecture->setActif(true);
                $em->persist($sousPrefecture);
                $em->flush();

                $translated = $translated = $this->get('translator')->trans('sousPrefecture.ajout_success');
                $this->get('session')->getFlashBag()->add('info', $translated);

// return $this->redirectToRoute('departement_show', array('id' => $departement->getId()));
                return $this->redirectToRoute('lister_sousPrefecture');
            }
        }
        return $this->render('ParametrageBundle:Prefecture:ajout_sousPrefecture.html.twig', array(
                    'sousPrefecture' => $sousPrefecture, 'idR' => $prefecture->getRegion()->getId(), 'idP' => $idP,
                    'form' => $form->createView(),
        ));
    }

    public function newSousPrefectureAction(Request $request) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $sousPrefecture = new SousPrefectureCommune();

        $codLang = $request->getLocale();
        $em = $this->getDoctrine()->getManager();
//$prefecture=$em->getRepository('BanquemondialeBundle:Prefecture')->find($idP);
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $form = $this->createForm(new SousPrefectureType(array('locale' => $request->getLocale(), 'pref' => null)), $sousPrefecture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sousPrefecture->setActif(true);
            $em->persist($sousPrefecture);
            $em->flush();

            $translated = $translated = $this->get('translator')->trans('sousPrefecture.ajout_success');
            $this->get('session')->getFlashBag()->add('info', $translated);

// return $this->redirectToRoute('departement_show', array('id' => $departement->getId()));
            return $this->redirectToRoute('lister_sousPrefecture');
        }
        return $this->render('ParametrageBundle:Prefecture:ajout_sous_prefecture.html.twig', array(
                    'sousPrefecture' => $sousPrefecture,
                    'form' => $form->createView(),
        ));
    }

    public function editSousPrefectureAction(Request $request, $id) {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user->getFirstLog()) {
            return $this->redirectToRoute('utilisateur_profil-updatepassword');
        }
        $codLang = $request->getLocale();
        $em = $this->getDoctrine()->getManager();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $sousPrefecture = $em->getRepository('BanquemondialeBundle:SousPrefectureCommune')->find($id);
        $idP = $sousPrefecture->getPrefecture()->getId();
//$paysTraduit = $em->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('langue' => $langue, 'pays' => $prefecture->getRegion()->getPays()->getId()));

        $editForm = $form = $this->createForm(new \BanquemondialeBundle\Form\SousPrefectureType1(array('locale' => $request->getLocale(), 'pref' => $idP)), $sousPrefecture);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($sousPrefecture);
            $em->flush();
            $translated = $translated = $this->get('translator')->trans('succes_modification');
            $this->get('session')->getFlashBag()->add('info', $translated);
            return $this->redirectToRoute('lister_sousPrefecture');
        }

        return $this->render('ParametrageBundle:Prefecture:edit_sousPrefecture.html.twig', array(
                    'sousPrefecture' => $sousPrefecture,
                    'form' => $editForm->createView(),
        ));
    }

    public function deleteSousPrefectureAction(Request $request, SousPrefectureCommune $sousPrefecture) {
        $em = $this->getDoctrine()->getManager();
        try {
            $sousPrefecture->setActif(false);
            $em->flush();
            $translated = $this->get('translator')->trans('message_suppression_entity_succes');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $translated = $this->get('translator')->trans('message_suppression_entity_fail');
        }



        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('lister_sousPrefecture');
    }

    public function chargerPrefectureAction(Request $request) {
        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();
            $region = $em->getRepository('BanquemondialeBundle:Region')->find($request->get('idR'));
//$regions = $em->getRepository('BanquemondialeBundle:Region')->getRegionByPays2($pays->getId());
            if (!$region)
                return new JsonResponse(array('error' => '1'));

            $prefectures = $em->getRepository('BanquemondialeBundle:Prefecture')->findBy(array('region' => $region, 'actif' => true));
            $retour = array();
            $retourId = array();

            $i = 0;

            foreach ($prefectures as $prefecture) {

                $retour[$i] = $prefecture->getLibelle();
                $retourId[$i] = $prefecture->getId();
                $i++;
            }

            return new JsonResponse(array('retour' => $retour, 'retourId' => $retourId));
        } else
            return new JsonResponse();
    }

    public function chargerProfilAction(Request $request) {
        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();
            $pole = $em->getRepository('ParametrageBundle:Pole')->find($request->get('idP'));
//$regions = $em->getRepository('BanquemondialeBundle:Region')->getRegionByPays2($pays->getId());
            if (!$pole)
                return new JsonResponse(array('error' => '1'));

            $profils = $em->getRepository('UtilisateursBundle:Profile')->findBy(array('pole' => $pole));
            $retour = array();
            $retourId = array();

            $i = 0;

            foreach ($profils as $profil) {

                $retour[$i] = $profil->getNom();
                $retourId[$i] = $profil->getId();
                $i++;
            }

            return new JsonResponse(array('retour' => $retour, 'retourId' => $retourId));
        } else
            return new JsonResponse();
    }

    public function chargerEntrepriseAction(Request $request) {
        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();
            $pole = $em->getRepository('ParametrageBundle:Pole')->find($request->get('idP'));
//$regions = $em->getRepository('BanquemondialeBundle:Region')->getRegionByPays2($pays->getId());
            if (!$pole)
                return new JsonResponse(array('error' => '1'));

            $profils = $em->getRepository('BanquemondialeBundle:Entreprise')->findBy(array('pole' => $pole,'actif'=>true));
            $retour = array();
            $retourId = array();

            $i = 0;

            foreach ($profils as $profil) {

                $retour[$i] = $profil->getDenomination();
                $retourId[$i] = $profil->getId();
                $i++;
            }

            return new JsonResponse(array('retour' => $retour, 'retourId' => $retourId));
        } else
            return new JsonResponse();
    }

    public function chargerSousPrefectureAction(Request $request) {
        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();
            $prefecture = $em->getRepository('BanquemondialeBundle:Prefecture')->find($request->get('idP'));
            if (!$prefecture)
                return new JsonResponse(array('error' => '1'));

            $sousPrefectures = $em->getRepository('BanquemondialeBundle:SousPrefectureCommune')->findBy(array('prefecture' => $prefecture, 'actif' => true));
            $retour = array();
            $retourId = array();
            $i = 0;

            foreach ($sousPrefectures as $sousPrefecture) {
                $retour[$i] = $sousPrefecture->getLibelle();
                $retourId[$i] = $sousPrefecture->getId();
                $i++;
            }

            return new JsonResponse(array('retour' => $retour, 'retourId' => $retourId));
        } else
            return new JsonResponse();
    }

    public function chargerQuartierAction(Request $request) {
        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();
            $sousPrefecture = $em->getRepository('BanquemondialeBundle:SousPrefectureCommune')->find($request->get('idSP'));
            if (!$sousPrefecture)
                return new JsonResponse(array('error' => '1'));

            $quartiers = $em->getRepository('BanquemondialeBundle:Quartier')->findBy(array('sousPrefecture' => $sousPrefecture, 'actif' => true));
            $retour = array();
            $retourId = array();
            $i = 0;

            foreach ($quartiers as $quartier) {
                $retour[$i] = $quartier->getLibelle();
                $retourId[$i] = $quartier->getId();
                $i++;
            }

            return new JsonResponse(array('retour' => $retour, 'retourId' => $retourId));
        } else
            return new JsonResponse();
    }
    public function chargerSecteurActiviteAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $codLang = $request->getLocale();
            $em = $this->getDoctrine()->getManager();
            $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

            $categorie = $em->getRepository('ParametrageBundle:CategorieActivite')->find($request->get('idC'));
//$regions = $em->getRepository('BanquemondialeBundle:Region')->getRegionByPays2($pays->getId());
            if (!$categorie)
                return new JsonResponse(array('error' => '1'));

            $secteurs = $em->getRepository('BanquemondialeBundle:SecteurActivite')->findBy(array('categorieActivite' => $categorie));

            $retour = array();
            $retourId = array();

            $i = 0;
            foreach ($secteurs as $secteur) {
//$secteursT = $em->getRepository('ParametrageBundle:SecteurActiviteTraduction')->findOneBy(array('secteurActivite' => $secteur, 'langue' => $langue));
//foreach ($secteursTraduit as $secteurT) {

                $retour[$i] = $secteur->getCode();
                $retourId[$i] = $secteur->getId();
                $i++;
//}
            }

            return new JsonResponse(array('retour' => $retour, 'retourId' => $retourId));
        } else
            return new JsonResponse();
    }

}
