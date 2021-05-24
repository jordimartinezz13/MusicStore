<?php

namespace App\Repository;

use App\Entity\Disco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Disco|null find($id, $lockMode = null, $lockVersion = null)
 * @method Disco|null findOneBy(array $criteria, array $orderBy = null)
 * @method Disco[]    findAll()
 * @method Disco[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Disco::class);
    }

    // /**
    //  * @return Disco[] Returns an array of Disco objects
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
    public function findOneBySomeField($value): ?Disco
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
