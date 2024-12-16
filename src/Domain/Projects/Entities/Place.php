<?php

declare(strict_types=1);

namespace App\Domain\Projects;

use App\Domain\Projects\ValueObjects\Capacity;
use App\Seedwork\Domain\Entity;

final class Place extends Entity
{
    public function __construct(
        public ?int $id,
        public Capacity $capacity,
        public string $description,
        public string $name,
    ) { }

    public function equals(Place $place): bool
    {
        return $this->id === $place->id;
    }
}
