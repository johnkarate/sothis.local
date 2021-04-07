<?php

namespace App\Repository;

use App\Entity\AdobeGrabacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdobeGrabacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdobeGrabacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdobeGrabacion[]    findAll()
 * @method AdobeGrabacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdobeGrabacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdobeGrabacion::class);
    }

    // /**
    //  * @return AdobeGrabacion[] Returns an array of AdobeGrabacion objects
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
    public function findOneBySomeField($value): ?AdobeGrabacion
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
