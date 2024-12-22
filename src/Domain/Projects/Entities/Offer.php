<?php

declare(strict_types=1);

namespace App\Domain\Projects;

use DateTimeInmutable;
use App\Domain\Shared\{OpenCloseEvent, TurnAvailability};
use App\Seedwork\Domain\AggregateRoot;

final class Offer extends AggregateRoot
{
    public function __construct(
        private readonly string $id,
        public string $description,
        public string $title,
        public string $termsAndConditions,
        public DateTimeInmutable $startDate,
        public DateTimeInmutable $endDate,
        public array $openCloseEvents = [],
        public array $turns = [],
    ) {
        parent::__construct($id);
    }

    public function addTurn(TurnAvailability $turn): void
    {
        $this->turns[] = $turn;
    }

    public function removeTurn(TurnAvailability $turn): void
    {
        $this->turns = array_filter(
            $this->turns, fn (TurnAvailability $s) => $s->equals($turn)
        );
    }

    public function addOpenCloseEvent(OpenCloseEvent $event): void
    {
        $this->openCloseEvents[] = $event;
    }

    public function removeOpenCloseEvent(OpenCloseEvent $event): void
    {
        $this->openCloseEvents = array_filter(
            $this->openCloseEvents, fn (OpenCloseEvent $event) => $event->equals($event)
        );
    }
}
