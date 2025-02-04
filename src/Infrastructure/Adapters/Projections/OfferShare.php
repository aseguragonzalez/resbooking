<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Projections;

readonly class OfferShare
{
    public function __construct(
        public int $projectId,
        public int $offerId,
        public ?int $turnId,
        public int $slotId,
        public string $date,
        public int $share,
        public int $bookingsTotal,
        public int $bookingsFree,
        public int $dinersTotal,
        public int $dinersFree,
    ) {
    }
}
