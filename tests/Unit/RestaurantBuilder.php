<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Restaurants\ValueObjects\User;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\TimeSlot;
use Faker\Generator as Faker;

final class RestaurantBuilder
{
    /**
     * @var array<DiningArea>|null $diningAreas
     */
    private array|null $diningAreas;

    /**
     * @var array<User> $users
     */
    private array $users;

    /**
     * @var array<Availability>|null $availabilities
     */
    private array|null $availabilities;

    private ?Settings $settings;

    public function __construct(private Faker $faker)
    {
        $this->diningAreas = null;
        $this->availabilities = null;
        $this->users = [];
        $this->settings = null;
    }

    public function build(): Restaurant
    {
        $settings = new Settings(
            email: new Email($this->faker->email),
            hasReminders: $this->faker->boolean,
            name: $this->faker->company(),
            maxNumberOfDiners: new Capacity(8),
            minNumberOfDiners: new Capacity(1),
            numberOfTables: new Capacity(25),
            phone: new Phone($this->faker->phoneNumber)
        );
        $capacity_value = $this->faker->numberBetween(1, 100);
        return Restaurant::build(
            id: $this->faker->uuid,
            settings: $this->settings ?? $settings,
            diningAreas: $this->diningAreas ?? [
                DiningArea::new(capacity: new Capacity($capacity_value), name: 'Area 1')
            ],
            users: $this->users,
            availabilities: $this->availabilities ?? $this->getDefaultAvailabilities(capacity_value: $capacity_value),
        );
    }

    /**
     * @return array<Availability> default availabilities for all days and time slots
     */
    private function getDefaultAvailabilities(int $capacity_value): array
    {
        $availabilities = [];
        $capacity = new Capacity($capacity_value);
        foreach (DayOfWeek::all() as $dayOfWeek) {
            foreach (TimeSlot::all() as $timeSlot) {
                $availabilities[] = new Availability(
                    dayOfWeek: $dayOfWeek,
                    capacity: $capacity,
                    timeSlot: $timeSlot
                );
            }
        }
        return $availabilities;
    }

    /**
     * @param array<DiningArea> $diningAreas
     * @return RestaurantBuilder
     */
    public function withDiningAreas(array $diningAreas = []): RestaurantBuilder
    {
        $this->diningAreas = $diningAreas;
        return $this;
    }

    public function withSettings(Settings $settings): RestaurantBuilder
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @param array<Availability> $availabilities
     * @return RestaurantBuilder
     */
    public function withAvailabilities(array $availabilities = []): RestaurantBuilder
    {
        $this->availabilities = $availabilities;
        return $this;
    }

    /**
     * @param array<User> $users
     * @return RestaurantBuilder
     */
    public function withUsers(array $users = []): RestaurantBuilder
    {
        $this->users = $users;
        return $this;
    }
}
