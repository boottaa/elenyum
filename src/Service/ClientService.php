<?php

namespace App\Service;

use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;

class ClientService extends BaseAbstractService
{
    public function __construct(
        ClientRepository $repository,
        private EntityManagerInterface $em
    ) {
        $this->repository = $repository;
    }
}