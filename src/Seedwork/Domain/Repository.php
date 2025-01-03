<?php

declare(strict_types=1);

namespace App\Seedwork\Domain;

use App\Seedwork\Domain\AggregateRoot;

/**
 * @template T of AggregateRoot
 */
interface Repository
{
    /**
     * @param T $aggregateRoot
     * @return void
     */
    public function save(T $aggregateRoot): void;

    public function getById(string $id): T;

    public function findById(string $id): ?T;
}
