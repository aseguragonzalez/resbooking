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
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Restaurants\ValueObjects\User;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Seedwork\Domain\EntityId;
use Domain\Shared\TimeSlot;
use Seedwork\Domain\AggregateRoot;

final class Restaurant extends AggregateRoot
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
     */
    private function __construct(
        EntityId $id,
        private Settings $settings,
        private array $users = [],
        private array $diningAreas = [],
        private array $availabilities = [],
    ) {
        parent::__construct($id);
    }

    public static function new(string $email, ?string $id = null): self
    {
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

        /** @var array<Availability> */
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

        $restaurant = new self(
            id: $id !== null ? EntityId::fromString($id) : EntityId::new(),
            settings: $settings,
            users: [$user],
            diningAreas: [DiningArea::new(
                capacity: new Capacity(self::DEFAULT_NUMBER_OF_TABLES),
                name: self::DEFAULT_DINING_AREA_NAME
            )],
            availabilities: $availabilities,
        );
        $restaurant->addEvent(RestaurantCreated::new(restaurantId: $restaurant->getId(), restaurant: $restaurant));
        return $restaurant;
    }

    /**
     * @param array<User> $users
     * @param array<DiningArea> $diningAreas
     * @param array<Availability> $availabilities
     */
    public static function build(
        string $id,
        Settings $settings,
        array $users = [],
        array $diningAreas = [],
        array $availabilities = [],
    ): self {
        return new self(
            id: EntityId::fromString($id),
            settings: $settings,
            users: $users,
            diningAreas: $diningAreas,
            availabilities: $availabilities,
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
     * @return array<DiningArea>
     */
    public function getDiningAreas(): array
    {
        return $this->diningAreas;
    }

    public function addDiningArea(DiningArea $diningArea): void
    {
        $diningAreas = array_filter(
            $this->diningAreas,
            fn (DiningArea $existingDiningArea) => $existingDiningArea->equals($diningArea)
                || $existingDiningArea->name === $diningArea->name
        );
        if (!empty($diningAreas)) {
            throw new DiningAreaAlreadyExist();
        }
        $this->diningAreas[] = $diningArea;
        $this->addEvent(DiningAreaCreated::new(restaurantId: $this->getId(), diningArea: $diningArea));
    }

    public function removeDiningAreasById(EntityId $diningAreaId): void
    {
        $filter = fn (DiningArea $diningArea) => $diningArea->id->equals($diningAreaId);
        $diningAreasToBeRemoved = array_filter($this->diningAreas, $filter);
        foreach ($diningAreasToBeRemoved as $diningArea) {
            $this->addEvent(DiningAreaRemoved::new(restaurantId: $this->getId(), diningArea: $diningArea));
        }
        $this->diningAreas = array_filter($this->diningAreas, fn (DiningArea $diningArea) => !$filter($diningArea));
    }

    public function updateDiningArea(DiningArea $diningArea): void
    {
        $existingDiningArea = array_filter(
            $this->diningAreas,
            fn (DiningArea $existingDiningArea) => $existingDiningArea->id->equals($diningArea->id)
        );
        if (empty($existingDiningArea)) {
            throw new DiningAreaNotFound(diningAreaId: $diningArea->id);
        }

        $this->diningAreas = array_map(
            fn (DiningArea $s) => $s->id->equals($diningArea->id) ? $diningArea : $s,
            $this->diningAreas
        );
        $this->addEvent(DiningAreaModified::new(restaurantId: $this->getId(), diningArea: $diningArea));
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

    public function updateSettings(Settings $settings): void
    {
        $this->settings = $settings;
        $this->addEvent(RestaurantModified::new(restaurantId: $this->getId(), restaurant: $this));
    }

    /**
     * @param array<Availability> $availabilities
     */
    public function updateAvailabilities(array $availabilities): void
    {
        $this->availabilities = $availabilities;
        $this->addEvent(AvailabilitiesUpdated::new(restaurantId: $this->getId(), availabilities: $availabilities));
    }
}
