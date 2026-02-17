<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants\UpdateDiningArea;

use Application\Restaurants\UpdateDiningArea\UpdateDiningAreaCommand;
use Application\Restaurants\UpdateDiningArea\UpdateDiningAreaHandler;
use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Events\DiningAreaModified;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Shared\Capacity;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Seedwork\Domain\EntityId;
use Tests\Unit\RestaurantBuilder;

final class UpdateDiningAreaTest extends TestCase
{
    private Faker $faker;
    private RestaurantBuilder $restaurantBuilder;
    private MockObject&RestaurantRepository $restaurantRepository;
    private MockObject&RestaurantObtainer $restaurantObtainer;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
        $this->restaurantRepository = $this->createMock(RestaurantRepository::class);
        $this->restaurantObtainer = $this->createMock(RestaurantObtainer::class);
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
        $this->restaurantObtainer->expects($this->once())
            ->method('obtain')
            ->with(EntityId::fromString($restaurant->getId()->value))
            ->willReturn($restaurant);
        $this->restaurantRepository
            ->expects($this->once())
            ->method('save')
            ->with($restaurant);

        $newName = $this->faker->name();
        $newCapacity = 20;
        $request = new UpdateDiningAreaCommand(
            restaurantId: $restaurant->getId()->value,
            diningAreaId: $diningAreaId,
            name: $newName,
            capacity: $newCapacity
        );
        $applicationService = new UpdateDiningAreaHandler($this->restaurantObtainer, $this->restaurantRepository);

        $applicationService->execute($request);

        $this->assertSame(1, count($restaurant->getDiningAreas()));
        $updatedDiningArea = $restaurant->getDiningAreas()[0];
        $this->assertSame($diningAreaId, $updatedDiningArea->id->value);
        $this->assertSame($newName, $updatedDiningArea->name);
        $this->assertSame($newCapacity, $updatedDiningArea->capacity->value);
        $events = $restaurant->getEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(DiningAreaModified::class, $events[0]);
        $event = $events[0];
        $this->assertSame($updatedDiningArea, $event->payload['diningArea']);
        $this->assertSame($restaurant->getId()->value, $event->payload['restaurantId']);
    }
}
