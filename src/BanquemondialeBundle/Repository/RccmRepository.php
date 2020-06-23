<?php

namespace BanquemondialeBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * RccmRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RccmRepository extends EntityRepository
{
	public function verifierNumeroFormalite($numRccmFormalite, $idd) {
        $db = $this->createQueryBuilder('r')
                ->where('substring(r.numRccmFormalite,14) = substring(:numRccmFormalite,14)')
				->andWhere('substring(r.numRccmFormalite,8,4) = substring(:numRccmFormalite,8,4)')
                ->andWhere('r.dossierDemande != :idd')
                ->setParameters(array('numRccmFormalite' => $numRccmFormalite, 'idd' =>  $idd));
        return $db->getQuery()->getResult();
    }
	
	public function verifierNumeroEntreprise($numRccmEntreprise, $idd) {
        $db = $this->createQueryBuilder('r')
				->where('substring(r.numRccmEntreprise,14) = substring(:numRccmEntreprise,14)')
				->andWhere('substring(r.numRccmEntreprise,8,4) = substring(:numRccmEntreprise,8,4)')
                ->andWhere('r.dossierDemande != :idd')
                ->setParameters(array('numRccmEntreprise' => $numRccmEntreprise, 'idd' =>  $idd));
        return $db->getQuery()->getResult();
    }
	
	public function findDossierByRccmAndNumeroDossier($numeroDossier, $numRccmEntreprise) {
		
        $db = $this->createQueryBuilder('r')
				->join('r.dossierDemande','d')
				->where('d.numeroDossier = :numeroDossier')
				->andWhere('r.numRccmEntreprise = :numRccmEntreprise')
                ->setParameters(array('numeroDossier' =>  $numeroDossier, 'numRccmEntreprise' => $numRccmEntreprise));
        return $db->getQuery()->getResult();
    }
	
	public function findDossierValideById($idd) {
        $db = $this->createQueryBuilder('r')
				->join('r.dossierDemande','d')
				->where('d.id = :idd')
				->andWhere('d.statutValidationChefGreffe = 2')
                ->setParameters(array('idd' =>  $idd));
        return $db->getQuery()->getOneOrNullResult();
    }
}
