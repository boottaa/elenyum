<?php

declare(strict_types=1);

namespace App\Service;

interface BaseInterface
{
    /**
     * @return array
     */
    public function list(): array;

    /**
     * @return void
     */
    public function hydrate(): void;

    /**
     * @return bool
     */
    public function edit(): bool;

    /**
     * @return bool
     */
    public function add(): bool;

    /**
     * @return bool
     */
    public function del(): bool;
}