<?php

declare(strict_types=1);

namespace Domain\Restaurants\ValueObjects;

use Domain\Shared\Email;
use SeedWork\Domain\ValueObject;

final readonly class User extends ValueObject
{
    public function __construct(public Email $username)
    {
        parent::__construct();
    }

    protected function validate(): void
    {
    }

    public function equals(ValueObject $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }
        /** @var self $other */
        return $this->username->equals($other->username);
    }
}
