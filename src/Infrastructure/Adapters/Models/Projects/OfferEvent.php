<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class OfferEvent
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $turnId,
        public int $offerId,
        public string $configurationId,
        public int $year,
        public int $week,
        public int $dayOfWeek,
        public string $date,
        public bool $state,
    ) {
    }
}
