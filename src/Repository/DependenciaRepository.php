<?php

namespace App\Repository;

use App\Entity\Dependencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dependencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dependencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dependencia[]    findAll()
 * @method Dependencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DependenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dependencia::class);
    }

    // /**
    //  * @return Dependencia[] Returns an array of Dependencia objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Dependencia
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
