<?php

namespace BanquemondialeBundle\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;

/**
 * DossierDemandeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QuittanceRepository extends EntityRepository {

    public function findQuittanceValideByParametres($data, $idLangue, $idUser, $limit = null, $lieu = null) {

        $query = $this->createQueryBuilder('a')->where('a.isPaid = true')
            ->leftJoin('a.utilisateur', 'u')
            ->leftJoin('u.entreprise', 'e');

        if ($lieu) {
            //$query->leftJoin('a.utilisateur', 'u')
            //->leftJoin('u.entreprise', 'e');
            //$query->andwhere('u.id=:idu')->setParameter('idu', $idUser);
            $query->join('a.sousPrefecture', 'sp')->andWhere('sp.id=:idSP')->setParameter('idSP', $lieu);
        }


        if ($data['numeroDossier'] != '') {
            $query->andWhere('LOWER(a.numeroDossier) = :numeroDossier')
                ->setParameter('numeroDossier', $data['numeroDossier']);
        }
        if ($data['typeDossier'] != '') {
            $query->join(('a.dossierDemande'), 'dd');
            $query->andWhere('dd.typeDossier = :idtd')
                ->setParameter('idtd', $data['typeDossier']);
        }

        if ($data['denominationSociale'] != '') {
            $query->andWhere('LOWER(a.denominationSociale) like :denominationSociale')
                ->setParameter('denominationSociale', '%' . $data['denominationSociale'] . '%');
        }

        if ($data['formeJuridique'] != '') {
            $query->andWhere('a.formeJuridique = :formeJuridique')
                ->setParameter('formeJuridique', $data['formeJuridique']);
        }

        if ($data['typeDossier'] != '') {
            $query->andWhere('a.typeDossier = :typeDossier')
                ->setParameter('typeDossier', $data['typeDossier']);
        }

        if ($data['numeroQuittance'] != '') {
            $query->andWhere('LOWER(a.numeroQuittance) = :numeroQuittance')
                ->setParameter('numeroQuittance', $data['numeroQuittance']);
        }

        if ($data['datePaiementDebut'] != '') {
            $query->andWhere('DATE_DIFF(a.datePaiement,:datePaiementDebut)>=0')
                ->setParameter('datePaiementDebut', new DateTime($data['datePaiementDebut']));
        }

        if ($data['datePaiementFin'] != '') {
            $query->andWhere('DATE_DIFF(a.datePaiement,:datePaiementFin)<=0')
                ->setParameter('datePaiementFin', new DateTime($data['datePaiementFin']));
        }

        if ($data['entreprise'] != '') {
            $query->andWhere('e.denomination = :entreprise')
                ->setParameter('entreprise', $data['entreprise']);
        }


        if ($limit) {
            $query->setMaxResults($limit);
        }
        $query->orderBy('a.id', 'desc');
        $results = $query->getQuery()->getResult();
        $tabResult = array();
        $i = 0;
        foreach ($results as $result) {
            $tabResult[$i]['id'] = $result->getId();
            $formJ = $result->getFormeJuridique();
            $idf = $formJ->getId();
            if ($formJ) {
                $idf = $formJ->getId();
                $tabResult[$i]['idFormeJ'] = $idf;
                $formJTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->getLibelleFormeJuridiqueByLanque($idLangue, $idf);
                ($formJTrad) ? $tabResult[$i]['libelleFormeJ'] = $formJTrad->getLibelle() : $tabResult[$i]['libelleFormeJ'] = "";
            } else {
                $tabResult[$i]['libelleFormeJ'] = "";
            }
            $typeDossier = $this->getEntityManager()->getRepository('BanquemondialeBundle:TypeDossier')->find($result->getTypeDossier());
            $tabResult[$i]['typeDossier'] = $typeDossier->getAttestationPayement();

            $tabResult[$i]['denominationSociale'] = $result->getDossierDemande()->getDenominationSociale();
            $tabResult[$i]['numeroDossier'] = $result->getNumeroDossier();
            $tabResult[$i]['numeroQuittance'] = $result->getNumeroQuittance();
            $tabResult[$i]['montantVerse'] = $result->getMontantVerse();
            $tabResult[$i]['montantRestant'] = $result->getMontantRestant();
            $tabResult[$i]['datePaiement'] = $result->getDatePaiement();
            $tabResult[$i]['dateFacturation'] = $result->getDateFacturation();
            $tabResult[$i]['montantRestant'] = $result->getMontantRestant();
            $tabResult[$i]['typeDossier'] = $result->getDossierDemande()->getTypeDossier()->getLibelle();
            ($result->getUtilisateur() && $result->getUtilisateur()->getEntreprise()) ? $tabResult[$i]['entreprise'] = $result->getUtilisateur()->getEntreprise()->getDenomination() : $tabResult[$i]['entreprise'] = "";
            $i++;
        }

        return $tabResult;
    }

    public function findQuittanceEnAttenteByParametres($data, $idLangue, $idUser, $limit = null, $lieu = null) {

        $query = $this->createQueryBuilder('a')
            ->where('a.isPaid = false')
            ->andWhere('a.statut != 3 or a.statut is null');
//                ->leftJoin('a.utilisateur', 'u')
//                ->leftJoin('u.entreprise', 'e');

        if ($lieu) {
            //die(dump($lieu));
            //$query->leftJoin('a.utilisateur', 'u')
            //->leftJoin('u.entreprise', 'e');
            //$query->andwhere('u.id=:idu')->setParameter('idu', $idUser);
            $query->innerJoin('a.sousPrefecture', 'sp')->andWhere('sp.id=:idSP')->setParameter('idSP', $lieu);
        }

        if (!empty($data)) {
            if ($data['numeroDossier'] != '') {
                $query->andWhere('LOWER(a.numeroDossier) = :numeroDossier')
                    ->setParameter('numeroDossier', $data['numeroDossier']);
            }
            if ($data['typeDossier'] != '') {
                $query->join(('a.dossierDemande'), 'dd');
                $query->andWhere('dd.typeDossier = :idtd')
                    ->setParameter('idtd', $data['typeDossier']);
            }
            if ($data['denominationSociale'] != '') {
                $query->andWhere('LOWER(a.denominationSociale) like :denominationSociale')
                    ->setParameter('denominationSociale', '%' . $data['denominationSociale'] . '%');
            }
            if ($data['formeJuridique'] != '') {
                //die(dump($data['formeJuridique']));
                $query->andWhere('a.formeJuridique = :formeJuridique')
                    ->setParameter('formeJuridique', $data['formeJuridique']);
            }
            if ($data['datePaiementDebut'] != '') {
                $query->andWhere('DATE_DIFF(a.dateFacturation,:datePaiementDebut)>=0')
                    ->setParameter('datePaiementDebut', new DateTime($data['datePaiementDebut']));
            }
            if ($data['datePaiementFin'] != '') {
                $query->andWhere('DATE_DIFF(a.dateFacturation,:datePaiementFin)<=0')
                    ->setParameter('datePaiementFin', new DateTime($data['datePaiementFin']));
            }
            if ($data['nomAgentDepot'] != '') {

                $query->leftJoin('a.dossierDemande', 'dd')->leftJoin('dd.utilisateur', 'u');
                $query->andWhere('LOWER(u.nom) like :nomagent or LOWER(u.prenom) like :nomagent')
                    ->setParameter('nomagent', '%' . $data['nomAgentDepot'] . '%');
            }
        }
        /*
          if (($data['datePaiementDebut'] == '')&&($data['datePaiementFin'] == '')) {
          $today = date('Y-m-d');
          $query->andWhere('DATE_DIFF(a.dateFacturation,:datePaiementFin)<=0')
          ->setParameter('datePaiementFin', new DateTime($today));
          $query->andWhere('DATE_DIFF(a.dateFacturation,:datePaiementDebut)>=0')
          ->setParameter('datePaiementDebut', new DateTime($today));
          }
         */
        if ($limit) {
            $query->setMaxResults($limit);
        }
        $query->orderBy('a.dateFacturation', 'desc');
        $results = $query->getQuery()->getResult();
        //die(dump($query->getQuery()));
        $tabResult = array();
        $i = 0;
        foreach ($results as $result) {
            $tabResult[$i]['id'] = $result->getId();
            $formJ = $result->getFormeJuridique();
            if ($formJ) {
                $idf = $formJ->getId();
                $tabResult[$i]['idFormeJ'] = $idf;
                $formJTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->getLibelleFormeJuridiqueByLanque($idLangue, $idf);
                ($formJTrad) ? $tabResult[$i]['libelleFormeJ'] = $formJTrad->getLibelle() : $tabResult[$i]['libelleFormeJ'] = "";
            } else {
                $tabResult[$i]['libelleFormeJ'] = "";
            }

            $tabResult[$i]['denominationSociale'] = $result->getDossierDemande()->getDenominationSociale();
            $tabResult[$i]['numeroDossier'] = $result->getNumeroDossier();
            $tabResult[$i]['numeroQuittance'] = $result->getNumeroQuittance();
            $tabResult[$i]['montantVerse'] = $result->getMontantVerse();
            $tabResult[$i]['montantRestant'] = $result->getMontantRestant();
            $tabResult[$i]['datePaiement'] = $result->getDatePaiement();
            $tabResult[$i]['dateFacturation'] = $result->getDateFacturation();
            $tabResult[$i]['montantRestant'] = $result->getMontantRestant();
            $tabResult[$i]['typeDossier'] = $result->getDossierDemande()->getTypeDossier()->getLibelle();
            $tabResult[$i]['idTypeDossier']=$result->getDossierDemande()->getTypeDossier()->getId();
            $createur = $result->getDossierDemande()->getUtilisateur();
            $userDepot=$result->getDossierDemande()->getUtilisateurDepot();
            $userSaisi=($createur)?$createur->getPrenom() . " " . $createur->getNom() : "";
            $tabResult[$i]['nomAgentDepot'] = ($userDepot) ? $userDepot->getPrenom() . " " . $userDepot->getNom() : $userSaisi;
            $tabResult[$i]['structure'] = ($createur && $createur->getEntreprise()) ? $createur->getEntreprise()->getDenomination() : "";
            $statutDocCollected = $this->getEntityManager()->getRepository('BanquemondialeBundle:DocumentCollected')->getStatutPoleCaisse($result->getDossierDemande()->getId());
            //die(dump($statutDocCollected));
            if ($statutDocCollected) {
                unset($tabResult[$i]);
                $i--;
            }
            $i++;
        }
        //  die(dump($tabResult));
        return $tabResult;

    }

}
