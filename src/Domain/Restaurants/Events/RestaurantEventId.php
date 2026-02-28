<?php

declare(strict_types=1);

namespace Domain\Restaurants\Events;

use SeedWork\Domain\EventId;
use SeedWork\Domain\Exceptions\ValueException;

final readonly class RestaurantEventId extends EventId
{
    public static function create(): self
    {
        return new self('evt-' . uniqid('', true));
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
            throw new ValueException('Event id cannot be empty');
        }

        if (!preg_match('/^evt-[a-z0-9.-]+$/', $this->value)) {
            throw new ValueException('Event id must start with "evt-"');
        }
    }
}
