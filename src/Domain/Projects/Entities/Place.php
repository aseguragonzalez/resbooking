<?php

declare(strict_types=1);

namespace App\Domain\Projects\Entities;

use App\Domain\Shared\Capacity;
use App\Seedwork\Domain\Entity;
use App\Seedwork\Domain\Exceptions\ValueException;
use Tuupola\Ksuid;

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
            id: $id ?? (string) new Ksuid(),
            capacity: $capacity,
            name: $name
        );
    }

    public static function build(string $id, Capacity $capacity, string $name): self
    {
        return new self($id, $capacity, $name);
    }
}
