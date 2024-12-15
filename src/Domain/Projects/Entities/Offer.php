<?php

declare(strict_types=1);

namespace App\Domain\Projects;

use DateTimeInmutable;
use App\Seedwork\Domain\Entity;

final class Offer extends Entity
{
    public function __construct(
        public int $id,
        public string $description,
        public string $title,
        public string $termsAndConditions,
        public DateTimeInmutable $startDate,
        public DateTimeInmutable $endDate,
        public array $offerConfigs = [],
        public array $offerEvents = [],
    ) { }

    public function addConfig(OfferConfig $offerConfig): void
    {
        $this->offerConfigs[] = $offerConfig;
    }

    public function removeConfig(OfferConfig $offerConfig): void
    {
        $this->offerConfigs = array_filter(
            $this->offerConfigs,
            fn (OfferConfig $s) => $s->equals($offerConfig)
        );
    }

    public function addEvent(OfferEvent $offerEvent): void
    {
        $this->offerEvents[] = $offerEvent;
    }

    public function removeEvent(OfferEvent $offerEvent): void
    {
        $this->offerEvents = array_filter(
            $this->offerEvents,
            fn (OfferEvent $s) => $s->equals($offerEvent)
        );
    }
}
