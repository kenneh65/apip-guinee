<?php

namespace DefaultBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ReglageActivation
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReglageActivationRepository extends EntityRepository
{
	public function findFirst() {
        $query = $this->createQueryBuilder('r')               
                ->setMaxResults(1);
        //die(dump($query->getQuery()->getOneOrNullResult()));
        return $query->getQuery()->getOneOrNullResult();
    }
}
