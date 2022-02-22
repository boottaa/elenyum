<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Position;
use App\Exception\ArrayException;
use App\Utils\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Position|null find($id, $lockMode = null, $lockVersion = null)
 * @method Position|null findOneBy(array $criteria, array $orderBy = null)
 * @method Position[]    findAll()
 * @method Position[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PositionRepository extends ServiceEntityRepository implements ListRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Position::class);
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
        $qb = $this->createQueryBuilder("p")
            ->select('p')
            ->orderBy('p.id', 'ASC')
            ->where('p.company=:company')
            ->setParameter('company', $company);

        return (new Paginator($qb))->paginate($page);
    }

    /**
     * @param int $id
     * @return array
     */
    public function get(int $id): array
    {
        $qb = $this->createQueryBuilder("p")
            ->select('p', 'po', 'r', 'o')
            ->orderBy('p.id', 'ASC')
            ->where('p.id=:id')
            ->leftJoin('p.positionRole', 'r')
            ->leftJoin('p.positionOperation', 'po')
            ->leftJoin('po.operation', 'o')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
    }
}
