<?php

declare(strict_types=1);

namespace App\Domain\Projects\Entities;

use App\Domain\Projects\Entities\{Place, User};
use App\Domain\Projects\ValueObjects\Settings;
use App\Domain\Shared\ValueObjects\{OpenCloseEvent, TurnAvailability};
use App\Seedwork\Domain\AggregateRoot;

final class Project extends AggregateRoot
{
    public function __construct(
        private readonly string $id,
        public readonly Settings $settings,
        public readonly array $users = [],
        public readonly array $places = [],
        public readonly array $turns = [],
        public readonly array $openCloseEvents = [],
    ) {
        parent::__construct($id);
    }

    public function addUser(User $user): void
    {
        $this->users[] = $user;
    }

    public function removeUser(User $user): void
    {
        $this->users = array_filter(
            $this->users,
            fn (User $s) => $s->equals($user)
        );
    }

    public function addPlace(Place $place): void
    {
        $this->places[] = $place;
    }

    public function removePlace(Place $place): void
    {
        $this->places = array_filter(
            $this->places,
            fn (Place $s) => $s->equals($place)
        );
    }

    public function addTurn(TurnAvailability $turn): void
    {
        $this->turns[] = $turn;
    }

    public function removeTurn(TurnAvailability $turn): void
    {
        $this->turns = array_filter(
            $this->turns,
            fn (TurnAvailability $s) => $s->equals($turn)
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
