<?php

declare(strict_types=1);

namespace Application\Reservations\ListReservations;

use Seedwork\Application\Command;

final class ListReservationsCommand extends Command
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $from,
        public readonly int $offset = 0
    ) {
    }
}
