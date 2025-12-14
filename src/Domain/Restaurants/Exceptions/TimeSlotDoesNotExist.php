<?php

declare(strict_types=1);

namespace Domain\Restaurants\Exceptions;

use Seedwork\Domain\Exceptions\DomainException;

final class TimeSlotDoesNotExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('Time slot does not exists in restaurant');
    }
}
