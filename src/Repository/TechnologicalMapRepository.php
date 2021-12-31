<?php

namespace App\Repository;

use App\Entity\TechnologicalMap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TechnologicalMap|null find($id, $lockMode = null, $lockVersion = null)
 * @method TechnologicalMap|null findOneBy(array $criteria, array $orderBy = null)
 * @method TechnologicalMap[]    findAll()
 * @method TechnologicalMap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TechnologicalMapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TechnologicalMap::class);
    }

    // /**
    //  * @return TechnologicalMaps[] Returns an array of TechnologicalMaps objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TechnologicalMaps
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
