<?php

declare(strict_types=1);

namespace App\Service;

use App\Utils\Paginator;

interface BaseInterface
{
    /**
     * @param array|null $params
     * @param int $page
     * @return Paginator
     */
    public function list(?array $params, int $page): Paginator;

    /**
     * @param array $data
     * @return object
     */
    public function put(array $data): object;

    /**
     * Add data
     *
     * @param array $data
     * @return bool
     */
    public function post(array $data): bool;

    /**
     * @param int $id
     * @return bool
     */
    public function del(int $id): bool;
}