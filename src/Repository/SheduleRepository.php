<?php

namespace App\Repository;

use App\Entity\Shedule;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shedule[]    findAll()
 * @method Shedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shedule::class);
    }

    public function findByRange(DateTimeInterface $start, DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('s')
            ->addSelect('sheduleOperations', 'operation', 'PARTIAL client.{id,name,phone,status}, PARTIAL employee.{id}')
            ->leftJoin('s.sheduleOperations', 'sheduleOperations')
            ->leftJoin('sheduleOperations.operation', 'operation')
            ->leftJoin('s.client', 'client')
            ->leftJoin('s.employee', 'employee')
            ->andWhere('s.start >= :start')
            ->andWhere('s.end <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('s.end', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById(int $id): ?array
    {
        $result = $this->createQueryBuilder('s')
            ->addSelect('sheduleOperations', 'operation', 'PARTIAL client.{id,name,phone,status}, PARTIAL employee.{id}')
            ->leftJoin('s.sheduleOperations', 'sheduleOperations')
            ->leftJoin('sheduleOperations.operation', 'operation')
            ->leftJoin('s.client', 'client')
            ->leftJoin('s.employee', 'employee')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->orderBy('s.end', 'ASC')
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

        if (is_array($result)) {
            return $result;
        }

        return null;
    }
}
