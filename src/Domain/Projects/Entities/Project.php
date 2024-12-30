<?php

declare(strict_types=1);

namespace App\Domain\Projects\Entities;

use App\Domain\Projects\Entities\{Place, User};
use App\Domain\Projects\Exceptions\{
    PlaceAlreadyExist,
    PlaceDoesNotExist,
    UserAlreadyExist,
    UserDoesNotExist
};
use App\Domain\Shared\Exceptions\{
    OpenCloseEventAlreadyExist,
    OpenCloseEventDoesNotExist,
    OpenCloseEventOutOfRange,
    TurnAlreadyExist,
    TurnDoesNotExist,
};
use App\Domain\Projects\ValueObjects\Settings;
use App\Domain\Shared\ValueObjects\{OpenCloseEvent, TurnAvailability};
use App\Seedwork\Domain\AggregateRoot;

final class Project extends AggregateRoot
{
    /**
     * @param array<User> $users
     * @param array<Place> $places
     * @param array<TurnAvailability> $turns
     * @param array<OpenCloseEvent> $openCloseEvents
     */
    public function __construct(
        string $id,
        private Settings $settings,
        private array $users = [],
        private array $places = [],
        private array $turns = [],
        private array $openCloseEvents = [],
    ) {
        parent::__construct($id);
    }

    /**
     * @return array<User>
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    public function addUser(User $user): void
    {
        $users = array_filter($this->users, fn (User $s) => $s->equals($user));
        if (!empty($users)) {
            throw new UserAlreadyExist();
        }
        $this->users[] = $user;
    }

    public function removeUser(User $user): void
    {
        $users = array_filter($this->users, fn (User $s) => $s->equals($user));
        if (empty($users)) {
            throw new UserDoesNotExist();
        }

        $this->users = array_filter(
            $this->users,
            fn (User $s) => !$s->equals($user)
        );
    }

    /**
     * @return array<Place>
     */
    public function getPlaces(): array
    {
        return $this->places;
    }

    public function addPlace(Place $place): void
    {
        $places = array_filter($this->places, fn (Place $s) => $s->equals($place));
        if (!empty($places)) {
            throw new PlaceAlreadyExist();
        }
        $this->places[] = $place;
    }

    public function removePlace(Place $place): void
    {
        $places = array_filter($this->places, fn (Place $s) => $s->equals($place));
        if (empty($places)) {
            throw new PlaceDoesNotExist();
        }
        $this->places = array_filter(
            $this->places,
            fn (Place $s) => !$s->equals($place)
        );
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
    }

    public function getSettings(): Settings
    {
        return $this->settings;
    }
}
