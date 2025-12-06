<?php

declare(strict_types=1);

namespace Application\Reservations\ListReservations;

final class ListReservationsCommand
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $from,
        public readonly int $offset = 0
    ) {
    }
}
