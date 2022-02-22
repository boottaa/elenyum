<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Operation;
use App\Exception\ArrayException;
use App\Utils\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Operation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Operation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Operation[]    findAll()
 * @method Operation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationRepository extends ServiceEntityRepository implements ListRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operation::class);
    }

    /**
     * @param array|null $params
     * @param int $page
     * @return Paginator
     * @throws \Exception
     */
    public function list(?array $params, int $page): Paginator
    {
        $company = $params['company'];
        $employee = $params['employee'];
        if (!$company instanceof Company) {
            throw new ArrayException('Company undefined', '422');
        }
        $qb = $this->createQueryBuilder("o")
            ->select('o', 'p', 'po', 'e')
            ->orderBy('o.id', 'ASC')
            ->leftJoin('o.positionOperation', 'po')
            ->leftJoin('po.position', 'p')
            ->leftJoin('p.employee', 'e')
            ->where('o.company=:company')
            ->setParameter('company', $company);

        if ($employee !== null) {
            $qb->andWhere('e.id=:employee')
                ->setParameter('employee', $employee);
        }

        return (new Paginator($qb))->paginate($page);
    }
}
