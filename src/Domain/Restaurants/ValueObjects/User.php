<?php

declare(strict_types=1);

namespace Domain\Restaurants\ValueObjects;

use Domain\Shared\Email;
use Seedwork\Domain\ValueObject;

final readonly class User extends ValueObject
{
    public function __construct(public Email $username)
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
