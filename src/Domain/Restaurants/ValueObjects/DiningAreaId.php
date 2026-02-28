<?php

declare(strict_types=1);

namespace Domain\Restaurants\ValueObjects;

use SeedWork\Domain\EntityId;
use SeedWork\Domain\Exceptions\ValueException;

final readonly class DiningAreaId extends EntityId
{
    public static function create(): self
    {
        return new self('id-' . uniqid('', true));
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    private function __construct(string $value)
    {
        parent::__construct($value);
    }

    protected function validate(): void
    {
        if (empty($this->value)) {
            throw new ValueException('Dining area id cannot be empty');
        }
    }
}
