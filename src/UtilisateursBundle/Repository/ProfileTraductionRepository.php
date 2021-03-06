<?php

namespace UtilisateursBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProfileTraductionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProfileTraductionRepository extends EntityRepository
{
      public function getProfileTraduction($id, $idLangue) {
        $qb = $this->createQueryBuilder('t')
                ->join('t.profile','p')
                ->join('t.langue','l');


        $qb->where('p.id = :idProfile and l.code= :idLangue')
                ->setParameter('idProfile', $id)
                ->setParameter('idLangue', $idLangue)
        ;
     
        return $qb->getQuery()->getOneOrNullResult();
    }
}
