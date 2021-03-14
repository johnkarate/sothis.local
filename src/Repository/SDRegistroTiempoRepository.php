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
}
