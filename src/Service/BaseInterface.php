<?php

declare(strict_types=1);

namespace App\Service;

interface BaseInterface
{
    /**
     * @param array|null $params
     * @return array
     */
    public function list(?array $params): array;

    /**
     * @param array $data
     * @return bool
     */
    public function edit(array $data): bool;

    /**
     * @param array $data
     * @return bool
     */
    public function add(array $data): bool;

    /**
     * @param int $id
     * @return bool
     */
    public function del(int $id): bool;
}