<?php

declare(strict_types=1);

namespace Domain\Projects\ValueObjects;

use Domain\Shared\Email;
use Seedwork\Domain\ValueObject;

final readonly class User extends ValueObject
{
    public function __construct(public Email $username)
    {
    }
}
