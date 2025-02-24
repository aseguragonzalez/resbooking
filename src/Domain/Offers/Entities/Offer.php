<?php

declare(strict_types=1);

namespace App\Domain\Offers\Entities;

use App\Domain\Offers\Exceptions\{
    InvalidDateRange,
    OfferAlreadyDisabled,
    OfferAlreadyEnabled
};
use App\Domain\Offers\Events\{
    OfferCreated,
    OfferUpdated,
    OfferDisabled,
    OfferEnabled,
    OpenCloseEventCreated,
    OpenCloseEventRemoved,
    TurnAssigned,
    TurnUnassigned
};
use App\Domain\Offers\ValueObjects\{Project, Settings};
use App\Domain\Shared\Exceptions\{
    OpenCloseEventAlreadyExist,
    OpenCloseEventDoesNotExist,
    OpenCloseEventOutOfRange,
    TurnAlreadyExist,
    TurnDoesNotExist,
};
use App\Domain\Shared\ValueObjects\{OpenCloseEvent, TurnAvailability};
use App\Seedwork\Domain\AggregateRoot;
use Tuupola\Ksuid;

final class Offer extends AggregateRoot
{
    /**
     * @param array<OpenCloseEvent> $openCloseEvents
     * @param array<TurnAvailability> $turns
     */
    private function __construct(
        string $id,
        public readonly Project $project,
        private Settings $settings,
        private bool $available = true,
        private array $openCloseEvents = [],
        private array $turns = [],
    ) {
        parent::__construct($id);
    }

    /**
     * @param array<TurnAvailability> $turns
     */
    public static function new(Project $project, Settings $settings, array $turns = []): self
    {
        $offerId = (string) new Ksuid();
        $offer = new self(
            id: $offerId,
            project: $project,
            settings: $settings,
            turns: $turns
        );
        $offer->addEvent(OfferCreated::new(offerId: $offerId, offer: $offer));
        return $offer;
    }

    /**
     * @param array<OpenCloseEvent> $openCloseEvents
     * @param array<TurnAvailability> $turns
     */
    public static function build(
        string $id,
        Project $project,
        Settings $settings,
        bool $available,
        array $openCloseEvents,
        array $turns
    ): self {
        return new self(
            id: $id,
            project: $project,
            settings: $settings,
            available: $available,
            openCloseEvents: $openCloseEvents,
            turns: $turns
        );
    }

    public function getSettings(): Settings
    {
        return $this->settings;
    }

    public function setSettings(Settings $settings): void
    {
        $this->settings = $settings;
        $this->addEvent(OfferUpdated::new(offerId: $this->getId(), offer: $this));
    }

    public function disable(): void
    {
        if (!$this->available) {
            throw new OfferAlreadyDisabled();
        }
        $this->available = false;
        $this->addEvent(OfferDisabled::new(offerId: $this->getId(), offer: $this));
    }

    public function enable(): void
    {
        if ($this->available) {
            throw new OfferAlreadyEnabled();
        }
        $this->available = true;
        $this->addEvent(OfferEnabled::new(offerId: $this->getId(), offer: $this));
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    /**
     * @return array<TurnAvailability>
     */
    public function getTurns(): array
    {
        return $this->turns;
    }

    public function addTurn(TurnAvailability $turn): void
    {
        $turns = array_filter($this->turns, fn (TurnAvailability $s) => $s->equals($turn));
        if (!empty($turns)) {
            throw new TurnAlreadyExist();
        }
        $this->turns[] = $turn;
        $this->addEvent(TurnAssigned::new(offerId: $this->getId(), turn: $turn));
    }

    public function removeTurn(TurnAvailability $turn): void
    {
        $turns = array_filter($this->turns, fn (TurnAvailability $s) => $s->equals($turn));
        if (empty($turns)) {
            throw new TurnDoesNotExist();
        }
        $this->turns = array_filter(
            $this->turns,
            fn (TurnAvailability $s) => !$s->equals($turn)
        );
        $this->addEvent(TurnUnassigned::new(offerId: $this->getId(), turn: $turn));
    }

    /**
     * @return array<OpenCloseEvent>
     */
    public function getOpenCloseEvents(): array
    {
        return $this->openCloseEvents;
    }

    public function addOpenCloseEvent(OpenCloseEvent $event): void
    {
        $events = array_filter($this->openCloseEvents, fn (OpenCloseEvent $s) => $s->equals($event));
        if (!empty($events)) {
            throw new OpenCloseEventAlreadyExist();
        }
        $yesterday = (new \DateTimeImmutable())->sub(new \DateInterval('P1D'));

        if ($event->date <= $yesterday) {
            throw new OpenCloseEventOutOfRange();
        }

        $this->openCloseEvents[] = $event;
        $this->addEvent(OpenCloseEventCreated::new(offerId: $this->getId(), openCloseEvent: $event));
    }

    public function removeOpenCloseEvent(OpenCloseEvent $event): void
    {
        $events = array_filter($this->openCloseEvents, fn (OpenCloseEvent $s) => $s->equals($event));
        if (empty($events)) {
            throw new OpenCloseEventDoesNotExist();
        }
        $this->openCloseEvents = array_filter(
            $this->openCloseEvents,
            fn (OpenCloseEvent $event) => !$event->equals($event)
        );
        $this->addEvent(OpenCloseEventRemoved::new(offerId: $this->getId(), openCloseEvent: $event));
    }
}
