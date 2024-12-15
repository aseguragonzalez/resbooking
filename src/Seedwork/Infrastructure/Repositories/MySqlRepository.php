<?php

declare(strict_types=1);

namespace App\Seedwork\Infrastructure\Repositories;

use App\Seedwork\Domain\{Repository, AggregateRoot};
use App\Seedwork\Exceptions\NotImplementedException;

abstract class MySqlRepository implements Repository
{
    public function __construct()
    {
    }

    public function save(AggregateRoot $aggregateRoot = null): void
    {
        throw new NotImplementedException();
    }

    public function getById(int $id): AggregateRoot
    {
        throw new NotImplementedException();
    }

    public function findById(int $id): ?AggregateRoot
    {
        throw new NotImplementedException();
    }
}
