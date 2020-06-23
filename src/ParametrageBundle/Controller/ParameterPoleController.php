<?php

namespace ParametrageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ParametrageBundle\Form\PoleType;
use ParametrageBundle\Entity\Pole;
use ParametrageBundle\Entity\ComplementPole;
use ParametrageBundle\Form\ComplementPoleType;
use BanquemondialeBundle\Form\CircuitType;
use BanquemondialeBundle\Form\PieceJointeType;
use BanquemondialeBundle\Entity\Circuit;
use BanquemondialeBundle\Entity\PieceJointe;
use BanquemondialeBundle\Entity\Region;
use \ParametrageBundle\Entity\Tarification;
use ParametrageBundle\Form\TarificationType;
use ParametrageBundle\Form\EtapeCreationType;
use ParametrageBundle\Entity\EtapeCreation;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ParameterPoleController extends Controller {

    public function ajouterPoleAction($idP = null) {
        $id = 0;
        $newPole = new Pole();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        //echo("<script>alert('id=".$request->request->get('id')."');</script>");
        if ((isset($idP) && ($idP != 0))) {
            $id = $idP;
            $newPole = $em->getRepository('ParametrageBundle:Pole')->find($idP);
        }
        $form = $this->createForm(new PoleType(), $newPole);
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            $translated = "";
            if ($form->isValid()) {
                //$curentPole = $em->getRepository('ParametrageBundle:Pole')->findOneByNom($newPole->getNom());
                $curentPole = $em->getRepository('ParametrageBundle:Pole')->find($idP);
                if ($curentPole) {
                    $curentPole->setAdresse($newPole->getAdresse());
                    $curentPole->setSigle($newPole->getSigle());
                    //$curentPole->setRegion($newPole->getRegion());
                    $em->persist($curentPole);
                    $em->flush();
                    $translated = $this->get('translator')->trans('message.modification_succes');
                } else {

                    $newPole->setActif(true);
                    $em->persist($newPole);
                    $em->flush();
                    $translated = $this->get('translator')->trans('message.ajout_succes');
                }
            }
            $this->get('session')->getFlashBag()->add('info', $translated);
            return new RedirectResponse($this->container->get('router')->generate('ajouter_pole'));
        }
        //$listePoles = $em->getRepository('ParametrageBundle:Pole')->findByActif(true);
        $listePoles = $em->getRepository('ParametrageBundle:Pole')->findAll();
        return $this->render('ParametrageBundle:ParameterPole:ajouter_pole.html.twig', array('form' => $form->createView(), 'listePoles' => $listePoles, 'id' => $id));
    }

    public function supprimerPoleAction($idP) {
        $em = $this->getDoctrine()->getManager();
        $pole = $em->getRepository('ParametrageBundle:Pole')->find($idP);
        $translated = null;
        //echo("<script>alert('ok delete');</script>");
        if ($pole) {
            $pole->setActif(false);
            $em->persist($pole);
            $em->flush();
            $translated = $this->get('translator')->trans('desactivation_message');
        }

        $this->get('session')->getFlashBag()->add('info', $translated);
        //return new RedirectResponse($this->container->get('router')->generate('ajouter_pole'));

        return $this->redirectToRoute('ajouter_pole');
    }

    public function activerPoleAction($idP) {
        $em = $this->getDoctrine()->getManager();
        $pole = $em->getRepository('ParametrageBundle:Pole')->find($idP);
        $translated = null;
        //echo("<script>alert('ok 45');</script>");
        if ($pole) {
            //echo("<script>alert('ok');</script>");
            $pole->setActif(true);
            $em->persist($pole);
            $em->flush();
            $translated = $this->get('translator')->trans('activation_message');
        }
        $this->get('session')->getFlashBag()->add('info', $translated);
        //return new RedirectResponse($this->container->get('router')->generate('ajouter_pole'));

        return $this->redirectToRoute('ajouter_pole');
    }

    public function rechercherPoleAction() {
        $request = $this->get('request');
        $data = $request->request->all()['pole'];
        $em = $this->getDoctrine()->getManager();
        $newPole = new Pole();
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $form = $this->createForm(new PoleType(), $newPole);
        $listePoles = $em->getRepository('ParametrageBundle:Pole')->rechercherPole($data, $langue->getId());

        return $this->render('ParametrageBundle:ParameterPole:ajouter_pole.html.twig', array('form' => $form->createView(), 'listePoles' => $listePoles));
    }

    public function ajoutComplementPoleAction($idC = null) {
        $newchamps = new ComplementPole();
        $translated = "";
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        if ((isset($idC) && ($idC != 0))) {
            $newchamps = $em->getRepository('ParametrageBundle:ComplementPole')->find($idC);
        }
        $form = $this->createForm(new ComplementPoleType(), $newchamps);
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $newchamps->setActif(true);
                $newchamps->setRequis(false);
                $em->persist($newchamps);
                $em->flush();
                $translated = $this->get('translator')->trans('message_complement_pole_enregistrer_succes');
                $this->get('session')->getFlashBag()->add('info', $translated);
                $newchamps = new ComplementPole();
                $form = $this->createForm(new ComplementPoleType(), $newchamps);
            }return new RedirectResponse($this->container->get('router')->generate('champ_pole'));
        }
        $listeChamps = $em->getRepository('ParametrageBundle:ComplementPole')->findByActif(true);
        return $this->render('ParametrageBundle:ParameterPole:champs_pole.html.twig', array('form' => $form->createView(), 'listeChamps' => $listeChamps));
    }

    public function supprimerComplementPoleAction($idP) {
        $em = $this->getDoctrine()->getManager();
        $pole = $em->getRepository('ParametrageBundle:ComplementPole')->find($idP);
        $translated = "";
        $compPoleDate = $em->getRepository('ParametrageBundle:PoleComplementDossier')->findOneBy(array('complementpole' => $idP));
        if ($compPoleDate) {
            if ($pole) {
                $pole->setActif(false);
                $em->persist($pole);
                $em->flush();
                $translated = $this->get('translator')->trans('pole.suppression_message');
            }
        } else {
            if ($pole) {
                $em->remove($pole);
                $em->flush();
                $translated = $this->get('translator')->trans('pole.suppression_message');
            }
        }
        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('champ_pole');
    }

    public function definirCircuitAction($idC = null) {
        $newCircuit = new Circuit();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $form = $this->createForm(new CircuitType(array('langue' => $langue, 'typeOpTraduit' => null, 'formeJTraduit' => null)), $newCircuit);
        $typeOpTraduit = NULL;
        $translated = "";
        if ((isset($idC) && ($idC != 0))) { //cas de Mise à jour
            $newCircuit = $em->getRepository('BanquemondialeBundle:Circuit')->find($idC);
            $typeOpTraduit = $em->getRepository('BanquemondialeBundle:TypeOperationTraduction')->findOneBy(array('langue' => $langue, 'typeOperation' => $newCircuit->getTypeOperation()->getId()));
            $formeJTraduit = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('langue' => $langue, 'formeJuridique' => $newCircuit->getFormeJuridique()->getId()));
            $form = $this->createForm(new CircuitType(array('langue' => $langue, 'typeOpTraduit' => $typeOpTraduit, 'formeJTraduit' => $formeJTraduit)), $newCircuit);
        }

        if ($request->getMethod() == 'POST') {
            if (isset($typeOpTraduit)) {
                $form->bind($request);
                if ($form->isValid()) {
                    $circuitExist = $em->getRepository('BanquemondialeBundle:Circuit')->findOneBy(array('typeOperation' => $newCircuit->getTypeOperation(), 'formeJuridique' => $newCircuit->getFormeJuridique(), 'pole' => $newCircuit->getPole(),'typeDossier'=>$newCircuit->getTypeDossier()));
                    if ($circuitExist) {
                        $circuitExist->setOrdre($newCircuit->getOrdre());
                        $em->persist($circuitExist);
                        $em->flush();
                        $translated = $this->get('translator')->trans('circuit.update_succes');
                    } else {
                        $em->persist($newCircuit);
                        $em->flush();
                        $translated = $this->get('translator')->trans('circuit.add_succes');
                    }
                } else {
                    //die(dump($form->getErrorsAsString()));
                }
                $this->get('session')->getFlashBag()->add('info', $translated);
                return $this->redirectToRoute('definirCircuit');
            } else { //en cas de nouvel ajout                
                $form->bind($request);
                if ($form->isValid()) {
                    $circuitExist = $em->getRepository('BanquemondialeBundle:Circuit')->findOneBy(array('typeOperation' => $newCircuit->getTypeOperation(), 
                                'formeJuridique' => $newCircuit->getFormeJuridique(), 'pole' => $newCircuit->getPole(),'typeDossier'=>$newCircuit->getTypeDossier()));
                    if ($circuitExist) {
                        $translated = $this->get('translator')->trans('circuit.existe_deja');
                    } else {
                        $em->persist($newCircuit);
                        $em->flush();
                        $translated = $this->get('translator')->trans('circuit.add_succes');
                    }
                }
                $this->get('session')->getFlashBag()->add('info', $translated);
                return $this->redirectToRoute('definirCircuit');
                //}
            }
        }



        $listeCircuit = $em->getRepository('BanquemondialeBundle:Circuit')->getListCircuit($langue->getId());
        return $this->render('ParametrageBundle:ParameterPole:ajoutCicuit.html.twig', array('form' => $form->createView(), 'listeCircuit' => $listeCircuit, 'idC' => $idC));
    }

    public function rechercherCircuitAction($idC = null) {

        $request = $this->get('request');
        $data = $request->request->all()['circuit'];
        //die(dump($data));
        $em = $this->getDoctrine()->getManager();
        $newCircuit = new Circuit();
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);


        //die(dump($data["typeOperation"]));
        $idTypeOperation = null;
        $idFormeJuridique = null;
        $idPole = null;
        $ordre = null;
		$idTypeDossier = null;
        if ($data["typeOperation"] != '') {
            $idTypeOperation = $em->getRepository('BanquemondialeBundle:TypeOperationTraduction')->find($data["typeOperation"])->getTypeOperation()->getId();
        }

        if ($data["formeJuridique"] != '') {
            $idFormeJuridique = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->find($data["formeJuridique"])->getFormeJuridique()->getId();
        }

        if ($data["pole"] != '') {
            $idPole = $data["pole"];
        }

        if ($data["ordre"] != '') {
            $ordre = $data["ordre"];
        }
		
		if ($data["typeDossier"] != '') {
            $idTypeDossier = $data["typeDossier"];
        }


        $idLangue = $langue->getId();
        //die(dump($idTypeOperation));


        $form = $this->createForm(new CircuitType(array('langue' => $langue, 'typeOpTraduit' => null, 'formeJTraduit' => null)), $newCircuit);
        //$listeCircuit = $em->getRepository('BanquemondialeBundle:Circuit')->findCircuit($data, $langue->getId());
        $listeCircuit = $em->getRepository('BanquemondialeBundle:Circuit')->findCircuitTraitement($idTypeOperation, $idFormeJuridique, $idPole, $ordre, $idTypeDossier, $idLangue);

        //$listeCircuit = $em->getRepository('BanquemondialeBundle:Circuit')->getListCircuit($langue->getId());
        return $this->render('ParametrageBundle:ParameterPole:ajoutCicuit.html.twig', array('form' => $form->createView(), 'listeCircuit' => $listeCircuit, 'idC' => $idC));
    }

    public function supprimerCircuitAction($idC) {
        $em = $this->getDoctrine()->getManager();
        $circuitSelect = $em->getRepository('BanquemondialeBundle:Circuit')->find($idC);
        $translated = "";
        if ($circuitSelect) {
            $em->remove($circuitSelect);
            $em->flush();
            $translated = $this->get('translator')->trans('pole.suppression_message');
        }

        $this->get('session')->getFlashBag()->add('info', $translated);
        //return new RedirectResponse($this->container->get('router')->generate('ajouter_pole'));

        return $this->redirectToRoute('definirCircuit');
    }

    public function rechercherPieceAction($idP = null) {

        $request = $this->get('request');
        $data = $request->request->all()['piece_jointe'];
        $em = $this->getDoctrine()->getManager();
        $newPiece = new PieceJointe();
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $form = $this->createForm(new PieceJointeType(array('langue' => $langue, 'typeOpTraduit' => null, 'formeJTraduit' => null, 'docTraduit' => null, 'fonctionTraduit' => null)), $newPiece);
        /*
          if ((isset($idP) && ($idP != 0))) {
          $newPiece = $em->getRepository('BanquemondialeBundle:PieceJointe')->find($idP);

          $typeOpTraduit = $em->getRepository('BanquemondialeBundle:TypeOperationTraduction')->findOneBy(array('langue' => $langue, 'typeOperation' => $newPiece->getTypeOperation()->getId()));
          $formeJTraduit = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('langue' => $langue, 'formeJuridique' => $newPiece->getFormeJuridique()->getId()));
          $docTraduit = $em->getRepository('BanquemondialeBundle:DocumentTraduction')->findOneBy(array('langue' => $langue, 'document' => $newPiece->getDocument()->getId()));
          $fct = $newPiece->getFonction();
          $fonctionTraduit = null;
          if ($fct) {
          $fonctionTraduit = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('langue' => $langue, 'fonction' => $fct->getId()));
          }

          $form = $this->createForm(new PieceJointeType(array('langue' => $langue, 'typeOpTraduit' => $typeOpTraduit, 'formeJTraduit' => $formeJTraduit, 'docTraduit' => $docTraduit, 'fonctionTraduit' => $fonctionTraduit)), $newPiece);
          }
         */
        //die(dump($data));

        $idTypeOperation = null;
        $idFormeJuridique = null;
        $idDocument = null;
        $idFonction = null;

        if ($data["typeOperation"] != '') {
            $idTypeOperation = $em->getRepository('BanquemondialeBundle:TypeOperationTraduction')->find($data["typeOperation"])->getTypeOperation()->getId();
        }

        if ($data["formeJuridique"] != '') {
            $idFormeJuridique = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->find($data["formeJuridique"])->getFormeJuridique()->getId();
        }

        if ($data["document"] != '') {
            $idDocument = $em->getRepository('BanquemondialeBundle:DocumentTraduction')->find($data["document"])->getDocument()->getId();
        }

        if ($data["fonction"] != '') {
            $idFonction = $data["fonction"];
        }


        $idLangue = $langue->getId();




        $listeCircuit = $em->getRepository('BanquemondialeBundle:PieceJointe')->rechercherPieceAJoindre($idTypeOperation, $idFormeJuridique, $idDocument, $idFonction, $idLangue);
        //$listeCircuit = $em->getRepository('BanquemondialeBundle:PieceJointe')->rechercherPiece($data, $langue->getId());
        return $this->render('ParametrageBundle:ParameterPole:ajoutPieceJ.html.twig', array('form' => $form->createView(), 'listeCircuit' => $listeCircuit, 'idP' => $idP));
    }

    public function supprimerPieceAction($idP) {
        $em = $this->getDoctrine()->getManager();
        $pieceAjoindre = $em->getRepository('BanquemondialeBundle:PieceJointe')->find($idP);
        $translated = "";
        if ($pieceAjoindre) {
            $em->remove($pieceAjoindre);
            $em->flush();
            $translated = $this->get('translator')->trans('pole.suppression_message');
        }

        $this->get('session')->getFlashBag()->add('info', $translated);
        //return new RedirectResponse($this->container->get('router')->generate('ajouter_pole'));

        return $this->redirectToRoute('definirPieceJointe');
    }

    public function definirPieceAJoindreAction($idP) {
        $newPiece = new PieceJointe();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $form = $this->createForm(new PieceJointeType(array('langue' => $langue, 'typeOpTraduit' => null, 'formeJTraduit' => null, 'docTraduit' => null, 'fonctionTraduit' => null)), $newPiece);
        if ((isset($idP) && ($idP != 0))) {

            $newPiece = $em->getRepository('BanquemondialeBundle:PieceJointe')->find($idP);
            $typeOpTraduit = $em->getRepository('BanquemondialeBundle:TypeOperationTraduction')->findOneBy(array('langue' => $langue, 'typeOperation' => $newPiece->getTypeOperation()->getId()));
            $formeJTraduit = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('langue' => $langue, 'formeJuridique' => $newPiece->getFormeJuridique()->getId()));
            $docTraduit = $em->getRepository('BanquemondialeBundle:DocumentTraduction')->findOneBy(array('langue' => $langue, 'document' => $newPiece->getDocument()->getId()));
            $fct = $newPiece->getFonction();
            $fonctionTraduit = null;
            if ($fct) {
                $fonctionTraduit = $em->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('langue' => $langue, 'fonction' => $fct->getId()));
            }

            $form = $this->createForm(new PieceJointeType(array('langue' => $langue, 'typeOpTraduit' => $typeOpTraduit, 'formeJTraduit' => $formeJTraduit, 'docTraduit' => $docTraduit, 'fonctionTraduit' => $fonctionTraduit)), $newPiece);

            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {
                    $pieceExist = $em->getRepository('BanquemondialeBundle:PieceJointe')->findOneBy(array('typeOperation' => $newPiece->getTypeOperation(),
                        'formeJuridique' => $newPiece->getFormeJuridique(), 'document' => $newPiece->getDocument(), 'fonction' => $newPiece->getFonction()));
                    if ($pieceExist) {
                        $translated = $this->get('translator')->trans('message_combinaison_existe');
                        $this->get('session')->getFlashBag()->add('info', $translated);
                    } else {
                        $em->persist($newPiece);
                        $em->flush();
                        $translated = $this->get('translator')->trans('succes_update');
                        $this->get('session')->getFlashBag()->add('info', $translated);
                    }

                    return new RedirectResponse($this->container->get('router')->generate('definirPieceJointe'));
                } else {
                    //die(dump($form->getErrorsAsString()));
                }
            }
        } else {
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {
                    $pieceExist = $em->getRepository('BanquemondialeBundle:PieceJointe')->findOneBy(array('typeOperation' => $newPiece->getTypeOperation(),
                        'formeJuridique' => $newPiece->getFormeJuridique(), 'document' => $newPiece->getDocument(), 'fonction' => $newPiece->getFonction()));
                    if ($pieceExist) {
                        $translated = $this->get('translator')->trans('message_combinaison_existe');
                        $this->get('session')->getFlashBag()->add('info', $translated);
                    } else {
                        $em->persist($newPiece);
                        $em->flush();
                        $translated = $this->get('translator')->trans('succes_add');
                        $this->get('session')->getFlashBag()->add('info', $translated);
                    }
                    return new RedirectResponse($this->container->get('router')->generate('definirPieceJointe'));
                } else {
                    //die(dump($form->getErrorsAsString()));
                }
            }
        }
        $listeCircuit = $em->getRepository('BanquemondialeBundle:PieceJointe')->getListPieceAJoindre($langue->getId());
        return $this->render('ParametrageBundle:ParameterPole:ajoutPieceJ.html.twig', array('form' => $form->createView(), 'listeCircuit' => $listeCircuit, 'idP' => $idP));
    }

    public function definirTarificationAction($id = null) {
        $newFrais = new Tarification();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        if (isset($id) && $id != 0) {

            $newFrais = $em->getRepository('ParametrageBundle:Tarification')->find($id);
            $typeTraduit = $em->getRepository('BanquemondialeBundle:TypeOperationTraduction')->getLibelleOperationByLanque($langue->getId(), $newFrais->getTypeOperation());
            $idf = $newFrais->getFormeJuridique()->getId();
            $idl = $langue->getId();
            $idPole = $newFrais->getPole()->getId();
            $listeLibelleTarification = $em->getRepository('ParametrageBundle:LibelleTarification')->findByPole($newFrais->getPole());
            $idLibelleTarificationDefinie = $em->getRepository('ParametrageBundle:LibelleTarification')->find($newFrais->getLibelleTarification())->getId();
            $formeJTraduit = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('formeJuridique' => $idf, 'langue' => $idl));
            $form = $this->createForm(new TarificationType(array('langue' => $langue, 'typeOpTraduit' => $typeTraduit, 'formeJTraduit' => $formeJTraduit)), $newFrais);


            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {

                    $fraisExist = $em->getRepository('ParametrageBundle:Tarification')->findOneBy(array('typeOperation' => $newFrais->getTypeOperation(),
                        'formeJuridique' => $newFrais->getFormeJuridique(), 'pole' => $newFrais->getPole(), 'typeDossier' => $newFrais->getTypeDossier(), 'libelleTarification' => $newFrais->getLibelleTarification()));
                    if ($fraisExist) {
                        if ($id == $fraisExist->getId()) {
                            $fraisExist->setMontant($newFrais->getMontant());
                            $em->persist($fraisExist);
                            $em->flush();

                            $translated = $this->get('translator')->trans('succes_update');
                            $this->get('session')->getFlashBag()->add('info', $translated);
                        } else {
                            $translated = $this->get('translator')->trans('message_combinaison_existe');
                            $this->get('session')->getFlashBag()->add('info', $translated);
                        }
                    } else {
                        $em->persist($newFrais);
                        $em->flush();
                        $translated = $this->get('translator')->trans('succes_update');
                        $this->get('session')->getFlashBag()->add('info', $translated);
                    }

                    //$newFrais = new Tarification();
                    //$form = $this->createForm(new TarificationType(array('langue' => $langue,'typeOpTraduit'=>null,'formeJTraduit'=>null)), $newFrais);
                    return new RedirectResponse($this->container->get('router')->generate('definirFraisDossier'));
                } else {
                    //die("invalid");
                }
            }
            $listeFrais = $em->getRepository('ParametrageBundle:Tarification')->getAllFraisdeConstitution($langue->getId());
            return $this->render('ParametrageBundle:ParameterPole:ajoutTarification.html.twig', array('form' => $form->createView(), 'listedesfrais' => $listeFrais, 'id' => $id, 'tarificationDefinie' => $newFrais, 'idPole' => $idPole, 'idLibelleTarificationDefinie' => $idLibelleTarificationDefinie));
        } else {

            $newFrais = new Tarification();
            $form = $this->createForm(new TarificationType(array('langue' => $langue, 'typeOpTraduit' => null, 'formeJTraduit' => null)), $newFrais);
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {

                    $fraisExist = $em->getRepository('ParametrageBundle:Tarification')->findOneBy(array('typeOperation' => $newFrais->getTypeOperation(),
                        'formeJuridique' => $newFrais->getFormeJuridique(), 'pole' => $newFrais->getPole(), 'typeDossier' => $newFrais->getTypeDossier(), 'libelleTarification' => $newFrais->getLibelleTarification()));
                    if ($fraisExist) {
                        $translated = $this->get('translator')->trans('message_combinaison_existe');
                        $this->get('session')->getFlashBag()->add('info', $translated);
                    } else {
                        $em->persist($newFrais);
                        $em->flush();
                        $translated = $this->get('translator')->trans('succes_add');
                        $this->get('session')->getFlashBag()->add('info', $translated);
                    }

                    $newFrais = new Tarification();
                    $form = $this->createForm(new TarificationType(array('langue' => $langue, 'typeOpTraduit' => null, 'formeJTraduit' => null)), $newFrais);
                }
            }
            $listeFrais = $em->getRepository('ParametrageBundle:Tarification')->getAllFraisdeConstitution($langue->getId());
            return $this->render('ParametrageBundle:ParameterPole:ajoutTarification.html.twig', array('form' => $form->createView(), 'listedesfrais' => $listeFrais, 'id' => $id, 'tarificationDefinie' => null, 'idPole' => null, 'idLibelleTarificationDefinie' => null));
        }
    }

    public function supprimerFraisAction($idF) {
        $em = $this->getDoctrine()->getManager();
        $frais = $em->getRepository('ParametrageBundle:Tarification')->find($idF);
        $translated = "";
        if ($frais) {
            $em->remove($frais);
            $em->flush();
            $translated = $this->get('translator')->trans('pole.suppression_message');
        }

        $this->get('session')->getFlashBag()->add('info', $translated);

        return $this->redirectToRoute('definirFraisDossier');
    }

    public function rechercherFraisAction() {

        $request = $this->get('request');
        $codLang = $request->getLocale();

        $em = $this->getDoctrine()->getManager();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);

        $data = $request->request->all()['tarification'];

        //die(dump($data));
        $idTypeOperation = null;
        $idFormeJuridique = null;
        $idPole = null;
        $idTypeDossier = null;
        $idLibelleTarification = null;
        $montant = null;


        if ($data["typeOperation"] != '') {
            $idTypeOperation = $em->getRepository('BanquemondialeBundle:TypeOperationTraduction')->find($data["typeOperation"])->getTypeOperation()->getId();
        }

        if ($data["formeJuridique"] != '') {
            $idFormeJuridique = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->find($data["formeJuridique"])->getFormeJuridique()->getId();
        }

        if ($data["typeDossier"] != '') {
            $idTypeDossier = $data["typeDossier"];
        }

        if ($data["pole"] != '') {
            $idPole = $data["pole"];
        }

        if (array_key_exists("libelleTarification", $data) && $data["libelleTarification"]) {
            $idLibelleTarification = $data["libelleTarification"];
        }

        if ($data["montant"] != '') {
            $montant = $data["montant"];
        }



        $idLangue = $langue->getId();
        //die(dump($idTypeOperation));
        $newFrais = new Tarification();

        $form = $this->createForm(new TarificationType(array('langue' => $langue, 'typeOpTraduit' => null, 'formeJTraduit' => null)), $newFrais);

        //$listeFrais = $em->getRepository('ParametrageBundle:Tarification')->rechercherFrais($data, $langue->getId());
        $listeFrais = $em->getRepository('ParametrageBundle:Tarification')->rechercherFraisSoumission($idTypeOperation, $idFormeJuridique, $idTypeDossier, $idPole, $idLibelleTarification, $montant, $idLangue);

        return $this->render('ParametrageBundle:ParameterPole:ajoutTarification.html.twig', array('form' => $form->createView(), 'listedesfrais' => $listeFrais, 'id' => null, 'tarificationDefinie' => null, 'idPole' => null, 'idLibelleTarificationDefinie' => null));
    }

    public function definirEtapeCreationAction($idE) {
        $newEtape = new EtapeCreation();
        $fonctionnaliteTraduit = null;
        $formeJTraduit = null;
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        if ((isset($idE) && ($idE != 0))) {
            $newEtape = $em->getRepository('ParametrageBundle:EtapeCreation')->find($idE);
            $fonctionnaliteTraduit = $em->getRepository('ParametrageBundle:FonctionnaliteTraduction')->findOneBy(array('fonctionnalite' => $newEtape->getEtape(), 'langue' => $langue));
            $formeJTraduit = $em->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->findOneBy(array('langue' => $langue, 'formeJuridique' => $newEtape->getFormeJuridique()->getId()));
        }
        $form = $this->createForm(new EtapeCreationType(array('langue' => $langue, 'fonctionnaliteTraduit' => $fonctionnaliteTraduit, 'formeJTraduit' => $formeJTraduit)), $newEtape);
        $translated = "";
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $etapeExist = $em->getRepository('ParametrageBundle:EtapeCreation')->find($idE);
                if ($etapeExist) {
                    $etapeExist->setOrdre($newEtape->getOrdre());
                    $em->persist($etapeExist);
                    $em->flush();
                    $translated = $this->get('translator')->trans('succes_update');
                } else {
                    $em->persist($newEtape);
                    $em->flush();
                    $translated = $this->get('translator')->trans('succes_add');
                }
            }

            $this->get('session')->getFlashBag()->add('info', $translated);
            return new RedirectResponse($this->container->get('router')->generate('definirEtapeCreation'));
        }
        $lesEtapes = $em->getRepository('ParametrageBundle:EtapeCreation')->getListEtapeBrouillonByLangue($langue);

        return $this->render('ParametrageBundle:ParameterPole:definirEtapeCreation.html.twig', array('form' => $form->createView(), 'lesEtapes' => $lesEtapes, 'idE' => $idE));
    }

    public function supprimerEtapeAction($idE) {
        $em = $this->getDoctrine()->getManager();
        $etapeSelect = $em->getRepository('ParametrageBundle:EtapeCreation')->find($idE);
        $translated = "";
        if ($etapeSelect) {
            $em->remove($etapeSelect);
            $em->flush();
            $translated = $this->get('translator')->trans('pole.suppression_message');
        }

        $this->get('session')->getFlashBag()->add('info', $translated);
        //return new RedirectResponse($this->container->get('router')->generate('ajouter_pole'));

        return $this->redirectToRoute('definirEtapeCreation');
    }

    public function menuCreationAction($active) {
        $em = $this->getDoctrine()->getManager();
        $listEtapes = $em->getRepository('ParametrageBundle:EtapeCreation')->findBy(array(), array('ordre' => 'asc'));
        return $this->render('DefaultBundle:Default:menuCreation.html.twig', array('active' => $active, 'listeEtapes' => $listEtapes));
    }

    public function chargeLibelleTarificationAction(Request $request) {


        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();

            $pole = $em->getRepository('ParametrageBundle:Pole')->find($request->get('idpole'));

            if (!$pole) {
                return new JsonResponse(array('error' => '1'));
            }
            //$regions = $em->getRepository('BanquemondialeBundle:Region')->getRegionByPays2($pays->getId());
            $libelleTarifications = $em->getRepository('ParametrageBundle:LibelleTarification')->findByPole($pole);


            $retour = array();
            $retourId = array();

            $i = 0;

            foreach ($libelleTarifications as $libelleTarification) {

                $retour[$i] = $libelleTarification->getLibelle();
                $retourId[$i] = $libelleTarification->getId();
                $i++;
            }


            return new JsonResponse(array('retour' => $retour, 'retourId' => $retourId));
        } else
            return new JsonResponse();
    }

}
