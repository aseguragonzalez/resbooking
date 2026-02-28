<?php

declare(strict_types=1);

namespace Domain\Shared;

use SeedWork\Domain\Exceptions\ValueException;
use SeedWork\Domain\ValueObject;

final readonly class Email extends ValueObject
{
    public function __construct(public string $value)
    {
        parent::__construct();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(ValueObject $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }
        return $this->value === $other->value;
    }

    protected function validate(): void
    {
        if (trim($this->value) === '') {
            throw new ValueException('Email address is required');
        }

        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            throw new ValueException('Invalid email address');
        }
    }
}
