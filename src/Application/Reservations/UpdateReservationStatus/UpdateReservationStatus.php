<?php

declare(strict_types=1);

namespace Application\Reservations\UpdateReservationStatus;

interface UpdateReservationStatus
{
    public function execute(UpdateReservationStatusCommand $command): void;
}
