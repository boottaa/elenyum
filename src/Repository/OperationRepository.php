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
        if (!$company instanceof Company) {
            throw new ArrayException('Company undefined', '422');
        }
        $qb = $this->createQueryBuilder("o")
            ->select('o')
            ->orderBy('o.id', 'ASC')
            ->where('o.company=:company')
            ->setParameter('company', $company);

        return (new Paginator($qb))->paginate($page);
    }
}
