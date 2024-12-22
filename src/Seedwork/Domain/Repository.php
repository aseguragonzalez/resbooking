<?php

declare(strict_types=1);

namespace App\Seedwork\Domain;

interface Repository
{
    public function save(AggregateRoot $aggregateRoot): void;

    public function getById(string $id): AggregateRoot;

    public function findById(string $id): ?AggregateRoot;
}
