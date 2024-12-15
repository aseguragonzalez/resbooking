<?php

declare(strict_types=1);

namespace App\Domain\Projects;

use DateTimeImmutable;
use App\Domain\Shared\Turn;
use App\Seedwork\Domain\ValueObject;

final class OfferEvent extends ValueObject
{
    public function __construct(
        public readonly DateTimeImmutable $date,
        public readonly Turn $turn,
        public readonly bool $available,
    ) { }

    public function equals(OfferEvent $offerEvent): bool
    {
        return $this->turn->equals($offerEvent->turn)
            && $this->date->format('Y-m-d') === $offerEvent->date->format('Y-m-d');
    }
}
