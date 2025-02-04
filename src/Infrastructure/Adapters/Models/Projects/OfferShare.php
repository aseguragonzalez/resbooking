<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class OfferShare
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $offerId,
        public int $dayOfWeek,
        public int $slotId,
        public ?int $turnId,
        public int $share,
    ) {
    }
}
