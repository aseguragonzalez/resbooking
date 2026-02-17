<?php

declare(strict_types=1);

namespace Seedwork\Domain;

/**
 * Value type representing the unique identity of an aggregate or entity.
 *
 * Immutable and comparable. Use new() when creating a new aggregate or entity;
 * use fromString() when loading from persistence or when the id comes from a
 * command/query (primitives-only boundary). Do not use domain types in
 * command/query DTOs—pass the id as string and build EntityId in the handler.
 */
final readonly class EntityId
{
    private function __construct(public string $value)
    {
    }

    /**
     * Creates a new identity with a unique value (via uniqid()).
     */
    public static function new(): self
    {
        return new self(uniqid());
    }

    /**
     * Creates an identity from an existing string (e.g. from database or command).
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Returns whether this identity has the same value as the other.
     */
    public function equals(EntityId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
