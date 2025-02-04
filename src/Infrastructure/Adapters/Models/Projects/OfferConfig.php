<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class OfferConfig
{
    public function __construct(
        public int $id,
        public int $offerId,
        public int $dayId,
        public int $slotId,
        public int $turnId,
    ) {
    }
}
