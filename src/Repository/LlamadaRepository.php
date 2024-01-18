<?php

namespace App\Repository;

use App\Entity\Llamada;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Llamada|null find($id, $lockMode = null, $lockVersion = null)
 * @method Llamada|null findOneBy(array $criteria, array $orderBy = null)
 * @method Llamada[]    findAll()
 * @method Llamada[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LlamadaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Llamada::class);
    }

    // /**
    //  * @return Llamada[] Returns an array of Llamada objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Llamada
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
