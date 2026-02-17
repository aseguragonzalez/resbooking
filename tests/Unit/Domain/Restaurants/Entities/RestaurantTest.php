<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Entities;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Events\AvailabilitiesUpdated;
use Domain\Restaurants\Events\DiningAreaCreated;
use Domain\Restaurants\Events\DiningAreaModified;
use Domain\Restaurants\Events\DiningAreaRemoved;
use Domain\Restaurants\Events\RestaurantCreated;
use Domain\Restaurants\Events\RestaurantModified;
use Domain\Restaurants\Exceptions\DiningAreaAlreadyExist;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Restaurants\ValueObjects\Settings;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Seedwork\Domain\EntityId;
use Domain\Shared\TimeSlot;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;
use Seedwork\Domain\EntityId;
use Tests\Unit\RestaurantBuilder;

final class RestaurantTest extends TestCase
{
    private Faker $faker;
    private RestaurantBuilder $restaurantBuilder;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
    }

    protected function tearDown(): void
    {
    }

    private function settings(): Settings
    {
        return new Settings(
            email: new Email($this->faker->email),
            hasReminders: true,
            name: 'New Restaurant',
            maxNumberOfDiners: new Capacity(8),
            minNumberOfDiners: new Capacity(1),
            numberOfTables: new Capacity(Restaurant::DEFAULT_NUMBER_OF_TABLES),
            phone: new Phone(Restaurant::DEFAULT_PHONE_NUMBER)
        );
    }

    public function testCreateDefaultRestaurant(): void
    {
        $id = $this->faker->uuid;
        $settings = $this->settings();

        $restaurant = Restaurant::new(id: $id, email: $settings->email->value);

        $this->assertSame($id, $restaurant->getId()->value);
        $restaurantSettings = $restaurant->getSettings();
        $this->assertSame($settings->email->value, $restaurantSettings->email->value);
        $this->assertSame($settings->hasReminders, $restaurantSettings->hasReminders);
        $this->assertSame($settings->name, $restaurantSettings->name);
        $this->assertSame($settings->maxNumberOfDiners->value, $restaurantSettings->maxNumberOfDiners->value);
        $this->assertSame($settings->minNumberOfDiners->value, $restaurantSettings->minNumberOfDiners->value);
        $this->assertSame($settings->numberOfTables->value, $restaurantSettings->numberOfTables->value);
        $this->assertSame($settings->phone->value, $restaurantSettings->phone->value);
        $this->assertCount(1, $restaurant->getUsers());
        $this->assertCount(1, $restaurant->getDiningAreas());
        $numberOfAvailabilities = count(DayOfWeek::all()) * count(TimeSlot::all());
        $this->assertCount($numberOfAvailabilities, $restaurant->getAvailabilities());
        $events = $restaurant->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertSame($restaurant, $event->payload['restaurant']);
        $this->assertSame($restaurant->getId()->value, $event->payload['restaurantId']);
        $this->assertInstanceOf(Restaurant::class, $restaurant);
        $this->assertInstanceOf(RestaurantCreated::class, $events[0]);
    }

    public function testAddDiningAreaToRestaurant(): void
    {
        $restaurant = $this->restaurantBuilder->withSettings($this->settings())->build();
        $diningArea = DiningArea::new(name: $this->faker->name, capacity: new Capacity(value: 100));

        $restaurant->addDiningArea($diningArea);

        $this->assertContains($diningArea, $restaurant->getDiningAreas());
        $events = $restaurant->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertSame($diningArea, $event->payload['diningArea']);
        $this->assertSame($restaurant->getId()->value, $event->payload['restaurantId']);
        $this->assertInstanceOf(DiningAreaCreated::class, $events[0]);
    }

    public function testAddDiningAreaFailWhenDiningAreaAlreadyExist(): void
    {
        $diningArea = DiningArea::new(name: $this->faker->name, capacity: new Capacity(value: 100));
        $restaurant = $this->restaurantBuilder
            ->withSettings($this->settings())
            ->withDiningAreas([$diningArea])
            ->build();
        $this->expectException(DiningAreaAlreadyExist::class);

        $restaurant->addDiningArea($diningArea);
    }

    public function testUpdateRestaurantSettings(): void
    {
        $restaurant = $this->restaurantBuilder->withSettings($this->settings())->build();
        $settings = $this->settings();

        $restaurant->updateSettings($settings);

        $this->assertSame($settings, $restaurant->getSettings());
        $events = $restaurant->getEvents();
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertSame($restaurant, $event->payload['restaurant']);
        $this->assertSame($restaurant->getId()->value, $event->payload['restaurantId']);
        $this->assertInstanceOf(RestaurantModified::class, $events[0]);
    }

    public function testRemoveDiningAreasFromRestaurant(): void
    {
        $diningAreas = [
            DiningArea::new(name: $this->faker->name, capacity: new Capacity(value: 100)),
            DiningArea::new(name: $this->faker->name, capacity: new Capacity(value: 100)),
            DiningArea::new(name: $this->faker->name, capacity: new Capacity(value: 100)),
        ];
        $restaurant = $this->restaurantBuilder->withSettings($this->settings())->withDiningAreas($diningAreas)->build();
        $diningAreaToRemove = $diningAreas[1];

        $restaurant->removeDiningAreasById($diningAreaToRemove->id);

        $events = $restaurant->getEvents();
        $event = $events[0];
        $this->assertNotContains($diningAreaToRemove, $restaurant->getDiningAreas());
        $this->assertCount(1, $events);
        $this->assertSame($diningAreaToRemove, $event->payload['diningArea']);
        $this->assertSame($restaurant->getId()->value, $event->payload['restaurantId']);
        $this->assertInstanceOf(DiningAreaRemoved::class, $events[0]);
    }

    public function testUpdateDiningAreaInRestaurant(): void
    {
        $originalDiningArea = DiningArea::new(name: $this->faker->name, capacity: new Capacity(value: 100));
        $restaurant = $this->restaurantBuilder
            ->withSettings($this->settings())
            ->withDiningAreas([$originalDiningArea])
            ->build();
        $updatedDiningArea = DiningArea::build(
            id: $originalDiningArea->id->value,
            capacity: new Capacity(value: 200),
            name: $this->faker->name
        );

        $restaurant->updateDiningArea($updatedDiningArea);

        $events = $restaurant->getEvents();
        $event = $events[0];
        $this->assertContains($updatedDiningArea, $restaurant->getDiningAreas());
        $this->assertNotContains($originalDiningArea, $restaurant->getDiningAreas());
        $this->assertCount(1, $events);
        $this->assertSame($updatedDiningArea, $event->payload['diningArea']);
        $this->assertSame($restaurant->getId()->value, $event->payload['restaurantId']);
        $this->assertInstanceOf(DiningAreaModified::class, $events[0]);
    }

    public function testUpdateAvailabilitiesSucceedsAndEmitsAvailabilitiesUpdatedEvent(): void
    {
        $restaurant = $this->restaurantBuilder->withSettings($this->settings())->build();
        $newAvailabilities = [
            new Availability(
                capacity: new Capacity(value: 10),
                dayOfWeek: DayOfWeek::Monday,
                timeSlot: TimeSlot::H1200,
            ),
            new Availability(
                capacity: new Capacity(value: 20),
                dayOfWeek: DayOfWeek::Tuesday,
                timeSlot: TimeSlot::H1230,
            ),
        ];

        $restaurant->updateAvailabilities($newAvailabilities);

        $events = $restaurant->getEvents();
        $event = $events[0];
        $this->assertSame($newAvailabilities, $restaurant->getAvailabilities());
        $this->assertSame(1, count($events));
        $this->assertSame($restaurant->getId()->value, $event->payload['restaurantId']);
        $this->assertSame($newAvailabilities, $event->payload['availabilities']);
        $this->assertInstanceOf(AvailabilitiesUpdated::class, $events[0]);
    }
}
