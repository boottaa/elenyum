<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\Operation;
use App\Exception\ArrayException;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;

class OperationService extends BaseAbstractService
{
    public function __construct(
        OperationRepository $repository,
        private EntityManagerInterface $em
    ) {
        $this->repository = $repository;
    }

    /**
     * @param Operation $operation
     * @param array $data
     * @return void
     */
    private function hydrate(Operation $operation, array $data): void
    {
        $operation->setPrice($data['price']);
        $operation->setTitle($data['title']);
        $operation->setDuration($data['duration']);
        $this->em->persist($operation);
    }

    /**
     * @param array $data
     * @return Operation
     * @throws ArrayException
     */
    public function put(array $data): Operation
    {
        $operationData = $data['operation'];
        $operation = $this->repository->find($operationData['id']);
        if (!$operation instanceof Operation) {
            throw new ArrayException('Not defined '.Operation::class, '422');
        }
        $this->hydrate($operation, $operationData);
        $this->em->flush();

        return $operation;
    }

    /**
     * @param array $data
     * @return Operation
     * @throws ArrayException
     */
    public function post(array $data): Operation
    {
        $user = $data['user'];
        $operationData = $data['operation'];

        if (!$user instanceof Employee) {
            throw new ArrayException('Not defined '.Employee::class, '422');
        }

        $operation = new Operation();
        $operation->setCompany($user->getCompany());
        $this->hydrate($operation, $operationData);
        $this->em->flush();

        return $operation;
    }

    /**
     * @param int $id
     * @return bool
     * @throws ArrayException
     */
    public function del(int $id): bool
    {
        $operation = $this->repository->find($id);
        if (!$operation instanceof Operation) {
            throw new ArrayException('Not defined '.Operation::class, '422');
        }

        $this->em->remove($operation);
        $this->em->flush();

        return true;
    }
}