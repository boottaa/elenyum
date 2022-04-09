<?php

namespace App\Repository;

use App\Entity\NewClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NewClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewClient[]    findAll()
 * @method NewClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewClient::class);
    }

    // /**
    //  * @return NewClient[] Returns an array of NewClient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NewClient
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
