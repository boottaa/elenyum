<?php

namespace App\Repository;

use App\Utils\Paginator;

interface ListRepositoryInterface
{
    public function list(?array $params, int $page): Paginator;
}