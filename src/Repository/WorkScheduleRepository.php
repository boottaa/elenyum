<?php

namespace App\Repository;

use App\Entity\WorkSchedule;
use App\Utils\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method WorkSchedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkSchedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkSchedule[]    findAll()
 * @method WorkSchedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkSchedule::class);
    }

    /**
     * @param array|null $params
     * @param int $page
     * @return Paginator
     * @throws Exception
     */
    public function listByUser(?array $params, int $page): Paginator
    {
        $userId = $params['userId'];
        $qb = $this->createQueryBuilder('ws')
            ->orderBy('ws.id', 'DESC');

        $qb->where('ws.employee=:userId');

        return (new Paginator($qb))->paginate($page);
    }
}
