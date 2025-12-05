<?php

declare(strict_types=1);

namespace Domain\Projects\Entities;

use Domain\Shared\Capacity;
use Seedwork\Domain\Entity;
use Seedwork\Domain\Exceptions\ValueException;

final class Place extends Entity
{
    private function __construct(
        string $id,
        public readonly Capacity $capacity,
        public readonly string $name,
    ) {
        parent::__construct(id: $id);

        if (empty($name)) {
            throw new ValueException('Name is required');
        }
    }

    public static function new(Capacity $capacity, string $name, ?string $id = null): self
    {
        return new self(
            id: $id ?? uniqid(),
            capacity: $capacity,
            name: $name
        );
    }

    public static function build(string $id, Capacity $capacity, string $name): self
    {
        return new self($id, $capacity, $name);
    }
}
