<?php

namespace DefaultBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * factureOrangeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class factureOrangeRepository extends EntityRepository
{

    public function getMaxId()
    {

        // var_dump( $qb->getQuery()->getResult()['ref']);die();
        return  $aa=$this->createQueryBuilder('f')
            ->select('e')
            ->from('DefaultBundle:factureOrange', 'e')
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        // var_dump( $aa->getId());die(); return;
    }
}