<?php

declare(strict_types=1);

namespace Application\Reservations\CreateReservation;

interface CreateReservation
{
    public function execute(CreateReservationCommand $command): void;
}
