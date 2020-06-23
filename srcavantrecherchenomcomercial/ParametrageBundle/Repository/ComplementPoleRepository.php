<?php

namespace ParametrageBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ComplementPoleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ComplementPoleRepository extends EntityRepository
{	

public function findComplementByPole($idPole)
	{
		$query = $this->createQueryBuilder('p')->where('p.actif=true')
					->andWhere('p.pole = :pole')
					->setParameter('pole', $idPole);	
		
		$results = $query->getQuery()->getResult();
		$tabResult = array();
        $i = 0;
        foreach ($results as $result) { 
			$tabResult[$i]['valeur'] = "";		
			$tabResult[$i]['id'] = $result->getId();
            $tabResult[$i]['libelle'] = $result->getLibelle();			
            $tabResult[$i]['idPole'] = $result->getPole();
			$tabResult[$i]['typedonnee'] = $result->getTypedonnee();
            $i++;
        }
        return $tabResult;
	}
	
	
	
}
