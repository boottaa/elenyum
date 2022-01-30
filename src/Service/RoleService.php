<?php

namespace App\Service;

use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;

class RoleService extends BaseAbstractService
{
    public function __construct(
        RoleRepository $repository,
        private EntityManagerInterface $em
    ) {
        $this->repository = $repository;
    }
}