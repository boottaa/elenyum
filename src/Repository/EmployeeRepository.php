<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Employee;
use App\Exception\ArrayException;
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
     */
    public function list(?array $params): array
    {
        $company = $params['company'];
        if ($company instanceof Company) {
            return $this->createQueryBuilder("e")
                ->select('PARTIAL e.{id, name}')
                ->orderBy('e.id', 'ASC')
                ->where('e.company=:company')
                ->setParameter('company', $company)
                ->getQuery()
                ->getArrayResult();
        }

        throw new ArrayException('Company undefined', '422');
    }
}
