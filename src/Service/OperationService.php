<?php

namespace App\Service;

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
}