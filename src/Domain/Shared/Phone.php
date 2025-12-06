<?php

declare(strict_types=1);

namespace Domain\Shared;

final class Phone
{
    public function __construct(public readonly string $value)
    {
        // TODO: Implement phone number validation
        // if (!preg_match('/^\(\d{2}\) \d{4,5}-\d{4}$/', $value)) {
        //      throw new \InvalidArgumentException('Invalid phone number');
        // }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Phone $phone): bool
    {
        return $this->value === $phone->value;
    }
}
