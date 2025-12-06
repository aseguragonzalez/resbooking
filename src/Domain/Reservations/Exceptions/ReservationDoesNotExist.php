<?php

declare(strict_types=1);

namespace Domain\Reservations\Exceptions;

use Seedwork\Domain\Exceptions\DomainException;

final class ReservationDoesNotExist extends DomainException
{
    public function __construct()
    {
        parent::__construct('Reservation does not exist');
    }
}
