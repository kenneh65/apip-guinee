<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BanquemondialeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BanquemondialeBundle\Entity\CommissionnaireAuCompte;
use BanquemondialeBundle\Form\CommissionnaireAuCompteType;
use BanquemondialeBundle\Entity\DossierDemandeCommissionnaireAuCompte;
use BanquemondialeBundle\Form\DossierDemandeCommissaireAuCompteType;
use BanquemondialeBundle\Repository\TypeFonctionCommissaireTraductionRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of CommissionnaireAuCompteController
 *
 * @author DELL
 */
class CommissionnaireAuCompteController extends Controller {

    //put your code here   

    public function listerComCompteAction($id = null, $idd = null) {
        $message = '';
        $idDossier = $idd;
        if ((isset($id) && ($id != 0))) {  // modification d'un acteur existant : on recherche ses données
            $message = $this->get('translator')->trans('message_modification_en_cours');

            $em = $this->getDoctrine()->getManager();
            $creationComCompte = $em->find('BanquemondialeBundle:CommissionaireAuCompte', $id);
            if (!$creationComCompte) {
                $message = $this->get('translator')->trans('message_aucun_element_trouve');
            }
            $request = $this->get('request');
            $codLang = $request->getLocale();
            $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
            $definedFonctionTraduction = $em->getRepository("BanquemondialeBundle:FonctionTraduction")->getFonctionTraduction($creationComCompte->getFonction(), $langue->getId());
            $form = $this->container->get('form.factory')->create(new CommissionnaireAuCompteType(array('langue' => $langue)), $creationComCompte, array('definedFonctionTraduction' => $definedFonctionTraduction));
            $request = $this->container->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                //if ($form->isValid())  {
                $doc = $em->find('BanquemondialeBundle:DossierDemande', $idd);
                $dossierComCompte->setDossierDemande($doc);
                $em->persist($creationComCompte);
                $em->flush();
                if ((isset($id)) && ($id != 0)) {
                    $message = $translated = $this->get('translator')->trans('succes_modification');
                } else {
                    $message = $this->get('translator')->trans('commissaire_ajout_succes');
                }
                // }
            }
        } else {
            $creationComCompte = new CommissionnaireAuCompte();
            //$creationAdmin.idDossierDemande = $id;
            $request = $this->get('request');
            $codLang = $request->getLocale();
            $em = $this->getDoctrine()->getManager();
            $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
            $form = $this->container->get('form.factory')->create(new CommissionnaireAuCompteType(array('langue' => $langue)), $creationComCompte);
            $request = $this->container->get('request');
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {
                    $doc = $em->find('BanquemondialeBundle:DossierDemande', $idd);
                    $dossierComCompte->setDossierDemande($doc);
                    $em->persist($creationComCompte);
                    $em->flush();
                    $message = $this->get('translator')->trans('commissaire_ajout_succes');
                    $creationComCompte = new CommissionnaireAuCompte();
                    $form = $this->container->get('form.factory')->create(new CommissionnaireAuCompteType(array('langue' => $langue)), $creationComCompte);
                }
            }
        }

        $em = $this->getDoctrine()->getManager();
        $listercomCompte = $em->getRepository('BanquemondialeBundle:CommissionnaireAuCompte')->findAll();
        //$listeradmin= $em->getRepository('BanquemondialeBundle:Administrateur')->getListSearchAdministrateur($idDossier);
        //$listercomCompte= $em->getRepository('BanquemondialeBundle:Administrateur')->findByDossierDemande($idDossier);
        //$listeradmin= $em->getRepository('BanquemondialeBundle:Administrateur')->search($idDossier);
        // $query = $this->getDoctrine()->getRepository('AcmeDemoBundle:Pony')->search($form->getData());
        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:CommissionnaireAuCompte/layout/commissionnaireAuCompte.html.twig', array('form' => $form->createView(),
                    'message' => $message,
                    'listercomCompte' => $listercomCompte,
                    'idDossier' => $idDossier,
                    'comCompte' => $creationComCompte));
    }

    public function supprimerComCompteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $comCompte = $em->find('BanquemondialeBundle:CommissionnaireAuCompte', $id);
        if (!$comCompte) {
            throw new NotFoundHttpException($this->get('translator')->trans('message_aucun_element_trouve'));
        }
        $em->remove($comCompte);
        $em->flush();
        return new RedirectResponse($this->container->get('router')->generate('CommissionnaireAuCompte_listercomCompte', array('id' => 0)));
    }

    public function retirerComCompteAction($id, $idd) {
        $em = $this->getDoctrine()->getManager();
        $comCompte = $em->find('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte', $id);
        if (!$comCompte) {
            throw new NotFoundHttpException($this->get('translator')->trans('message_aucun_element_trouve'));
        }
        $em->remove($comCompte);
        $em->flush();
        return new RedirectResponse($this->container->get('router')->generate('CommissionnaireAuCompte_ListeAffectercomCompte', array('idd' => $idd)));
    }

    public function affecterComCompteAction($id, $idd) {
        $em = $this->getDoctrine()->getManager();
        $dossierComCompte = new \BanquemondialeBundle\Entity\DossierDemandeCommissionnaireAuCompte();
        $doc = $em->find('BanquemondialeBundle:DossierDemande', $idd);
        $dossierComCompte->setDossierDemande($doc);
        $comCompte = $em->find('BanquemondialeBundle:CommissionnaireAuCompte', $id);
        $dossierComCompte->setCommissionnaireAuCompte($comCompte);
        $em->persist($dossierComCompte);
        //echo("<script>alert('ok');</script>");
        $em->flush();
        return new RedirectResponse($this->container->get('router')->generate('CommissionnaireAuCompte_ListeAffectercomCompte', array('idd' => $idd)));
    }

    public function listerAffecterComCompteAction($idd) {
        $idDossier = $idd;
        $em = $this->getDoctrine()->getManager();
        $listercomCompte = $em->getRepository('BanquemondialeBundle:CommissionnaireAuCompte')->findAll();
        $listeCommissaire = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findByDossierDemande($idd);

        // $dossComm = new DossierDemandeCommissionnaireAuCompte();
        //$creationAdmin.idDossierDemande = $id;
//        $request = $this->get('request');
//        $codLang = $request->getLocale();
//        $em = $this->getDoctrine()->getManager();                
//        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);        
//        $form = $this->container->get('form.factory')->create(new DossierDemandeCommissaireAuCompteType(array('langue' => $langue)), $dossComm);

        return $this->container->get('templating')->renderResponse('BanquemondialeBundle:Default:CommissionnaireAuCompte/layout/affecterComCompte.html.twig', array(
                    //'form' => $form->createView(),
                    'listercomCompte' => $listercomCompte,
                    'idd' => $idd,
                    'listeCommissaire' => $listeCommissaire));
    }

    public function ajoutCommissaireAction($idd, $idC = null) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        //$user = $this->container->get('security.context')->getToken()->getUser();
        //$idLangue=$em->getRepository('BanquemondialeBundle:Langue')->findById($codLang);
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $numeroDossier = $dossierDemande->getNumeroDossier();
        $newCommissaire = new DossierDemandeCommissionnaireAuCompte();
        $edit = "";
        $translated = "";
        $form = $this->createForm(new DossierDemandeCommissaireAuCompteType(array('langue' => $langue, 'definedTypeFonctionCommissaire' => null)), $newCommissaire);
        if ((isset($idC) && ($idC != 0))) { //cas de Mise à jour
            $newCommissaire = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->find($idC);
            $edit = "edit";
            $typeFonction = $em->getRepository('BanquemondialeBundle:TypeFonctionCommissaire')->find($newCommissaire->getFonction());

            $form = $this->createForm(new DossierDemandeCommissaireAuCompteType(array('langue' => $langue, 'definedTypeFonctionCommissaire' => $typeFonction)), $newCommissaire);
        }
        if ($request->getMethod() == 'POST') {

            $form->bind($request);
            if ($form->isValid()) {
                if (isset($edit) && !empty($edit)) {
                    $curentComissaireDossier = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findOneBy(array('dossierDemande' => $idd, 'commissionnaireAuCompte' => $newCommissaire->getCommissionnaireAuCompte()));
                    if ($curentComissaireDossier) {
                        $translated = $this->get('translator')->trans('commisaire_modifier_succes');
                        $curentComissaireDossier->setFonction($newCommissaire->getFonction());
                        $em->persist($curentComissaireDossier);
                        $em->flush();
                    } else {
                        $em->persist($newCommissaire);
                        $em->flush();
                        $translated = $this->get('translator')->trans('commisaire_ajout_succes');
                    }
                } else {
                    $curentComissaireDossier = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findOneBy(array('dossierDemande' => $idd, 'commissionnaireAuCompte' => $newCommissaire->getCommissionnaireAuCompte()));
                    if ($curentComissaireDossier) {
                        $translated = $this->get('translator')->trans('commisaire_existe_deja');
                    } else {
                        $newCommissaireDossier = new DossierDemandeCommissionnaireAuCompte();
                        $newCommissaireDossier->setCommissionnaireAuCompte($newCommissaire->getCommissionnaireAuCompte());
                        $newCommissaireDossier->setDossierDemande($dossierDemande);
                        $newCommissaireDossier->setFonction($newCommissaire->getFonction());
                        $em->persist($newCommissaireDossier);
                        $em->flush();
                        $translated = $this->get('translator')->trans('commisaire_ajout_succes');
                    }
                }
            }
            $this->get('session')->getFlashBag()->add('info', $translated);
            return $this->redirectToRoute('ajoutCommissaire', array('idd' => $idd, 'numeroDossier' => $numeroDossier));
        }
        $listCommissaire = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findDossierDemandeCommissaire($idd, $langue);
        return $this->render('BanquemondialeBundle:Default:CommissionnaireAuCompte/layout/ajoutCommissaire.html.twig', array('form' => $form->createView(), 'listCommissaire' => $listCommissaire, 'idd' => $idd, 'idC' => $idC, 'numeroDossier' => $numeroDossier));
    }

    public function supprimerCommissaireAction($idd, $idC) {
        $em = $this->getDoctrine()->getManager();
        $commissaire = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->find($idC);
        $translated = "";
        if ($commissaire) {
            $em->remove($commissaire);
            $em->flush();
            $translated = $this->get('translator')->trans('pole.suppression_message');
        }

        $this->get('session')->getFlashBag()->add('info', $translated);
        //return new RedirectResponse($this->container->get('router')->generate('ajouter_pole'));

        return $this->redirectToRoute('ajoutCommissaire', array('idd' => $idd));
    }

    public function choixCommissaireAuCompteAction($idd = null) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $demande = $em->find('BanquemondialeBundle:DossierDemande', $idd);
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang)->getId();
        $listTypeFonctionCommissaire = $em->getRepository('BanquemondialeBundle:TypeFonctionCommissaire')->findByLangue($langue);
        if (isset($idd)) {  // Recherche des données du dossiers via l'id du Dossier            
            if ($request->getMethod() == 'POST') {
                $j = 1;
                $cocher = $request->get('radio' . $j);
                while ($cocher = $request->get('radio' . $j)) {
                    if ($cocher) {
                        //echo "<script>alert('".$request->get('radio' . $j)."');</script>";
                        $idfonction = $request->get('radio' . $j);
                        $idcomm = $request->get('idcomm' . $j);
                        $doss = $request->get('doss' . $j);
                        $dossCom = $request->get('dossCom' . $j);
                        //echo "<script>alert('".$idfonction."-".$idcomm."');</script>";
                        //$CommissaireDossierExist = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findOneBy(array('dossierDemande' => $doss, 'commissionnaireAuCompte' => $idcomm));
                        if (!((isset($dossCom) && ($dossCom != 0)))) {
                            $demande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($doss);
                            $fonction = $em->getRepository('BanquemondialeBundle:TypeFonctionCommissaire')->find($idfonction);
                            $commissaire = $em->getRepository('BanquemondialeBundle:CommissionnaireAuCompte')->find($idcomm);
                            $typFonction = new DossierDemandeCommissionnaireAuCompte();
                            $typFonction->setFonction($fonction);
                            $typFonction->setCommissionnaireAuCompte($commissaire);
                            $typFonction->setDossierDemande($demande);
                            $em->persist($typFonction);
                            $em->flush();
                        } else {
                            $demande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($doss);
                            $fonction = $em->getRepository('BanquemondialeBundle:TypeFonctionCommissaire')->find($idfonction);
                            $commissaire = $em->getRepository('BanquemondialeBundle:CommissionnaireAuCompte')->find($idcomm);
                            $typFonction = $em->find('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte', $dossCom);
                            $typFonction->setFonction($fonction);
                            $typFonction->setCommissionnaireAuCompte($commissaire);
                            $typFonction->setDossierDemande($demande);
                            $em->persist($typFonction);
                            $em->flush();
                        }
                    }
                    $j++;
                }
            }
        }

        $listCommissaireDossier = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findByDossierDemande($idd);
        $listCommissaire = $em->getRepository('BanquemondialeBundle:CommissionnaireAuCompte')->findAll();
        return $this->render('BanquemondialeBundle:Default:CommissionnaireAuCompte/layout/choixComm.html.twig', array('listCommissaire' => $listCommissaire,
                    'idd' => $idd,
                    'listCommissaireDossier' => $listCommissaireDossier,
                    'listTypeFonctionCommissaire' => $listTypeFonctionCommissaire));
    }

    public function ListeComCOmpteDossierAction(Request $request, $idd) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        //$listTypeFonctionCommissaire = $em->getRepository('BanquemondialeBundle:TypeFonctionCommissaire')->findByLangue($langue);
        //$listCommissaire = $em->getRepository('BanquemondialeBundle:CommissionnaireAuCompte')->findAll();
        //$listCommissaireDossier = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findByDossierDemande($idd);
        $listCommissaireDossier = $em->getRepository('BanquemondialeBundle:DossierDemandeCommissionnaireAuCompte')->findDossierDemandeCommissaire($idd, $langue);
        //die(dump($listTypeFonctionCommissaire));
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $dd = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $idFormeJ=$dd->getFormeJuridique()->getId();
        $nextRte = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextEtapePoleRoute($currentRoute,$idFormeJ);
        
        if (!$nextRte) {
            $nextRte = 'traiterDossier';
        }
        $routeAvant = $em->getRepository('ParametrageBundle:EtapeCreation')->getEtapePoleRouteBefore($currentRoute,$idFormeJ);
          try {
            $referer = $request->headers->get('referer');
            $path = substr($referer, strpos($referer, $request->getBaseUrl()));
            $path = str_replace($request->getBaseUrl(), '', $path);

            $matcher = $this->get('router')->getMatcher();
            $parameters = $matcher->match($path);
            $previous = $parameters['_route'];

            if ($routeAvant != $previous and $previous!=$nextRte) {
                $translated = $this->get('translator')->trans('suivre_etape_acces_page');
                $this->get('session')->getFlashBag()->add('error', $translated);

                return $this->redirectToRoute($previous);
            }
        } catch (\Exception $e) {
            $translated = $this->get('translator')->trans('suivre_etape_acces_page');
            $this->get('session')->getFlashBag()->add('error', $translated);
            return $this->redirectToRoute('administration');
        }
        
        return $this->render('BanquemondialeBundle:Default:CommissionnaireAuCompte/layout/listeComComptePole.html.twig', array('listCommissaireDossier' => $listCommissaireDossier,
                    'idd' => $idd, 'dd' => $dd, 'rteSuivant' => $nextRte, 'routeAvant' => $routeAvant));
    }

    public function loadInfosComAction(Request $request) {


        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();

            $commissaire = $em->getRepository('BanquemondialeBundle:CommissionnaireAuCompte')->find($request->get('id'));
            $adress = "";
            if ($commissaire) {
                $adress = $commissaire->getAdresse();
            }

            return new JsonResponse(array(
                'error' => '0',
                'adresse' => $adress
            ));
        }

        return new JsonResponse(array(
            'error' => '1',
            'message' => 'Error de '));
    }

    private function createCreateForm(CommissionnaireAuCompte $entity) {
        $form = $this->createForm(new CommissionnaireAuCompteType(), $entity);


        return $form;
    }

    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $commissaires = $em->getRepository('BanquemondialeBundle:CommissionnaireAuCompte')->findAll();

        return $this->render('ParametrageBundle:commissaire:index.html.twig', array('commissaires' => $commissaires));
    }

    /**
     * 
     * @param Request $requestCette action permet d'ajouter un commissaire un compte
     */
    public function addAction(Request $request) {
        $commissaire = new CommissionnaireAuCompte();
        $form = $this->createCreateForm($commissaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();//$newPole->getNom()
			$commissaire->setTypes("Personne morale");
			$prenom = $commissaire->getPrenom();
			if (isset($prenom) && ($prenom != '')) {
				$commissaire->setTypes("Personne physique");
			}
			$commissaire->setActif(true);
            $em->persist($commissaire);
            $em->flush();
            $translated = $this->get('translator')->trans('commissaire.ajout_succes');
            $this->get('session')->getFlashBag()->add('info', $translated);
            return $this->redirectToRoute('commissaire_index');
        }

        return $this->render('ParametrageBundle:commissaire:new.html.twig', array(
                    'form' => $form->createView(),
                    'commissaire' => $commissaire
        ));
    }

    public function deleteAction(Request $request, CommissionnaireAuCompte $commissaire) {

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($commissaire);
            $em->flush();
            $translated = $this->get('translator')->trans('message_suppression_entity_succes');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $translated = $this->get('translator')->trans('message_suppression_entity_fail');
        }
        $this->get('session')->getFlashBag()->add('info', $translated);
        return $this->redirectToRoute('commissaire_index');
    }
	
	public function supprimerCommAction($id) {
        $em = $this->getDoctrine()->getManager();
        $comm = $em->getRepository('BanquemondialeBundle:CommissionnaireAuCompte')->find($id);
        $translated = null;
        if ($comm) {
            $comm->setActif(false);
            $em->persist($comm);
            $em->flush();
            $translated = $this->get('translator')->trans('desactivation_message');
        }

        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('commissaire_index');
    }
	
	public function activerCommAction($id) {
        $em = $this->getDoctrine()->getManager();
        $comm = $em->getRepository('BanquemondialeBundle:CommissionnaireAuCompte')->find($id);
        $translated = null;
        if ($comm) {
            $comm->setActif(true);
            $em->persist($comm);
            $em->flush();
            $translated = $this->get('translator')->trans('activation_message');
        }

        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('commissaire_index');
    }

    public function editAction(Request $request, CommissionnaireAuCompte $commissaire) {
        $form = $this->createCreateForm($commissaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			$commissaire->setTypes("Personne morale");
			$prenom = $commissaire->getPrenom();
			if (isset($prenom) && ($prenom != '')) {
				$commissaire->setTypes("Personne physique");
			}
            $em->persist($commissaire);
            $em->flush();
            $translated = $this->get('translator')->trans('commissaire.modification_succes');
            $this->get('session')->getFlashBag()->add('info', $translated);
            return $this->redirectToRoute('commissaire_index');
        }

        return $this->render('ParametrageBundle:commissaire:edit.html.twig', array(
                    'edit_form' => $form->createView(),
                    'commissaire' => $commissaire
        ));
    }

}
