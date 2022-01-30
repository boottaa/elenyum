<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Employee;
use App\Exception\ArrayException;
use App\Utils\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends ServiceEntityRepository implements ListRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    /**
     * @throws ArrayException
     * @throws \Exception
     */
    public function list(?array $params, int $page): Paginator
    {
        $company = $params['company'];
        if (!$company instanceof Company) {
            throw new ArrayException('Company undefined', '422');
        }
        $qb = $this->createQueryBuilder("e")
            ->select('e')
            ->orderBy('e.id', 'ASC')
            ->where('e.company=:company')
            ->setParameter('company', $company);

        return (new Paginator($qb))->paginate($page);
    }


    /**
     * @throws ArrayException
     * @throws \Exception
     */
    public function listForCalendar(?array $params): Paginator
    {
        $company = $params['company'];
        if (!$company instanceof Company) {
            throw new ArrayException('Company undefined', '422');
        }
        $qb = $this->createQueryBuilder("e")
            ->select('e', 'p')
            ->orderBy('e.id', 'ASC')
            ->leftJoin('e.position', 'p')
            ->where('e.company=:company')
            ->andWhere('p.inCalendar=:inCalendar')
            ->setParameter('company', $company)
            ->setParameter('inCalendar', true);

        return (new Paginator($qb, 0))->paginate();
    }
}
