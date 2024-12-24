<?php

declare(strict_types=1);

namespace App\Domain\Projects\Entities;

use App\Domain\Shared\Capacity;
use App\Seedwork\Domain\Entity;
use App\Seedwork\Domain\Exceptions\ValueException;

final class Place extends Entity
{
    public function __construct(
        private readonly string $id,
        public readonly Capacity $capacity,
        public readonly string $name,
    ) {
        parent::__construct(id: $id);

        if (empty($name)) {
            throw new ValueException('Name is required');
        }
    }
}
