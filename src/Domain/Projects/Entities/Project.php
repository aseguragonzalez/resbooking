<?php

declare(strict_types=1);

namespace Domain\Projects\Entities;

use Domain\Projects\Entities\Place;
use Domain\Projects\Events\OpenCloseEventCreated;
use Domain\Projects\Events\OpenCloseEventRemoved;
use Domain\Projects\Events\PlaceCreated;
use Domain\Projects\Events\PlaceRemoved;
use Domain\Projects\Events\ProjectCreated;
use Domain\Projects\Events\ProjectModified;
use Domain\Projects\Events\TurnsUpdated;
use Domain\Projects\Exceptions\OpenCloseEventAlreadyExist;
use Domain\Projects\Exceptions\OpenCloseEventDoesNotExist;
use Domain\Projects\Exceptions\OpenCloseEventOutOfRange;
use Domain\Projects\Exceptions\PlaceAlreadyExist;
use Domain\Projects\ValueObjects\OpenCloseEvent;
use Domain\Projects\ValueObjects\Settings;
use Domain\Projects\ValueObjects\TurnAvailability;
use Domain\Projects\ValueObjects\User;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\Turn;
use Seedwork\Domain\AggregateRoot;

final class Project extends AggregateRoot
{
    public const string DEFAULT_PHONE_NUMBER = '+34-555-0100';
    public const int DEFAULT_NUMBER_OF_TABLES = 20;
    public const int DEFAULT_MAX_NUMBER_OF_DINERS = 8;
    public const int DEFAULT_MIN_NUMBER_OF_DINERS = 1;
    public const string DEFAULT_PROJECT_NAME = 'New Project';
    public const string DEFAULT_PLACE_NAME = 'Default Place';

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

    public static function new(string $email, ?string $id = null): self
    {
        $projectEmail = new Email($email);
        $settings = new Settings(
            email: $projectEmail,
            hasReminders: true,
            name: self::DEFAULT_PROJECT_NAME,
            maxNumberOfDiners: new Capacity(self::DEFAULT_MAX_NUMBER_OF_DINERS),
            minNumberOfDiners: new Capacity(self::DEFAULT_MIN_NUMBER_OF_DINERS),
            numberOfTables: new Capacity(self::DEFAULT_NUMBER_OF_TABLES),
            phone: new Phone(self::DEFAULT_PHONE_NUMBER),
        );
        $user = new User(username: $projectEmail);

        /** @var array<TurnAvailability> */
        $turns = [];
        foreach (DayOfWeek::all() as $dayOfWeek) {
            foreach (Turn::all() as $turn) {
                $turns[] = new TurnAvailability(
                    dayOfWeek: $dayOfWeek,
                    capacity: new Capacity(self::DEFAULT_NUMBER_OF_TABLES),
                    turn: $turn
                );
            }
        }

        $project = new self(
            id: $id ?? uniqid(),
            settings: $settings,
            users: [$user],
            places: [Place::new(
                capacity: new Capacity(self::DEFAULT_NUMBER_OF_TABLES),
                name: self::DEFAULT_PLACE_NAME
            )],
            turns: $turns,
            openCloseEvents: [],
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

    /**
     * @param array<TurnAvailability> $turns
     */
    public function updateTurns(array $turns): void
    {
        $this->turns = $turns;
        $this->addEvent(TurnsUpdated::new(projectId: $this->getId(), turns: $turns));
    }
}
