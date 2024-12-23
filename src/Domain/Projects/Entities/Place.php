<?php

declare(strict_types=1);

namespace App\Domain\Projects\Entities;

use App\Domain\Projects\ValueObjects\Capacity;
use App\Seedwork\Domain\Entity;

final class Place extends Entity
{
    public function __construct(
        private readonly string $id,
        public Capacity $capacity,
        public string $description,
        public string $name,
    ) {
        parent::__construct($id);
    }

    public function equals(Place $place): bool
    {
        return $this->id === $place->getId();
    }
}
