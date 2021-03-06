<?php

namespace BanquemondialeBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * EmployeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EmployeRepository extends EntityRepository {

    public function getDetailsEmploye($idE, $idLangue) {
        $qb = $this->createQueryBuilder('e')
                        ->where('e.id=:idE')
                        ->setParameters(array('idE' => $idE))->setMaxResults(1);
        $query = $qb->getQuery();
        $results = $query->getResult();
        $tabResult = array();
        $i = 0;
        foreach ($results as $result) {
            $pays = $result->getNationalite();
            $tabResult[$i]['id'] = $result->getId();
            $tabResult[$i]['nom'] = $result->getNom();
            $tabResult[$i]['prenom'] = $result->getPrenom();
            $sexe = $result->getSexe();
            $tabResult[$i]['dateEmbauche'] = $result->getDateEmbauche();
            $tabResult[$i]['matricule'] = $result->getMatricule();
            $tabResult[$i]['dateNaissance'] = $result->getDateNaissance();

            $tabResult[$i]['formation'] = $result->getFormation();

            $tabResult[$i]['dernierSalaire'] = $result->getDernierSalaire();
            $tabResult[$i]['dernierDiplome'] = $result->getDernierDiplome();
            $tabResult[$i]['emploiOccupe'] = $result->getEmploiOccupe();
            $tabResult[$i]['categorieProfessionnel'] = $result->getCategorieProfessionnel();


            $tabResult[$i]['nationalite'] = "";
            if ($pays) {
                $paysTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $pays->getid(), 'langue' => $idLangue));
                ($paysTrad) ? $tabResult[$i]['nationalite'] = $paysTrad->getNationalite() : $tabResult[$i]['nationalite'] = "";
            }

            $tabResult[$i]['sexe'] = "";
            if ($sexe) {
                $genreTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:GenreTraduction')->findOneBy(array('genre' => $sexe->getid(), 'langue' => $idLangue));
                ($genreTrad) ? $tabResult[$i]['sexe'] = $genreTrad->getLibelle() : $tabResult[$i]['sexe'] = "";
            }
            $i++;
        }
        return $tabResult;
    }

    public function getListeDesEmployes($idd, $idLangue) {
        $qb = $this->createQueryBuilder('e')
                        ->where('e.dossierDemande=:idd')
                        ->setParameters(array('idd' => $idd));
        $query = $qb->getQuery();
        $results = $query->getResult();
        $tabResult = array();
        $i = 0;
        foreach ($results as $result) {
            $pays = $result->getNationalite();
            $tabResult[$i]['id'] = $result->getId();
            $tabResult[$i]['nom'] = $result->getNom();
            $tabResult[$i]['prenom'] = $result->getPrenom();
            $sexe = $result->getSexe();
            $tabResult[$i]['dateEmbauche'] = $result->getDateEmbauche();
            $tabResult[$i]['matricule'] = $result->getMatricule();
            $tabResult[$i]['dateNaissance'] = $result->getDateNaissance();

            $tabResult[$i]['formation'] = $result->getFormation();

            $tabResult[$i]['dernierSalaire'] = $result->getDernierSalaire();
            $tabResult[$i]['dernierDiplome'] = $result->getDernierDiplome();
            $tabResult[$i]['emploiOccupe'] = $result->getEmploiOccupe();
            $tabResult[$i]['categorieProfessionnel'] = $result->getCategorieProfessionnel();


            $tabResult[$i]['nationalite'] = "";
            if ($pays) {
                $paysTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:PaysTraduction')->findOneBy(array('pays' => $pays->getid(), 'langue' => $idLangue));
                ($paysTrad) ? $tabResult[$i]['nationalite'] = $paysTrad->getNationalite() : $tabResult[$i]['nationalite'] = "";
            }

            $tabResult[$i]['sexe'] = "";
            if ($sexe) {
                $genreTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:GenreTraduction')->findOneBy(array('genre' => $sexe->getid(), 'langue' => $idLangue));
                ($genreTrad) ? $tabResult[$i]['sexe'] = $genreTrad->getLibelle() : $tabResult[$i]['sexe'] = "";
            }
            $i++;
        }
        return $tabResult;
    }

}
