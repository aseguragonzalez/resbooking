<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Adapters\Repositories\Restaurants;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Restaurants\ValueObjects\User;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\Email;
use Domain\Shared\TimeSlot;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Adapters\Repositories\Restaurants\Models\Availability as AvailabilityModel;
use Infrastructure\Adapters\Repositories\Restaurants\Models\DiningArea as DiningAreaModel;
use Infrastructure\Adapters\Repositories\Restaurants\Models\Restaurant as RestaurantModel;
use Infrastructure\Adapters\Repositories\Restaurants\Models\Settings as SettingsModel;
use Infrastructure\Adapters\Repositories\Restaurants\RestaurantsMapper;
use PHPUnit\Framework\TestCase;
use Tests\Unit\RestaurantBuilder;

final class RestaurantsMapperTest extends TestCase
{
    private Faker $faker;
    private RestaurantBuilder $restaurantBuilder;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
    }

    public function testMapToDomainMapsRestaurantModelToDomainRestaurant(): void
    {
        $restaurantId = $this->faker->uuid;
        $email = $this->faker->email;
        $name = $this->faker->company();
        $phone = '+34-555-0100';

        $settingsModel = new SettingsModel(
            email: $email,
            hasReminders: true,
            name: $name,
            maxNumberOfDiners: 8,
            minNumberOfDiners: 1,
            numberOfTables: 25,
            phone: $phone
        );

        $diningAreaModel = new DiningAreaModel(
            id: $this->faker->uuid,
            capacity: 50,
            name: 'Main Hall'
        );

        $availabilityModel = new AvailabilityModel(
            capacity: 10,
            dayOfWeekId: DayOfWeek::Monday->value,
            timeSlotId: TimeSlot::H1200->value
        );

        $users = ['user1@example.com', 'user2@example.com'];

        $restaurantModel = new RestaurantModel(
            id: $restaurantId,
            settings: $settingsModel,
            diningAreas: [$diningAreaModel],
            availabilities: [$availabilityModel],
            users: $users
        );

        $restaurant = RestaurantsMapper::mapToDomain($restaurantModel);

        $this->assertInstanceOf(Restaurant::class, $restaurant);
        $this->assertSame($restaurantId, $restaurant->getId());
        $this->assertSame($email, $restaurant->getSettings()->email->value);
        $this->assertSame($name, $restaurant->getSettings()->name);
        $this->assertSame($phone, $restaurant->getSettings()->phone->value);
        $this->assertCount(1, $restaurant->getDiningAreas());
        $this->assertSame($diningAreaModel->name, $restaurant->getDiningAreas()[0]->name);
        $this->assertCount(1, $restaurant->getAvailabilities());
        $this->assertCount(2, $restaurant->getUsers());
    }

    public function testMapToModelMapsDomainRestaurantToRestaurantModel(): void
    {
        $restaurant = $this->restaurantBuilder->build();

        $model = RestaurantsMapper::mapToModel($restaurant);

        $this->assertInstanceOf(RestaurantModel::class, $model);
        $this->assertSame($restaurant->getId(), $model->id);
        $this->assertSame($restaurant->getSettings()->email->value, $model->settings->email);
        $this->assertSame($restaurant->getSettings()->name, $model->settings->name);
        $this->assertSame($restaurant->getSettings()->phone->value, $model->settings->phone);
        $this->assertCount(count($restaurant->getDiningAreas()), $model->diningAreas);
        $this->assertCount(count($restaurant->getAvailabilities()), $model->availabilities);
    }

    public function testMapToDomainWithEmptyDiningAreasAndAvailabilities(): void
    {
        $restaurantId = $this->faker->uuid;
        $settingsModel = new SettingsModel(
            email: $this->faker->email,
            hasReminders: false,
            name: $this->faker->company(),
            maxNumberOfDiners: 4,
            minNumberOfDiners: 1,
            numberOfTables: 10,
            phone: $this->faker->phoneNumber
        );

        $restaurantModel = new RestaurantModel(
            id: $restaurantId,
            settings: $settingsModel,
            diningAreas: [],
            availabilities: [],
            users: []
        );

        $restaurant = RestaurantsMapper::mapToDomain($restaurantModel);

        $this->assertSame($restaurantId, $restaurant->getId());
        $this->assertCount(0, $restaurant->getDiningAreas());
        $this->assertCount(0, $restaurant->getAvailabilities());
        $this->assertCount(0, $restaurant->getUsers());
    }

    public function testRoundTripPreservesData(): void
    {
        $restaurant = $this->restaurantBuilder
            ->withUsers([
                new User(new Email('a@example.com')),
                new User(new Email('b@example.com')),
            ])
            ->withDiningAreas([])
            ->withAvailabilities([
                new Availability(
                    capacity: new Capacity(5),
                    dayOfWeek: DayOfWeek::Tuesday,
                    timeSlot: TimeSlot::H1300
                ),
            ])
            ->build();

        $model = RestaurantsMapper::mapToModel($restaurant);
        $roundTrip = RestaurantsMapper::mapToDomain($model);

        $this->assertSame($restaurant->getId(), $roundTrip->getId());
        $this->assertSame($restaurant->getSettings()->email->value, $roundTrip->getSettings()->email->value);
        $this->assertSame($restaurant->getSettings()->name, $roundTrip->getSettings()->name);
        $this->assertCount(count($restaurant->getUsers()), $roundTrip->getUsers());
        $this->assertCount(count($restaurant->getAvailabilities()), $roundTrip->getAvailabilities());
    }
}
