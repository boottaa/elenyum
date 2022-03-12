<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use App\Utils\Paginator;
use Exception;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository implements ListRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @param array|null $params
     * @param int $page
     * @return Paginator
     * @throws Exception
     */
    public function list(?array $params, int $page): Paginator
    {
        $query = $params['query'] ?? null;
        $company = $params['company'];

        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->where('c.company=:company')
            ->setParameter('company', $company);

        if (!empty($query)) {
            $qb->andWhere('c.phone LIKE :query');
            $qb->setParameter('query', "%{$query}%");
        }

        return (new Paginator($qb))->paginate($page);
    }

    /**
     * @param int $id
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function get(int $id): array
    {
        $qb = $this->createQueryBuilder("c")
            ->select('c')
            ->orderBy('c.id', 'ASC')
            ->where('c.id=:id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
    }
}
