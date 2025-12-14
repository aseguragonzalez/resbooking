<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Restaurants\ValueObjects\User;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Faker\Generator as Faker;

final class RestaurantBuilder
{
    /**
     * @var array<DiningArea> $diningAreas
     */
    private array $diningAreas;

    /**
     * @var array<User> $users
     */
    private array $users;

    /**
     * @var array<Availability> $availabilities
     */
    private array $availabilities;

    private ?Settings $settings;

    public function __construct(private readonly Faker $faker)
    {
        $this->diningAreas = [];
        $this->availabilities = [];
        $this->users = [];
        $this->settings = null;
    }

    public function build(): Restaurant
    {
        $settings = new Settings(
            email: new Email($this->faker->email),
            hasReminders: $this->faker->boolean,
            name: $this->faker->name,
            maxNumberOfDiners: new Capacity(8),
            minNumberOfDiners: new Capacity(1),
            numberOfTables: new Capacity(25),
            phone: new Phone($this->faker->phoneNumber)
        );
        return Restaurant::build(
            id: $this->faker->uuid,
            settings: $this->settings ?? $settings,
            diningAreas: $this->diningAreas,
            users: $this->users,
            availabilities: $this->availabilities,
        );
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
