<?php

namespace App\Repository;

use App\Entity\PositionRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PositionRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method PositionRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method PositionRole[]    findAll()
 * @method PositionRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PositionRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PositionRole::class);
    }

    // /**
    //  * @return EmployeeRole[] Returns an array of EmployeeRole objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EmployeeRole
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
