<?php

namespace BanquemondialeBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PieceJointeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PieceJointeRepository extends EntityRepository {

    public function getListPieceAJoindre($idLangue) {
        $qb = $this->createQueryBuilder('p');
        $query = $qb->getQuery(); //->setParameter('idfj', $idFormeJ);
        $results = $query->getResult();
        $tabResult = array();
        $i = 0;
        foreach ($results as $result) {
            $tabResult[$i]['id'] = $result->getId();
            $formJ = $result->getFormeJuridique();
            $idf = $formJ->getId();
            $tabResult[$i]['idFormeJ'] = $idf;
            $formJTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->getLibelleFormeJuridiqueByLanque($idLangue, $idf);
            ($formJTrad) ? $tabResult[$i]['libelleFormeJ'] = $formJTrad->getLibelle() : $tabResult[$i]['libelleFormeJ'] = "";
            $idTyp = $result->getTypeOperation()->getId();
            $tabResult[$i]['idTypeOp'] = $idTyp;
            $typeOpTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:TypeOperationTraduction')->getLibelleOperationByLanque($idLangue, $idTyp);
            ($typeOpTrad) ? $tabResult[$i]['libelleTypeOp'] = $typeOpTrad->getLibelle() : $tabResult[$i]['libelleTypeOp'] = "";
            $iddoc = $result->getDocument()->getId();
            $tabResult[$i]['idDoc'] = $iddoc;
            $docTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:DocumentTraduction')->findOneBy(array('document' => $iddoc, 'langue' => $idLangue));
            ($docTrad) ? $tabResult[$i]['libelleDocument'] = $docTrad->getLibelle() : $tabResult[$i]['libelleDocument'] = "";
            $fct = $result->getFonction();
            $tabResult[$i]['fonctionName'] = "";
            if ($fct) {
                $tabResult[$i]['idFct'] = $fct->getId();
                $fctTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('fonction' => $fct->getId(), 'langue' => $idLangue));
                ($fctTrad) ? $tabResult[$i]['fonctionName'] = $fctTrad->getLibelle() : $tabResult[$i]['fonctionName'] = "";
            }
            $i++;
        }
        return $tabResult;
    }

    public function rechercherPiece($data, $idLangue) {
        $qb = $this->createQueryBuilder('p');
        if ($data['formeJuridique'] != '') {
            $qb->andWhere('p.formeJuridique = :formeJuridique')->setParameter('formeJuridique', $data['formeJuridique']);
        }
        if ($data['typeOperation'] != '') {
            $qb->andWhere('p.typeOperation = :typeOperation')->setParameter('typeOperation', $data['typeOperation']);
        }
        if ($data['document'] != '') {
            $qb->andWhere('p.document = :idDoc')->setParameter('idDoc', $data['document']);
        }
        if ($data['fonction'] != '') {
            $qb->andWhere('p.fonction = :fct')->setParameter('fct', $data['fonction']);
        }

        $query = $qb->getQuery();
        $results = $query->getResult();
        $tabResult = array();
        $i = 0;
        foreach ($results as $result) {
            $tabResult[$i]['id'] = $result->getId();
            $formJ = $result->getFormeJuridique();
            $idf = $formJ->getId();
            $tabResult[$i]['idFormeJ'] = $idf;
            $formJTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->getLibelleFormeJuridiqueByLanque($idLangue, $idf);
            ($formJTrad) ? $tabResult[$i]['libelleFormeJ'] = $formJTrad->getLibelle() : $tabResult[$i]['libelleFormeJ'] = "";
            $idTyp = $result->getTypeOperation()->getId();
            $tabResult[$i]['idTypeOp'] = $idTyp;
            $typeOpTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:TypeOperationTraduction')->getLibelleOperationByLanque($idLangue, $idTyp);
            ($typeOpTrad) ? $tabResult[$i]['libelleTypeOp'] = $typeOpTrad->getLibelle() : $tabResult[$i]['libelleTypeOp'] = "";
            $iddoc = $result->getDocument()->getId();
            $tabResult[$i]['idDoc'] = $iddoc;
            $docTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:DocumentTraduction')->findOneBy(array('document' => $iddoc, 'langue' => $idLangue));
            $tabResult[$i]['libelleDocument'] = $docTrad->getLibelle();
            $fct = $result->getFonction();
            $tabResult[$i]['fonctionName'] = "";
            if ($fct) {
                $tabResult[$i]['idFct'] = $fct->getId();
                $fctTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('fonction' => $fct->getId(), 'langue' => $idLangue));
                $tabResult[$i]['fonctionName'] = $fctTrad->getLibelle();
            }
            $i++;
        }
        return $tabResult;
    }

    public function findByPieceEntreprise($idTypeOp, $idFormeJ, $idLangue) {

        $qb = $this->createQueryBuilder('p')
                ->leftJoin('p.typeOperation', 'to')->addSelect('to')
                ->leftJoin('p.formeJuridique', 'f')->addSelect('f')
                ->where('to.id = :idtp')->andWhere('p.fonction is null')
                ->andWhere('f.id = :idfj')
                ->setParameters(array('idtp' => $idTypeOp, 'idfj' => $idFormeJ));
        $query = $qb->getQuery(); //->setParameter('idfj', $idFormeJ);
        $results = $query->getResult();


        $tabResult = array();
        $i = 0;
        foreach ($results as $result) {
            $doc = $result->getDocument();
            $tabResult[$i]['idDoc'] = $doc->getId();
            $tabResult[$i]['nomPiece'] = NULL;
            $docTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:DocumentTraduction')->findOneBy(array('document' => $doc->getid(), 'langue' => $idLangue));
            ($docTrad) ? $tabResult[$i]['libelle'] = $docTrad->getLibelle() : $tabResult[$i]['libelle'] = "";
            $i++;
        }
        return $tabResult;
    }

    public function findPieceAdministrateurs($idTypeOp, $idFormeJ, $idLangue) {
        $qb = $this->createQueryBuilder('p')
                ->leftJoin('p.typeOperation', 'to')->addSelect('to')
                ->leftJoin('p.formeJuridique', 'f')->addSelect('f')
                ->where('to.id = :idtp')->andWhere('f.id = :idfj')->andWhere('p.fonction is not null')
                ->setParameters(array('idtp' => $idTypeOp, 'idfj' => $idFormeJ));
        $results = $qb->getQuery()->getResult();
        $tabResult = array();
        $i = 0;
        foreach ($results as $result) {
            $doc = $result->getDocument();
            $tabResult[$i]['idDoc'] = $doc->getId();
            $tabResult[$i]['nomPiece'] = NULL;
            $docTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:DocumentTraduction')->findOneBy(array('document' => $doc->getId(), 'langue' => $idLangue));
            $tabResult[$i]['libelle'] = $docTrad->getLibelle();
            $fct = $result->getFonction();
            if ($fct) {
                $tabResult[$i]['idFct'] = $fct->getId();
                $fctTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('fonction' => $fct->getId(), 'langue' => $idLangue));
                $tabResult[$i]['fonctionName'] = $fctTrad->getLibelle();
            }
            $i++;
        }
        return $tabResult;
    }
	/*
    public function findPieceAssocie($idd, $idTypeOp, $idFormeJ, $idLangue) {
        $queryAss = $this->getEntityManager()->createQueryBuilder()->from('BanquemondialeBundle\Entity\Representant', 'r')
                        ->leftJoin('r.dossierDemande', 'dd')->select('r')->addSelect('dd')->where('dd.id =:idd')->setParameter('idd', $idd);
        $listAss = $queryAss->getQuery()->getResult();	
        $tabResult = array();
        foreach ($listAss as $associe) {
            if ($associe->getFonction()) {				
                $qb = $this->createQueryBuilder('p')
                        ->join('p.typeOperation', 'to1')->addSelect('to1')
                        ->join('p.formeJuridique', 'f')->addSelect('f')
                        ->join('p.fonction', 'fct')->addSelect('fct')
                        ->where('to1.id = :idtp')->andWhere('f.id = :idfj')->andWhere('fct.id = :idFct')
                        ->setParameters(array('idtp' => $idTypeOp, 'idfj' => $idFormeJ, 'idFct' => $associe->getFonction()->getId()));
				
                $results = $qb->getQuery()->getResult();
				
                $i = 0;
                foreach ($results as $result) {
                    $doc = $result->getDocument();
                    $tabResult[$i]['idDoc'] = $doc->getId();
                    $tabResult[$i]['nomPiece'] = NULL;
                    $docTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:DocumentTraduction')->findOneBy(array('document' => $doc->getId(), 'langue' => $idLangue));
                    ($docTrad) ? $tabResult[$i]['libelle'] = $docTrad->getLibelle() : $tabResult[$i]['libelle'] = "";
                    $fct = $result->getFonction();
                    if ($fct) {
                        $tabResult[$i]['idFct'] = $fct->getId();
                        $fctTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('fonction' => $fct->getId(), 'langue' => $idLangue));
                        ($fctTrad) ? $tabResult[$i]['fonctionName'] = $fctTrad->getLibelle() : $tabResult[$i]['fonctionName'] = "";

                        $tabResult[$i]['associeName'] = $associe->getPrenom() . ' ' . $associe->getNom();
                    }
                    $i++;
                }
            }			
            return $tabResult;
        }
    }
	*/
	public function findPieceAssocie($idd, $idTypeOp, $idFormeJ, $idLangue) {
        $queryAss = $this->getEntityManager()->createQueryBuilder()->from('BanquemondialeBundle\Entity\Representant', 'r')
                        ->leftJoin('r.dossierDemande', 'dd')->select('r')->addSelect('dd')->where('dd.id =:idd')->setParameter('idd', $idd);
        $listAss = $queryAss->getQuery()->getResult();
        $tabResult = array();
		$i = 0;
        foreach ($listAss as $associe) {
            if ($associe->getFonction()) {				
                $qb = $this->createQueryBuilder('p')
                        ->join('p.typeOperation', 'to1')->addSelect('to1')
                        ->join('p.formeJuridique', 'f')->addSelect('f')
                        ->join('p.fonction', 'fct')->addSelect('fct')
                        ->where('to1.id = :idtp')->andWhere('f.id = :idfj')->andWhere('fct.id = :idFct')
                        ->setParameters(array('idtp' => $idTypeOp, 'idfj' => $idFormeJ, 'idFct' => $associe->getFonction()->getId()));
				
                $results = $qb->getQuery()->getResult();

                foreach ($results as $result) {
                    $doc = $result->getDocument();
                    $tabResult[$i]['idDoc'] = $doc->getId();
                    $tabResult[$i]['nomPiece'] = NULL;
                    $docTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:DocumentTraduction')->findOneBy(array('document' => $doc->getId(), 'langue' => $idLangue));
                    ($docTrad) ? $tabResult[$i]['libelle'] = $docTrad->getLibelle() : $tabResult[$i]['libelle'] = "";
                    $fct = $result->getFonction();
                    if ($fct) {
                        $tabResult[$i]['idFct'] = $fct->getId();
                        $fctTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('fonction' => $fct->getId(), 'langue' => $idLangue));
                        ($fctTrad) ? $tabResult[$i]['fonctionName'] = $fctTrad->getLibelle() : $tabResult[$i]['fonctionName'] = "";

                        $tabResult[$i]['associeName'] = $associe->getPrenom() . ' ' . $associe->getNom();
						$tabResult[$i]['idAssocie'] = $associe->getId();
                    }
                    $i++;
                }
				
            }	
				
        }
       // die(dump($tabResult));
		return $tabResult;

    }

    public function rechercherPieceAJoindre($idTypeOperation, $idFormeJuridique, $idDocument, $idFonction, $idLangue) {
        $qb = $this->createQueryBuilder('p');

        if ($idFormeJuridique) {
            $qb->andWhere('p.formeJuridique = :formeJuridique')->setParameter('formeJuridique', $idFormeJuridique);
        }
        if ($idTypeOperation) {
            $qb->andWhere('p.typeOperation = :typeOperation')->setParameter('typeOperation', $idTypeOperation);
        }
        if ($idDocument) {
            $qb->andWhere('p.document = :document')->setParameter('document', $idDocument);
        }
        if ($idFonction) {
            $qb->andWhere('p.fonction = :fonction')->setParameter('fonction', $idFonction);
        }

        $query = $qb->getQuery();
        $results = $query->getResult();
        $tabResult = array();
        $i = 0;
        foreach ($results as $result) {
            $tabResult[$i]['id'] = $result->getId();
            $formJ = $result->getFormeJuridique();
            $idf = $formJ->getId();
            $tabResult[$i]['idFormeJ'] = $idf;
            $formJTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:FormeJuridiqueTraduction')->getLibelleFormeJuridiqueByLanque($idLangue, $idf);
            ($formJTrad) ? $tabResult[$i]['libelleFormeJ'] = $formJTrad->getLibelle() : $tabResult[$i]['libelleFormeJ'] = "";
            $idTyp = $result->getTypeOperation()->getId();
            $tabResult[$i]['idTypeOp'] = $idTyp;
            $typeOpTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:TypeOperationTraduction')->getLibelleOperationByLanque($idLangue, $idTyp);
            ($typeOpTrad) ? $tabResult[$i]['libelleTypeOp'] = $typeOpTrad->getLibelle() : $tabResult[$i]['libelleTypeOp'] = "";
            $iddoc = $result->getDocument()->getId();
            $tabResult[$i]['idDoc'] = $iddoc;
            $docTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:DocumentTraduction')->findOneBy(array('document' => $iddoc, 'langue' => $idLangue));
            $tabResult[$i]['libelleDocument'] = $docTrad->getLibelle();
            $fct = $result->getFonction();
            $tabResult[$i]['fonctionName'] = "";
            if ($fct) {
                $tabResult[$i]['idFct'] = $fct->getId();
                $fctTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:FonctionTraduction')->findOneBy(array('fonction' => $fct->getId(), 'langue' => $idLangue));
                $tabResult[$i]['fonctionName'] = $fctTrad->getLibelle();
            }
            $i++;
        }
        return $tabResult;
    }

    public function findPieceByForme($idFormeJ, $idLangue) {
        $qb = $this->createQueryBuilder('p')
                ->leftJoin('p.formeJuridique', 'f')->addSelect('f')
                ->where('f.id = :idfj')
                ->setParameters(array('idfj' => $idFormeJ));
        $results = $qb->getQuery()->getResult();

        //die(dump($results));
        $tabResult = array();
        $i = 0;
        foreach ($results as $result) {
            $doc = $result->getDocument();
            $tabResult[$i]['nomPiece'] = NULL;
            $docTrad = $this->getEntityManager()->getRepository('BanquemondialeBundle:DocumentTraduction')->findOneBy(array('document' => $doc->getId(), 'langue' => $idLangue));
            ($docTrad) ? $tabResult[$i]['libelle'] = $docTrad->getLibelle() : $tabResult[$i]['libelle'] = "";
            $fct = $result->getFonction();
            $i++;
        }
        return $tabResult;
    }

}
