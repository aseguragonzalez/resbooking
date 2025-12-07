<?php

declare(strict_types=1);

namespace Application\Reservations\ListReservations;

final readonly class ListReservationsCommand
{
    public function __construct(
        public string $projectId,
        public string $from,
        public int $offset = 0
    ) {
    }
}
