<?php

namespace BanquemondialeBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TypeFonctionCommissaireRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TypeFonctionCommissaireRepository extends EntityRepository
{
    public function getListTypeFonctionCommissaireByLanque($idLangue){        
        $query=$this->createQueryBuilder('p')->join('p.langue', 'l');
        $query->where('l.id = :idl')->setParameter('idl', $idLangue);
		 $results = $query->getQuery()->getResult();
		 die(dump($results));
        return $query;
    }
    
    public function getTypeFonctionCommissaire($id,$idLangue)
	{
		$qb = $this->createQueryBuilder('t');
                
		$qb	->where('t.fonction = :idFonction and t.langue = :idLangue')
			->setParameter('idFonction', $id)
			->setParameter('idLangue', $idLangue)
		;	
		//echo "<script>alert($id);</script>";
		
		return $qb->getQuery()->getOneOrNullResult();
		
	}
}
