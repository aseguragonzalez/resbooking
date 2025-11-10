<?php

declare(strict_types=1);

namespace Domain\Projects\ValueObjects;

use Domain\Shared\Email;
use Seedwork\Domain\ValueObject;

final class User extends ValueObject
{
    public function __construct(public readonly Email $username)
    {
    }
}
