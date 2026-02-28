<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants\UpdateDiningArea;

use Application\Restaurants\UpdateDiningArea\UpdateDiningAreaCommand;
use Application\Restaurants\UpdateDiningArea\UpdateDiningAreaHandler;
use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Events\DiningAreaModified;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Restaurants\ValueObjects\RestaurantId;
use Domain\Shared\Capacity;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\RestaurantBuilder;

final class UpdateDiningAreaTest extends TestCase
{
    private Faker $faker;
    private RestaurantBuilder $restaurantBuilder;
    private MockObject&RestaurantRepository $restaurantRepository;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
        $this->restaurantRepository = $this->createMock(RestaurantRepository::class);
    }

    public function testUpdateDiningAreaInRestaurant(): void
    {
        $diningAreaId = $this->faker->uuid();
        $originalDiningArea = DiningArea::build(
            id: $diningAreaId,
            capacity: new Capacity(10),
            name: $this->faker->name()
        );
        $diningAreas = [$originalDiningArea];
        $restaurant = $this->restaurantBuilder->withDiningAreas($diningAreas)->build();
        $this->restaurantRepository->expects($this->once())
            ->method('findBy')
            ->with($restaurant->id)
            ->willReturn($restaurant);
        $savedRestaurant = null;
        $this->restaurantRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($r) use (&$savedRestaurant) {
                $savedRestaurant = $r;
                return true;
            }));

        $newName = $this->faker->name();
        $newCapacity = 20;
        $request = new UpdateDiningAreaCommand(
            restaurantId: $restaurant->id->value,
            diningAreaId: $diningAreaId,
            name: $newName,
            capacity: $newCapacity
        );
        $restaurantObtainer = new RestaurantObtainer($this->restaurantRepository);
        $applicationService = new UpdateDiningAreaHandler($restaurantObtainer, $this->restaurantRepository);

        $applicationService->handle($request);

        $this->assertInstanceOf(\Domain\Restaurants\Entities\Restaurant::class, $savedRestaurant);
        $this->assertSame(1, count($savedRestaurant->getDiningAreas()));
        $updatedDiningArea = $savedRestaurant->getDiningAreas()[0];
        $this->assertSame($diningAreaId, $updatedDiningArea->id->value);
        $this->assertSame($newName, $updatedDiningArea->name);
        $this->assertSame($newCapacity, $updatedDiningArea->capacity->value);
        $events = $savedRestaurant->collectEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(DiningAreaModified::class, $events[0]);
        $event = $events[0];
        $this->assertSame($updatedDiningArea->id->value, $event->payload['dining_area_id']);
        $this->assertSame($restaurant->id->value, $event->payload['restaurant_id']);
    }
}
