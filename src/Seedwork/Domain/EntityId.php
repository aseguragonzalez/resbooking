<?php

declare(strict_types=1);

namespace Seedwork\Domain;

final readonly class EntityId
{
    private function __construct(public string $value)
    {
    }

    public static function new(): self
    {
        return new self(uniqid());
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function equals(EntityId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
