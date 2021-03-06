<?php

namespace BanquemondialeBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * EntrepriseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EntrepriseRepository extends EntityRepository
{
	public function getNotaires($pole)
	{
		$query = $this->createQueryBuilder('e')
				->where('e.pole =:pole')
				->setParameter('pole',$pole);
		return $query->getQuery()->getResult();
	}
	
	public function getListeDesStructures(){
		$query=$this->createQueryBuilder('e')->join('e.pole','p')
				->where('p.sigle=:sgleAPIP or p.sigle=:sgleNT' )
				->setParameters(array('sgleAPIP'=>"APIP",'sgleNT'=>"NT"));
		return $query;       
	}
	
	public function getListCaisse(){
		$query=$this->createQueryBuilder('e')->join('e.pole','p')
				->where("p.sigle='CT'" );
				//die(dump($query->getQuery()->getResult()));
		return $query;       
	}
}
