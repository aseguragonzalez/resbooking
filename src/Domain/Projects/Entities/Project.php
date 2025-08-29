<?php

declare(strict_types=1);

namespace App\Domain\Projects\Entities;

use App\Domain\Projects\Entities\Place;
use App\Domain\Projects\Events\{
    OpenCloseEventCreated,
    OpenCloseEventRemoved,
    PlaceCreated,
    PlaceRemoved,
    ProjectCreated,
    ProjectModified,
    TurnAssigned,
    TurnUnassigned,
    UserCreated,
    UserRemoved,
};
use App\Domain\Projects\Exceptions\{
    PlaceAlreadyExist,
    PlaceDoesNotExist,
    UserAlreadyExist,
    UserDoesNotExist,
};
use App\Domain\Projects\ValueObjects\{Settings, User};
use App\Domain\Shared\Exceptions\{
    OpenCloseEventAlreadyExist,
    OpenCloseEventDoesNotExist,
    OpenCloseEventOutOfRange,
    TurnAlreadyExist,
    TurnDoesNotExist,
};
use App\Domain\Shared\ValueObjects\{OpenCloseEvent, TurnAvailability};
use Seedwork\Domain\AggregateRoot;
use Tuupola\Ksuid;

final class Project extends AggregateRoot
{
    /**
     * @param array<User> $users
     * @param array<Place> $places
     * @param array<TurnAvailability> $turns
     * @param array<OpenCloseEvent> $openCloseEvents
     */
    private function __construct(
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
     * @param array<User> $users
     * @param array<Place> $places
     * @param array<TurnAvailability> $turns
     * @param array<OpenCloseEvent> $openCloseEvents
     */
    public static function new(
        Settings $settings,
        ?string $id = null,
        array $users = [],
        array $places = [],
        array $turns = [],
        array $openCloseEvents = []
    ): self {
        $project = new self(
            id: $id ?? (string) new Ksuid(),
            settings: $settings,
            users: $users,
            places: $places,
            turns: $turns,
            openCloseEvents: $openCloseEvents,
        );
        $project->addEvent(ProjectCreated::new(projectId: $project->getId(), project: $project));
        return $project;
    }

    /**
     * @param array<User> $users
     * @param array<Place> $places
     * @param array<TurnAvailability> $turns
     * @param array<OpenCloseEvent> $openCloseEvents
     */
    public static function build(
        string $id,
        Settings $settings,
        array $users = [],
        array $places = [],
        array $turns = [],
        array $openCloseEvents = [],
    ): self {
        return new self(
            id: $id,
            settings: $settings,
            users: $users,
            places: $places,
            turns: $turns,
            openCloseEvents: $openCloseEvents,
        );
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
        $users = array_filter($this->users, fn (User $s) => $s == $user);
        if (!empty($users)) {
            throw new UserAlreadyExist();
        }
        $this->users[] = $user;
        $this->addEvent(UserCreated::new(projectId: $this->getId(), user: $user));
    }

    public function removeUser(User $user): void
    {
        $users = array_filter($this->users, fn (User $s) => $s->username->equals($user->username));
        if (empty($users)) {
            throw new UserDoesNotExist();
        }

        $this->users = array_filter(
            $this->users,
            fn (User $s) => $s != $user
        );
        $this->addEvent(UserRemoved::new(projectId: $this->getId(), user: $user));
    }

    /**
     * @param callable(User): bool $filter
     */
    public function removeUsers(callable $filter): void
    {
        $usersToBeRemoved = array_filter($this->users, $filter);
        foreach ($usersToBeRemoved as $user) {
            $this->addEvent(UserRemoved::new(projectId: $this->getId(), user: $user));
        }
        $this->users = array_filter($this->users, fn (User $user) => !$filter($user));
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
        // TODO: check if other place with same name exists
        $places = array_filter($this->places, fn (Place $s) => $s->equals($place));
        if (!empty($places)) {
            throw new PlaceAlreadyExist();
        }
        $this->places[] = $place;
        $this->addEvent(PlaceCreated::new(projectId: $this->getId(), place: $place));
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
        $this->addEvent(PlaceRemoved::new(projectId: $this->getId(), place: $place));
    }

    /**
     * @param callable(Place): bool $filter
     */
    public function removePlaces(callable $filter): void
    {
        $placesToBeRemoved = array_filter($this->places, $filter);
        foreach ($placesToBeRemoved as $place) {
            $this->addEvent(PlaceRemoved::new(projectId: $this->getId(), place: $place));
        }
        $this->places = array_filter($this->places, fn (Place $place) => !$filter($place));
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
        $this->addEvent(TurnAssigned::new(projectId: $this->getId(), turn: $turn));
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
        $this->addEvent(TurnUnassigned::new(projectId: $this->getId(), turn: $turn));
    }

    /**
     * @param callable(TurnAvailability): bool $filter
     */
    public function removeTurns(callable $filter): void
    {
        $turnsToBeRemoved = array_filter($this->turns, $filter);
        foreach ($turnsToBeRemoved as $turn) {
            $this->addEvent(TurnUnassigned::new(projectId: $this->getId(), turn: $turn));
        }
        $this->turns = array_filter($this->turns, fn (TurnAvailability $turn) => !$filter($turn));
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
        $this->addEvent(
            OpenCloseEventCreated::new(projectId: $this->getId(), openCloseEvent: $event)
        );
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
        $this->addEvent(
            OpenCloseEventRemoved::new(projectId: $this->getId(), openCloseEvent: $event)
        );
    }

    /**
     * @param callable(OpenCloseEvent): bool $filter
     */
    public function removeOpenCloseEvents(callable $filter): void
    {
        $eventsToBeRemoved = array_filter($this->openCloseEvents, $filter);
        foreach ($eventsToBeRemoved as $event) {
            $this->addEvent(
                OpenCloseEventRemoved::new(projectId: $this->getId(), openCloseEvent: $event)
            );
        }
        $this->openCloseEvents = array_filter($this->openCloseEvents, fn (OpenCloseEvent $event) => !$filter($event));
    }

    public function getSettings(): Settings
    {
        return $this->settings;
    }

    public function updateSettings(Settings $settings): void
    {
        $this->settings = $settings;
        $this->addEvent(ProjectModified::new(projectId: $this->getId(), project: $this));
    }
}
