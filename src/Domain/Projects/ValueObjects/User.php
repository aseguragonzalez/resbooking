<?php

declare(strict_types=1);

namespace App\Domain\Projects\ValueObjects;

use App\Domain\Shared\Email;
use App\Seedwork\Domain\ValueObject;

final class User extends ValueObject
{
    public function __construct(public readonly Email $username)
    {
    }
}
