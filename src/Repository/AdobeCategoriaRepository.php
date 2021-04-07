<?php

namespace App\Repository;

use App\Entity\AdobeCategoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdobeCategoria|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdobeCategoria|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdobeCategoria[]    findAll()
 * @method AdobeCategoria[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdobeCategoriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdobeCategoria::class);
    }

    // /**
    //  * @return AdobeCategoria[] Returns an array of AdobeCategoria objects
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
    public function findOneBySomeField($value): ?AdobeCategoria
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
