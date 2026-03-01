<?php

declare(strict_types=1);

namespace Domain\Restaurants\Entities;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Events\AvailabilitiesUpdated;
use Domain\Restaurants\Events\DiningAreaCreated;
use Domain\Restaurants\Events\DiningAreaModified;
use Domain\Restaurants\Events\DiningAreaRemoved;
use Domain\Restaurants\Events\RestaurantCreated;
use Domain\Restaurants\Events\RestaurantModified;
use Domain\Restaurants\Exceptions\DiningAreaAlreadyExist;
use Domain\Restaurants\Exceptions\DiningAreaNotFound;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Restaurants\ValueObjects\RestaurantId;
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Restaurants\ValueObjects\User;
use Domain\Restaurants\ValueObjects\DiningAreaId;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\TimeSlot;
use SeedWork\Domain\AggregateRoot;
use SeedWork\Domain\DomainEvent;

/**
 * @extends AggregateRoot<RestaurantId>
 */
final readonly class Restaurant extends AggregateRoot
{
    public const string DEFAULT_PHONE_NUMBER = '+34-555-0100';
    public const int DEFAULT_NUMBER_OF_TABLES = 20;
    public const int DEFAULT_MAX_NUMBER_OF_DINERS = 8;
    public const int DEFAULT_MIN_NUMBER_OF_DINERS = 1;
    public const string DEFAULT_RESTAURANT_NAME = 'New Restaurant';
    public const string DEFAULT_DINING_AREA_NAME = 'Default Dining Area';

    /**
     * @param array<User> $users
     * @param array<DiningArea> $diningAreas
     * @param array<Availability> $availabilities
     * @param array<DomainEvent> $domainEvents
     */
    private function __construct(
        RestaurantId $id,
        private Settings $settings,
        private array $users,
        public array $diningAreas,
        private array $availabilities,
        array $domainEvents = [],
    ) {
        parent::__construct($id, $domainEvents);
    }

    public static function create(string $email, ?string $id = null): self
    {
        $restaurantId = $id !== null ? RestaurantId::fromString($id) : RestaurantId::create();
        $restaurantEmail = new Email($email);
        $settings = new Settings(
            email: $restaurantEmail,
            hasReminders: true,
            name: self::DEFAULT_RESTAURANT_NAME,
            maxNumberOfDiners: new Capacity(self::DEFAULT_MAX_NUMBER_OF_DINERS),
            minNumberOfDiners: new Capacity(self::DEFAULT_MIN_NUMBER_OF_DINERS),
            numberOfTables: new Capacity(self::DEFAULT_NUMBER_OF_TABLES),
            phone: new Phone(self::DEFAULT_PHONE_NUMBER),
        );
        $user = new User(username: $restaurantEmail);

        /** @var array<Availability> $availabilities */
        $availabilities = [];
        foreach (DayOfWeek::all() as $dayOfWeek) {
            foreach (TimeSlot::all() as $timeSlot) {
                $availabilities[] = new Availability(
                    dayOfWeek: $dayOfWeek,
                    capacity: new Capacity(self::DEFAULT_NUMBER_OF_TABLES),
                    timeSlot: $timeSlot
                );
            }
        }

        $event = RestaurantCreated::create($restaurantId);

        return new self(
            id: $restaurantId,
            settings: $settings,
            users: [$user],
            diningAreas: [
                DiningArea::new(
                    capacity: new Capacity(self::DEFAULT_NUMBER_OF_TABLES),
                    name: self::DEFAULT_DINING_AREA_NAME
                ),
            ],
            availabilities: $availabilities,
            domainEvents: [$event],
        );
    }

    /**
     * @param array<User> $users
     * @param array<DiningArea> $diningAreas
     * @param array<Availability> $availabilities
     * @param array<DomainEvent> $domainEvents
     */
    public static function build(
        string $id,
        Settings $settings,
        array $users = [],
        array $diningAreas = [],
        array $availabilities = [],
        array $domainEvents = [],
    ): self {
        return new self(
            id: RestaurantId::fromString($id),
            settings: $settings,
            users: $users,
            diningAreas: $diningAreas,
            availabilities: $availabilities,
            domainEvents: $domainEvents,
        );
    }

    protected function validate(): void
    {
    }

    /**
     * @return array<User>
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @return array<DiningArea>
     */
    public function getDiningAreas(): array
    {
        return $this->diningAreas;
    }

    public function getDiningAreaById(DiningAreaId $diningAreaId): DiningArea
    {
        $found = array_filter(
            $this->diningAreas,
            fn (DiningArea $d) => $d->id->equals($diningAreaId)
        );
        if ($found === []) {
            throw new DiningAreaNotFound(diningAreaId: $diningAreaId);
        }
        return reset($found);
    }

    public function addDiningArea(DiningArea $diningArea): self
    {
        $conflicts = array_filter(
            $this->diningAreas,
            fn (DiningArea $existing) => $existing->equals($diningArea)
                || $existing->name === $diningArea->name
        );
        if ($conflicts !== []) {
            throw new DiningAreaAlreadyExist();
        }
        $newDiningAreas = [...$this->diningAreas, $diningArea];
        $event = DiningAreaCreated::create($this->id, $diningArea);
        return new self(
            id: $this->id,
            settings: $this->settings,
            users: $this->users,
            diningAreas: $newDiningAreas,
            availabilities: $this->availabilities,
            domainEvents: [...$this->collectEvents(), $event],
        );
    }

    public function removeDiningAreasById(DiningAreaId $diningAreaId): self
    {
        $toRemove = array_filter(
            $this->diningAreas,
            fn (DiningArea $d) => $d->id->equals($diningAreaId)
        );
        $newDiningAreas = array_filter(
            $this->diningAreas,
            fn (DiningArea $d) => !$d->id->equals($diningAreaId)
        );
        $events = $this->collectEvents();
        foreach ($toRemove as $diningArea) {
            $events[] = DiningAreaRemoved::create($this->id, $diningArea);
        }
        return new self(
            id: $this->id,
            settings: $this->settings,
            users: $this->users,
            diningAreas: array_values($newDiningAreas),
            availabilities: $this->availabilities,
            domainEvents: $events,
        );
    }

    public function updateDiningArea(DiningArea $diningArea): self
    {
        $exists = array_filter(
            $this->diningAreas,
            fn (DiningArea $d) => $d->id->equals($diningArea->id)
        );
        if ($exists === []) {
            throw new DiningAreaNotFound(diningAreaId: $diningArea->id);
        }
        $newDiningAreas = array_map(
            fn (DiningArea $d) => $d->id->equals($diningArea->id) ? $diningArea : $d,
            $this->diningAreas
        );
        $event = DiningAreaModified::create($this->id, $diningArea);
        return new self(
            id: $this->id,
            settings: $this->settings,
            users: $this->users,
            diningAreas: $newDiningAreas,
            availabilities: $this->availabilities,
            domainEvents: [...$this->collectEvents(), $event],
        );
    }

    /**
     * @return array<Availability>
     */
    public function getAvailabilities(): array
    {
        return $this->availabilities;
    }

    public function getSettings(): Settings
    {
        return $this->settings;
    }

    public function updateSettings(Settings $settings): self
    {
        $event = RestaurantModified::create($this->id);
        return new self(
            id: $this->id,
            settings: $settings,
            users: $this->users,
            diningAreas: $this->diningAreas,
            availabilities: $this->availabilities,
            domainEvents: [...$this->collectEvents(), $event],
        );
    }

    /**
     * @param array<Availability> $availabilities
     */
    public function updateAvailabilities(array $availabilities): self
    {
        $event = AvailabilitiesUpdated::create($this->id, $availabilities);
        return new self(
            id: $this->id,
            settings: $this->settings,
            users: $this->users,
            diningAreas: $this->diningAreas,
            availabilities: $availabilities,
            domainEvents: [...$this->collectEvents(), $event],
        );
    }
}
