<?php

declare(strict_types=1);

namespace Domain\Restaurants\Exceptions;

use SeedWork\Domain\Exceptions\DomainException;

final class TimeSlotAlreadyExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('Time slot already exists in restaurant');
    }
}
