<?php

namespace App\Repository;

use App\Entity\SDTicket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SDTicket|null find($id, $lockMode = null, $lockVersion = null)
 * @method SDTicket|null findOneBy(array $criteria, array $orderBy = null)
 * @method SDTicket[]    findAll()
 * @method SDTicket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SDTicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SDTicket::class);
    }

    // /**
    //  * @return SDTicket[] Returns an array of SDTicket objects
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
    public function findOneBySomeField($value): ?SDTicket
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
