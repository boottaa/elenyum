<?php

namespace App\Repository;

use App\Entity\Role;
use App\Utils\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends ServiceEntityRepository implements ListRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * @param array|null $params
     * @param int $page
     * @return Paginator
     * @throws \Exception
     */
    public function list(?array $params, int $page): Paginator
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.id', 'DESC');

        return (new Paginator($qb))->paginate($page);
    }
}
