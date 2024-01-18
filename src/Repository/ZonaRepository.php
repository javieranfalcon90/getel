<?php

namespace App\Repository;

use App\Entity\Zona;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Zona|null find($id, $lockMode = null, $lockVersion = null)
 * @method Zona|null findOneBy(array $criteria, array $orderBy = null)
 * @method Zona[]    findAll()
 * @method Zona[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZonaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zona::class);
    }

    // /**
    //  * @return Zona[] Returns an array of Zona objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('z')
            ->andWhere('z.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('z.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Zona
    {
        return $this->createQueryBuilder('z')
            ->andWhere('z.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
