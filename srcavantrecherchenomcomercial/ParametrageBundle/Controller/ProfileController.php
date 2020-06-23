<?php

namespace ParametrageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UtilisateursBundle\Entity\ProfileTraduction;
use UtilisateursBundle\Entity\Profile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Departement controller.
 *
 * @Route("/profile")
 * @Security("has_role('ROLE_USER')")
 */
class ProfileController extends Controller {

    /**
     * Lists all Departement entities.
     *
     * @Route("/", name="profile_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $profiles = $em->getRepository('UtilisateursBundle:Profile')->findAll();
        $poles = $em->getRepository("ParametrageBundle:Pole")->findAll();
        $repository = $em->getRepository('UtilisateursBundle:ProfileTraduction');

        return $this->render('ParametrageBundle:Parametrage:Profile/index.html.twig', array(
                    'profiles' => $profiles, 'poles' => $poles,'repository'=>$repository
        ));
    }

    /**
     * @Route("/add", name="add_profile")
     */
    public function addProfileLangueAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $description = $request->get('description');
        $nom = $request->get('nom');
        $pole = $request->get('pole');

        if ($description == '')
            return new JsonResponse(array(
                'error' => '1',
                'message' => 'id not null'));

        if ($nom == '')
            return new JsonResponse(array(
                'error' => '1',
                'message' => 'nom not null'));
        if ($pole == '')
            return new JsonResponse(array(
                'error' => '1',
                'message' => 'pole not null'));
        $profile = new Profile();
        $profile->setDescription($description);
        $profile->setNom($nom);
        $pole = $em->getRepository('ParametrageBundle:Pole')->findOneByNom($pole);
        $profile->setPole($pole);

        $em->persist($profile);
        $em->flush();

        return new JsonResponse(array(
            'error' => '0',
            'message' => 'done'));
    }

    /**
     * @Route("/delete-profile", name="delete_profile")
     */
    public function deleteProfileLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $profile = $em->getRepository('UtilisateursBundle:Profile')->find($request->get('profile'));

            if (!$profile)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'Profile not null'));

            $em->remove($profile);
            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/update-profile", name="update_profile")
     */
    public function updateProfileLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $profile = $em->getRepository('UtilisateursBundle:Profile')->find($request->get('profile'));

            $description = $request->get('description');
            $nom = $request->get('nom');
            $pole = $request->get('pole');
            if ($description == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'description not null'));

            if ($nom == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'nom not null'));
            if ($pole == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'nom not null'));

            $profile->setDescription($description);
            $profile->setNom($nom);
            $pole = $em->getRepository('ParametrageBundle:Pole')->findOneByNom($pole);
            $profile->setPole($pole);

            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/details_profile/{id}", name="details_profile")
     */
    public function detailsProfileLangueAction(Profile $profile) {
        $em = $this->getDoctrine()->getManager();

        $langueTraduit = $em->getRepository('BanquemondialeBundle:Langue')->getLanguesByProfile($profile);

        $langues = $em->getRepository('BanquemondialeBundle:Langue')->findAll();

        return $this->render('ParametrageBundle:Parametrage:Profile/show.html.twig', array(
                    'profile' => $profile, 'langues' => $langues, 'languesTraduit' => $langueTraduit
        ));
    }

    /**
     * @Route("/add-traduction-langue", name="add_traduction_forme_profile")
     */
    public function addTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
            $profile = $em->getRepository('UtilisateursBundle:Profile')->find($request->get('id'));
            $nom = $request->get('nom');
            $description = $request->get('description');

            if (!$langue)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));

            if (!$profile)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'id not null'));

          
            if ($nom == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'nom not null'));

            $profileTraduction = new ProfileTraduction();
            $profileTraduction->setDescription($description);
            $profileTraduction->setNom($nom);
            $profileTraduction->setProfile($profile);
            $profileTraduction->setLangue($langue);

            $em->persist($profile);
            $em->persist($profileTraduction);
            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/delete-traduction-langue", name="delete_traduction_profile")
     */
    public function deleteTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $profileTraduction = $em->getRepository('UtilisateursBundle:ProfileTraduction')->find($request->get('traduction'));

            if (!$profileTraduction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'profileTraduction not null'));

            $em->remove($profileTraduction);
            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/update-traduction-langue", name="update_traduction_profile")
     */
    public function updateTraductionLangueAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $langue = $em->getRepository('BanquemondialeBundle:Langue')->find($request->get('langue'));
            $profile = $em->getRepository('UtilisateursBundle:Profile')->find($request->get('id'));
            $profileTraduction = $em->getRepository('UtilisateursBundle:ProfileTraduction')->find($request->get('traduction'));

            $nom = $request->get('nom');

            $description = $request->get('description');

            if (!$langue)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'langue not null'));

            if (!$profileTraduction)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'profileTraduction not null'));

            if (!$profile)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'profile not null'));

            if ($description == '')
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'description not null'));

            $profileTraduction->setDescription($description);
            $profileTraduction->setNom($nom);
            $profileTraduction->setProfile($profile);
            $profileTraduction->setLangue($langue);

            $em->flush();

            return new JsonResponse(array(
                'error' => '0',
                'langue' => $profileTraduction->getLangue()->getLibelle(),
                'message' => 'done'));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error'));
    }

    /**
     * @Route("/update-nom", name="update_profile_2")
     */
    public function updateNomAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $description = $request->get('description');
            $nom = $request->get('nom');

            if (!$nom)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'nom not null'));

            if (!$description)
                return new JsonResponse(array(
                    'error' => '1',
                    'message' => 'description not null'));

            $em = $this->getDoctrine()->getManager();
            $profile = $em->getRepository("UtilisateursBundle:Profile")->find($id);

            $profile->setDescription($description);
            $profile->setNom($nom);

            $em->persist($profile);

            $em->flush();
            return new JsonResponse(array(
                'error' => '0'));
        } else
            return new JsonResponse(array(
                'error' => '1',
                'message' => 'Error'));
    }

}
