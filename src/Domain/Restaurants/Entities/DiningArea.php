<?php

declare(strict_types=1);

namespace Domain\Restaurants\Entities;

use Domain\Restaurants\ValueObjects\DiningAreaId;
use Domain\Shared\Capacity;
use SeedWork\Domain\Entity;
use SeedWork\Domain\Exceptions\ValueException;

/**
 * @extends Entity<DiningAreaId>
 */
final readonly class DiningArea extends Entity
{
    private function __construct(
        DiningAreaId $id,
        public Capacity $capacity,
        public string $name,
    ) {
        parent::__construct(id: $id);
    }

    protected function validate(): void
    {
        if (empty($this->name)) {
            throw new ValueException('Name is required');
        }
    }

    public static function new(Capacity $capacity, string $name, ?string $id = null): self
    {
        return new self(
            id: $id !== null ? DiningAreaId::fromString($id) : DiningAreaId::create(),
            capacity: $capacity,
            name: $name
        );
    }

    public static function build(string $id, Capacity $capacity, string $name): self
    {
        return new self(DiningAreaId::fromString($id), $capacity, $name);
    }
}
