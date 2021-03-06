<?php

namespace BanquemondialeBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PrefectureRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PrefectureRepository extends EntityRepository
{
    public function getListPrefecture($idP)
    {
        $query = $this->createQueryBuilder('p')->where('p.actif=true');
        if($idP){
            $query->where('p.id = :idp')->setParameter('idp', $idP);       
        }
        //die(dump($query->getQuery()->getResult()));
        return $query;
    }
}
