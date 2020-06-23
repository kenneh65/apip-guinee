<?php

namespace BanquemondialeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BanquemondialeBundle\Form\ActiviteAnterieureType;
use BanquemondialeBundle\Entity\ActiviteAnterieure;
use Symfony\Component\HttpFoundation\RedirectResponse;

//use Symfony\Component\HttpFoundation\Request;

class PersPhysiqueController extends Controller {

    /**
     * @Route("/admin/addActiviteAnterieure")
     */
    public function addActiviteAnterieureAction($idd) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $isAguipe = true;
        $poleAguipe = $em->getRepository('ParametrageBundle:Pole')->findBySigle("AGUIPE");
        $docAguipe = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd, 'pole' => $poleAguipe));
        if ($docAguipe) {
            $isAguipe = true;
        } else {
            $isAguipe = false;
        }
        $formeJuridiqueId = $dossierDemande->getFormeJuridique()->getId();
        $activiteExist = $em->getRepository('BanquemondialeBundle:ActiviteAnterieure')->findOneBy(array('dossierDemande' => $dossierDemande));
        if ($activiteExist) {
            $newActivite = $activiteExist;
        } else {
            $newActivite = new ActiviteAnterieure();
        }


        $form = $this->createForm(new ActiviteAnterieureType(), $newActivite);
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $activiteExist = $em->getRepository('BanquemondialeBundle:ActiviteAnterieure')->findOneBy(array('dossierDemande' => $dossierDemande));
                if ($activiteExist) {
                    $activiteExist->setDossierDemande($dossierDemande);
                    $em->persist($activiteExist);
                    $em->flush();
                    $translated = $this->get('translator')->trans('succes_update');
                } else {
                    $newActivite->setDossierDemande($dossierDemande);
                    $em->persist($newActivite);
                    $em->flush();
                    $translated = $this->get('translator')->trans('succes_add');
                }
                $rteSuivant = $this->getNextEtapeRoute($formeJuridiqueId, $isAguipe);
                return new RedirectResponse($this->container->get('router')->generate($rteSuivant, array('idd' => $idd)));
            } else {
                die(dump($form->getErrorsAsString()));
            }
        }
        return $this->render('BanquemondialeBundle:PersPhysique:add_activite_anterieure.html.twig', array('form' => $form->createView(), 'dd' => $dossierDemande));
    }

    public function getNextEtapeRoute($formeJuridique, $isAguipe) {
        $em = $this->getDoctrine()->getManager();
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $etape = $em->getRepository('ParametrageBundle:Fonctionnalite')->findOneBy(array('route' => $currentRoute));
        $etapeC = $em->getRepository('ParametrageBundle:EtapeCreation')->findOneBy(array('etape' => $etape->getId(), 'formeJuridique' => $formeJuridique));
        $ordre = 1;
        if ($etapeC) {
            $ordre = $etapeC->getOrdre();
        }
        $etapeSuivant = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextStepFormeJuridique($ordre, $formeJuridique, $isAguipe);
        $rteSuivant = null;
        if ($etapeSuivant) {
            $rteSuivant = $etapeSuivant->getEtape()->getRoute();
        }
        return $rteSuivant;
    }

    public function detailsActiviteAnterieureAction($idd) {
        $em = $this->getDoctrine()->getManager();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $nextRte = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextEtapePoleRoute($currentRoute, $dossierDemande->getFormeJuridique()->getid());
        //die(dump($currentRoute));
        if (!$nextRte) {
            //$nextRte = 'traiterDossier';
        }

        $routeAvant = $em->getRepository('ParametrageBundle:EtapeCreation')->getEtapePoleRouteBefore($currentRoute, $dossierDemande->getFormeJuridique()->getid());


        $activiteAnteieure = $em->getRepository('BanquemondialeBundle:ActiviteAnterieure')->findOneBy(array('dossierDemande' => $dossierDemande));
        return $this->render('BanquemondialeBundle:PersPhysique:detailsActiviteAnterieure.html.twig', array('actAnterieure' => $activiteAnteieure, 'rteSuivant' => $nextRte, 'idd' => $idd, 'routeAvant' => $routeAvant, 'dd' => $dossierDemande));
    }

    public function detailsOrigineEIAction($idd) {
        $em = $this->getDoctrine()->getManager();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $nextRte = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextEtapePoleRoute($currentRoute, $dossierDemande->getFormeJuridique()->getid());
        if (!$nextRte) {
            $nextRte = 'traiterDossier';
        }

        $routeAvant = $em->getRepository('ParametrageBundle:EtapeCreation')->getEtapePoleRouteBefore($currentRoute, $dossierDemande->getFormeJuridique()->getid());
        $activiteAnteieure = $em->getRepository('BanquemondialeBundle:OriginePM')->findOneBy(array('dossierDemande' => $idd));
        return $this->render('BanquemondialeBundle:PersPhysique:detailsOrigineEtab.html.twig', array('origine' => $activiteAnteieure, 'rteSuivant' => $nextRte, 'idd' => $idd, 'routeAvant' => $routeAvant, 'dd' => $dossierDemande));
    }

    public function detailsDocACollecterAction($idd) {
        $em = $this->getDoctrine()->getManager();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $nextRte = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextEtapePoleRoute($currentRoute, $dossierDemande->getFormeJuridique()->getid());
        if (!$nextRte) {
            $nextRte = 'traiterDossier';
        }

        $routeAvant = $em->getRepository('ParametrageBundle:EtapeCreation')->getEtapePoleRouteBefore($currentRoute, $dossierDemande->getFormeJuridique()->getid());
        $listPoleCocher = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findBy(array('dossierDemande' => $idd), array('ordre' => 'asc'));
        //die(dump($listPoleCocher));
        return $this->render('BanquemondialeBundle:PersPhysique:detailsDocumentACollecter.html.twig', array('listDocForCollect' => $listPoleCocher, 'rteSuivant' => $nextRte, 'idd' => $idd, 'routeAvant' => $routeAvant, 'dd' => $dossierDemande));
    }

    public function detailsPersonneEngageurAction($idd) {
        $em = $this->getDoctrine()->getManager();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $nextRte = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextEtapePoleRoute($currentRoute, $dossierDemande->getFormeJuridique()->getid());
        if (!$nextRte) {
            $nextRte = 'traiterDossier';
        }
        $routeAvant = $em->getRepository('ParametrageBundle:EtapeCreation')->getEtapePoleRouteBefore($currentRoute, $dossierDemande->getFormeJuridique()->getid());
        $listePersonneEngageur = $em->getRepository('BanquemondialeBundle:PersonneEngageur')->findBy(array('dossierDemande' => $idd));
        return $this->render('BanquemondialeBundle:PersPhysique:personneEngageur.html.twig', array('listePersEngageur' => $listePersonneEngageur, 'rteSuivant' => $nextRte, 'idd' => $idd, 'routeAvant' => $routeAvant, 'dd' => $dossierDemande));
    }

    public function detailsCNSSAction($idd) {

        $em = $this->getDoctrine()->getManager();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $nextRte = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextEtapePoleRoute($currentRoute, $dossierDemande->getFormeJuridique()->getid());
        if (!$nextRte) {
            $nextRte = 'traiterDossier';
        }
        $routeAvant = $em->getRepository('ParametrageBundle:EtapeCreation')->getEtapePoleRouteBefore($currentRoute, $dossierDemande->getFormeJuridique()->getid());
        $ficheEmployeur = $em->getRepository('BanquemondialeBundle:Cnss')->findOneBy(array('dossierDemande' => $idd));
        return $this->render('BanquemondialeBundle:PersPhysique:detailsCNSS.html.twig', array('cnss' => $ficheEmployeur, 'rteSuivant' => $nextRte, 'idd' => $idd, 'routeAvant' => $routeAvant, 'dd' => $dossierDemande));
    }

    public function detailsFicheEntrepriseAction($idd) {
        $em = $this->getDoctrine()->getManager();
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $nextRte = $em->getRepository('ParametrageBundle:EtapeCreation')->getNextEtapePoleRoute($currentRoute, $dossierDemande->getFormeJuridique()->getid());
        if (!$nextRte) {
            $nextRte = 'traiterDossier';
        }
        $routeAvant = $em->getRepository('ParametrageBundle:EtapeCreation')->getEtapePoleRouteBefore($currentRoute, $dossierDemande->getFormeJuridique()->getid());
        $aguipe = $em->getRepository('BanquemondialeBundle:Aguipe')->findOneBy(array('dossierDemande' => $idd));
        $listeEmploye = $em->getRepository('BanquemondialeBundle:Employe')->findBy(array('dossierDemande' => $idd));
        return $this->render('BanquemondialeBundle:PersPhysique:ficheEntreprise.html.twig', array('aguipe' => $aguipe, 'listeEmploye' => $listeEmploye, 'rteSuivant' => $nextRte, 'idd' => $idd, 'routeAvant' => $routeAvant, 'dd' => $dossierDemande));
    }

    public function validerDossierDiasporaAction($idd) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        $user = $this->container->get('security.context')->getToken()->getUser();
        $stack = $this->get('request_stack');
        $masterRequest = $stack->getMasterRequest();
        $currentRoute = $masterRequest->get('_route');
        $routeAvant = $em->getRepository('ParametrageBundle:EtapeCreation')->getEtapePoleRouteBefore($currentRoute, $dossierDemande->getFormeJuridique()->getid());
        $numeroDossier = 'GU-CE-' . strtoupper($dossierDemande->getPays()->getCode()) . sprintf("%08d", $idd);
        if ($request->getMethod() == 'POST') {
            $date = new \DateTime();
            $dossierDemande->setDateValidation($date);
            $dossierDemande->setStatutValidation(1);
            $dossierDemande->setUtilisateurValidation($user);
            $dossierDemande->setNumeroDossier($numeroDossier);

            $em->persist($dossierDemande);
            $em->flush();
            //Matérialiser la validation du pole APIP
            $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
            $docForValidationAPIP = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('dossierDemande' => $idd, 'pole' => $poleAPIP->getId()));
            if ($docForValidationAPIP) {                          
                $statutDelivre= $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(2);
                $docForValidationAPIP->setStatutTraitement($statutDelivre);
                $docForValidationAPIP->setMotif(null);
                $docForValidationAPIP->setDateDelivrance($date);
                $em->persist($docForValidationAPIP);
                $em->flush();
            }
            //fin
            $premiersPolesTraitant = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findFirstPoleTraitant($idd);
            if ($premiersPolesTraitant) {
                $statutEncours = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(1);
                foreach ($premiersPolesTraitant as $poleTraitant) {
                    $poleTraitant->setStatutTraitement($statutEncours);
                    $poleTraitant->setDateSoumission($date);
                    $em->persist($poleTraitant);

                    $message = $this->get('translator')->trans('message_dossier_recu');

                    $objet = $this->get('translator')->trans('reception_dossier_objet');
                    $notif = $this->container->get('utilisateurs.notification');
                    $notif->notifier($message . ' ' . $dossierDemande->getNumeroDossier(), $poleTraitant->getPole()->getUtilisateur()->get(0), $objet);
                }

                $em->flush();
                $translated = $this->get('translator')->trans('message_soumission_succes_dossier_numero') . " " . $numeroDossier;
                $this->get('session')->getFlashBag()->add('info', $translated);
                return $this->redirectToRoute('suivreDossier');
            }
        }      
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $representant=$em->getRepository("BanquemondialeBundle:Representant")->getRepresentantByDossierDemande($idd,$langue->getId());
        return $this->render('ParametrageBundle:ParameterPole:traiterDossier.html.twig', array('idd' => $idd, 'routeAvant' => $routeAvant, 'dd' => $dossierDemande,'rep'=>$representant[0]));
    }

    public function demandeModificationAction($idd) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $pole = $user->getPole();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $dossierDemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->find($idd);
        if ($request->getMethod() == 'POST') {
            if ($request->request->has('textAreaModifier')) {
                $motif = $request->get('textAreaModifier');
                if (strlen($motif) <= 255) {

                    $dossierDemande->setStatut(3);
                    $dossierDemande->setMotif($motif);
                    $em->persist($dossierDemande);
                    $em->flush();

                    $poleAPIP = $em->getRepository('ParametrageBundle:Pole')->getPoleBySige("APIP");
                    $docForValidationAPIP = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findOneBy(array('dossierDemande' => $idd, 'pole' => $poleAPIP->getId()));
                    if ($docForValidationAPIP) {                      
                                              
                        $docForValidationAPIP->setMotif($motif);
                        $statutDModif = $em->getRepository('BanquemondialeBundle:StatutTraitement')->find(3);
                        $docForValidationAPIP->setStatutTraitement($statutDModif);
                        
                        $em->persist($docForValidationAPIP);
                        $em->flush();
                    }

                    $notif = $this->container->get('utilisateurs.notification');
                    $message = $this->get('translator')->trans('message_demande_modification_envoye');
                    $message2 = $this->get('translator')->trans('par_le_pole');
                    $nomDuPole = $this->get('translator')->trans($pole->getNom());
                    //envoi de la notification
                    $objet = $this->get('translator')->trans('demande_modification_dossier_envoye');
                    $notif->notifier($dossierDemande->getNumeroDossier() . ' ' . $message . ' ' . $message2 . ' ' . $nomDuPole, $dossierDemande->getUtilisateur(), $objet);
                }
            }
        }
        return $this->redirectToRoute('DossiersDiaspora');
    }

}
