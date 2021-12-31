<?php

namespace App\Repository;

use App\Entity\ClientCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClientCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientCompany[]    findAll()
 * @method ClientCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientCompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientCompany::class);
    }

    // /**
    //  * @return ClientCompany[] Returns an array of ClientCompany objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ClientCompany
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
