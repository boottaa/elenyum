<?php

namespace App\Repository;

use App\Entity\WorkSchedule;
use App\Utils\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Doctrine\ORM\Query\Expr;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method WorkSchedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkSchedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkSchedule[]    findAll()
 * @method WorkSchedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkScheduleRepository extends ServiceEntityRepository implements ListRepositoryInterface
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
    public function list(?array $params, int $page): Paginator
    {
        $userId = $params['userId'];
        $start = $params['start'];
        $end = $params['end'];

        $qb = $this->createQueryBuilder('ws')
            ->addSelect(['PARTIAL e.{id}', 'PARTIAL s.{id, start, end}']) //,, MIN(s.start) as startShedule, MAX(s.end) as endShedule
            ->leftJoin('ws.employee', 'e');

        $qb->leftJoin('e.schedules', 's', Expr\Join::WITH,
            $qb->expr()->andX(
                $qb->expr()->eq('s.employee', 'e.id'),
                $qb->expr()->gte('s.start', 'ws.start'),
                $qb->expr()->lte('s.end', 'ws.end'),
            )
        );

        $qb->where('ws.employee=:userId')
            ->andWhere('ws.start >= :start')
            ->andWhere('ws.end <= :end')
            ->setParameter('userId', $userId)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('s.id', 'DESC')
            ->orderBy('ws.id', 'DESC');

        return (new Paginator($qb))->paginate($page);
    }
}
