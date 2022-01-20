<?php

namespace App\Service;

use App\Repository\EmployeeRepository;

class EmployeeService extends BaseAbstractService
{
    public function __construct(EmployeeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function hydrate(): void
    {
        // TODO: Implement hydrate() method.
    }

    public function edit(): bool
    {
        // TODO: Implement edit() method.
    }

    public function add(): bool
    {
        // TODO: Implement add() method.
    }

    public function del(): bool
    {
        // TODO: Implement del() method.
    }
}