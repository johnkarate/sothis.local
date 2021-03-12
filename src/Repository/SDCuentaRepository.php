<?php

namespace App\Repository;

use App\Entity\SDCuenta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SDCuenta|null find($id, $lockMode = null, $lockVersion = null)
 * @method SDCuenta|null findOneBy(array $criteria, array $orderBy = null)
 * @method SDCuenta[]    findAll()
 * @method SDCuenta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SDCuentaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SDCuenta::class);
    }

    // /**
    //  * @return SDCuenta[] Returns an array of SDCuenta objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SDCuenta
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
