<?php

namespace Millarmony\SiteBundle\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * DiaryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DiaryRepository extends EntityRepository
{
    // Get back and return a list of concerts later than a date
    public function getDiary($date)
    {
        return $this->createQueryBuilder('d')
            ->where('d.date >= :date')
            ->setParameter('date', $date)
            ->orderBy('d.date', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    // Get back and return a list of concerts previous to a date (not used currently)
    /*
    public function getArchives($date)
    {
        return $this->createQueryBuilder('d')
            ->where('d.date < :date')
            ->setParameter('date', $date)
            ->orderBy('d.date', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
    */

    // Get back and return the next concert
    public function getNext($date)
    {
        return $this->createQueryBuilder('d')
            ->where('d.date >= :date')
            ->setParameter('date', $date)
            ->orderBy('d.date', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
