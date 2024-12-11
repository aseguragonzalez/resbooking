<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Projections;

readonly class TurnShare
{
    public function __construct(
        public int $projectId,
        public ?int $turnId,
        public string $date,
        public int $share,
        public int $bookingsTotal,
        public int $bookingsFree,
        public int $dinersTotal,
        public int $dinersFree,
    ) { }
}
