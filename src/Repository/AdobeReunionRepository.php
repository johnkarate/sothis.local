<?php

namespace App\Repository;

use App\Entity\AdobeReunion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdobeReunion|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdobeReunion|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdobeReunion[]    findAll()
 * @method AdobeReunion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdobeReunionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdobeReunion::class);
    }

    // /**
    //  * @return AdobeReunion[] Returns an array of AdobeReunion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdobeReunion
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
