<?php

declare(strict_types=1);

namespace Domain\Shared\Exceptions;

use SeedWork\Domain\Exceptions\DomainException;

final class InvalidTimeSlot extends DomainException
{
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
