<?php

declare(strict_types=1);

namespace Domain\Restaurants\Exceptions;

use SeedWork\Domain\Exceptions\DomainException;

final class TimeSlotDoesNotExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('Time slot does not exists in restaurant');
    }
}
