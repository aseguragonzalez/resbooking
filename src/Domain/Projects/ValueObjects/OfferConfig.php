<?php

declare(strict_types=1);

namespace App\Domain\Projects;

use App\Domain\Shared\{DayOfWeek, Turn};
use App\Seedwork\Domain\ValueObject;


final class OfferConfig extends ValueObject
{
    public function __construct(
        public readonly DayOfWeek $dayOfWeek,
        public readonly Turn $turn,
        public readonly int $capacity,
    ) { }

    public function equals(OfferConfig $offerConfig): bool
    {
        return $this->dayOfWeek->equals($offerConfig->dayOfWeek)
            && $this->turn->equals($offerConfig->turn);
    }
}
