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

    /**
     * @param array $data
     * @return object
     * @throws ArrayException
     */
    public function put(array $data): object
    {
        throw new ArrayException('Method: "' . __METHOD__ . '" is empty', '500');
    }

    /**
     * @param array $data
     * @return bool
     * @throws ArrayException
     */
    public function post(array $data): bool
    {
        throw new ArrayException('Method: "' . __METHOD__ . '" is empty', '500');
    }

    /**
     * @param int $id
     * @return bool
     * @throws ArrayException
     */
    public function del(int $id): bool
    {
        throw new ArrayException('Method: "' . __METHOD__ . '" is empty', '500');
    }
}