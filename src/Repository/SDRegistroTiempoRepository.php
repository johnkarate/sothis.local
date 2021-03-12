<?php

namespace App\Repository;

use App\Entity\SDRegistroTiempo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SDRegistroTiempo|null find($id, $lockMode = null, $lockVersion = null)
 * @method SDRegistroTiempo|null findOneBy(array $criteria, array $orderBy = null)
 * @method SDRegistroTiempo[]    findAll()
 * @method SDRegistroTiempo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SDRegistroTiempoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SDRegistroTiempo::class);
    }

    // /**
    //  * @return SDRegistroTiempo[] Returns an array of SDRegistroTiempo objects
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
    public function findOneBySomeField($value): ?SDRegistroTiempo
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
