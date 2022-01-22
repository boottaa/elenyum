<?php

namespace App\Service;

use App\Exception\ArrayException;
use App\Repository\ListRepositoryInterface;
use App\Utils\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

abstract class BaseAbstractService implements BaseInterface
{
    protected ServiceEntityRepositoryInterface $repository;

    /**
     * @throws ArrayException
     */
    public function list(?array $params, int $page): Paginator
    {
        if ($this->repository instanceof ListRepositoryInterface) {
            return $this->repository->list($params, $page);
        }

        throw new ArrayException('Not Implemented interface ' . ListRepositoryInterface::class, '501');
    }
}