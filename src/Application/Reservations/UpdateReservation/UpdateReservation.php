<?php

declare(strict_types=1);

namespace Application\Reservations\UpdateReservation;

interface UpdateReservation
{
    public function execute(UpdateReservationCommand $command): void;
}
