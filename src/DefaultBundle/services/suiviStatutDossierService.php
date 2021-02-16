<?php

namespace DefaultBundle\services;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\Request;

class suiviStatutDossierService extends Controller
{
    public function getDossierDepot($dateDebut=null,$dateFin=null)
    {
        $em = $this->getDoctrine()->getManager();
            $statDepot=$em->getRepository('BanquemondialeBundle:DossierDemande')->getDossierDeposerByPeriode($dateDebut,$dateFin);
            return $statDepot;
    }

    public function getAndsetStatQuittance($action='get',$numDossier=null,$dateDebut=null,$dateFin=null)
    {
        $em = $this->getDoctrine()->getManager();
        if($action=='get'){
            $dossierQuitancer=$em->getRepository('BanquemondialeBundle:DossierDemande')->getQuittancesValiderByPeriode($dateDebut,$dateFin);
            $dossierNonQuitancer=$em->getRepository('BanquemondialeBundle:DossierDemande')->getQuittancesNonValiderByPeriode($dateDebut,$dateFin);
            $statCaisse=[
                'quittancer'=>$dossierQuitancer,
                'nonquittancer'=>$dossierNonQuitancer
            ];
            return $statCaisse;
        }
        elseif ($action=='set'){
            $dossier=$em->getRepository('BanquemondialeBundle:DossierDemande')->findOneBy(['id'=>$numDossier],[]);
            //die(dump($dossier));
            $dossier->setStatSuivieQuittance(1);
            $em->persist($dossier);
            $em->flush();
        }
    }

    public function getAndsetStatSaisie($action='get',$numDossier=null,$dateDebut=null,$dateFin=null)
    {
        $em = $this->getDoctrine()->getManager();
        if($action=='get'){
            $dossierSaisie=$em->getRepository('BanquemondialeBundle:DossierDemande')->getDossierSaisieByPeriode($dateDebut,$dateFin);
            $dossierNonSaisie=$em->getRepository('BanquemondialeBundle:DossierDemande')->getDossierNonEncoreSaisieByPeriode($dateDebut,$dateFin);
            $statSaisie=[
                'dossierSaisie'=>$dossierSaisie,
                'dossierNonSaisie'=>$dossierNonSaisie
            ];
            return $statSaisie;
        }
        elseif ($action=='set'){
            $dossier=$em->getRepository('BanquemondialeBundle:DossierDemande')->findOneBy(['id'=>$numDossier],[]);
            $dossier->setStatSuivieSaisie(1);
            if (empty($dossier->getDateSaisie())){
                $dossier->setDateSaisie(new \DateTime());
            }

            $em->persist($dossier);
            $em->flush();
        }
    }

    public function getAndsetStatRccm($action='get',$numDossier=null,$dateDebut=null,$dateFin=null)
    {
        $em = $this->getDoctrine()->getManager();
        if($action=='get'){
            $rccmTraiter=$em->getRepository('BanquemondialeBundle:DossierDemande')->getRccmTraiterByPeriode($dateDebut,$dateFin);
            $rccmNonTraiter=$em->getRepository('BanquemondialeBundle:DossierDemande')->getRccmNonEncoreTraiterByPeriode($dateDebut,$dateFin);
            $rapportRccmTraiter=$em->getRepository('BanquemondialeBundle:DossierDemande')->getRapportRccmTraiterByPeriode($dateDebut,$dateFin);
            $statGreff=[
                'rccmTraiter'=>$rccmTraiter,
                'rccmNonTraiter'=>$rccmNonTraiter,
                'rapportTraitement'=>$rapportRccmTraiter
            ];
           // die(dump('ooo'));
            return $statGreff;
        }
        elseif ($action=='set'){
            $dossier=$em->getRepository('BanquemondialeBundle:DossierDemande')->findOneBy(['id'=>$numDossier],[]);
            $dossier->setStatSuivieRccm(1);
            $em->persist($dossier);
            $em->flush();
        }
    }

    public function getDossierTransmitDNI($dateDebut=null,$dateFin=null)
    {
        $em = $this->getDoctrine()->getManager();
        $statDepotStansmitDNI=$em->getRepository('BanquemondialeBundle:DossierDemande')->getDossierTransmitDNIByPeriode($dateDebut,$dateFin);
        $statDepotNonTransmitDNI=$em->getRepository('BanquemondialeBundle:DossierDemande')->getDossierEnsouffranceDNIByPeriode($dateDebut,$dateFin);
        return [
            'rccmTransmit'=>$statDepotStansmitDNI,
            'rccmNonTransmit'=>$statDepotNonTransmitDNI,
            ];
    }

    public function getAndsetStatRetrait($action='get',$numDossier=null,$dateDebut=null,$dateFin=null)
    {
        $em = $this->getDoctrine()->getManager();
        if($action=='get'){
            $dossierRetirer=$em->getRepository('BanquemondialeBundle:DossierDemande')->getDossierRetirerPeriode($dateDebut,$dateFin);
            $statDossierRetirer=$em->getRepository('BanquemondialeBundle:DossierDemande')->getStatistiqueDossierRetirerByAgentPeriode($dateDebut,$dateFin);
            $dossierNonRetirer=$em->getRepository('BanquemondialeBundle:DossierDemande')->getDossierNonEncoreRetirerByPeriode($dateDebut,$dateFin);
            $statRetrait=[
                'dossierRetirer'=>$dossierRetirer,
                'dossierNonRetirer'=>$dossierNonRetirer,
                'statDossierRetirer'=>$statDossierRetirer,

            ];
           // die(dump($statRetrait));
            return $statRetrait;
        }
        elseif ($action=='set'){
            $dossier=$em->getRepository('BanquemondialeBundle:DossierDemande')->findOneBy(['id'=>$numDossier],[]);
            $dossier->setDateRetrait(new \DateTime());
            $dossier->setUtilisateurRetrait($this->getUser());
            $dossier->setEstRetire(true);
            $em->persist($dossier);
            $em->flush();
        }
    }


    public function getRccmNifLogique($dateDebut=null,$dateFin=null)
    {
        $em = $this->getDoctrine()->getManager();
        $rccmLogique=$em->getRepository('BanquemondialeBundle:DossierDemande')->getRccmLogique($dateDebut,$dateFin);
        $nifLogique=$em->getRepository('BanquemondialeBundle:DossierDemande')->getNifLogique($dateDebut,$dateFin);
        $rccmNifLogique=[
            'rccmLogique'=>$rccmLogique,
            'nifLogique'=>$nifLogique
        ];
        return $rccmNifLogique;
    }


    public function getRccmNifPhysique($dateDebut=null,$dateFin=null)
    {
        $em = $this->getDoctrine()->getManager();
        $rccmPhysique=$em->getRepository('BanquemondialeBundle:DossierDemande')->getRccmPhysique($dateDebut,$dateFin);
        $nifPhysique=$em->getRepository('BanquemondialeBundle:DossierDemande')->getNifPhysique($dateDebut,$dateFin);
        $rccmNifPhysique=[
            'rccmPhysique'=>$rccmPhysique,
            'nifPhysique'=>$nifPhysique
        ];
        return $rccmNifPhysique;
    }
    public function getStatistiqueGeneralCircuitDossier($dateDebut=null,$dateFin=null)
    {
        $em = $this->getDoctrine()->getManager();
        $statGenerale=$em->getRepository('BanquemondialeBundle:DossierDemande')->getStatistiqueGeneralCircuitDossierBydate($dateDebut,$dateFin);
        return $statGenerale;
    }

}

?>