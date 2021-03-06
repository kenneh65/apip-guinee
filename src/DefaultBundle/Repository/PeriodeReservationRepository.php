<?php

namespace DefaultBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PeriodeReservationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PeriodeReservationRepository extends EntityRepository
{
    public function findBylocalAndFormeJurique($local) {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.formeJuridiqueTraduction', 'f')
            ->innerJoin('f.langue', 'l')
            ->where('l.id = :idl')
            ->setParameter('idl',$local)
            ->orderBy('p.nombre', 'asc')
        ;
        $query = $qb->getQuery();
        $results = $query->getResult();
        return($results);
    }

}
