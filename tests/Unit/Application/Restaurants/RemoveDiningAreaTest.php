<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants\RemoveDiningArea;

use Application\Restaurants\RemoveDiningArea\RemoveDiningAreaCommand;
use Application\Restaurants\RemoveDiningArea\RemoveDiningAreaService;
use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Events\DiningAreaRemoved;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Shared\Capacity;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\RestaurantBuilder;

final class RemoveDiningAreaTest extends TestCase
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

    public function testDiningAreaFromRestaurant(): void
    {
        $diningArea = DiningArea::new(new Capacity(10), name: $this->faker->name);
        $diningAreas = [
            $diningArea,
            DiningArea::new(new Capacity(10), name: $this->faker->name),
        ];
        $restaurant = $this->restaurantBuilder->withDiningAreas($diningAreas)->build();
        $this->restaurantObtainer->expects($this->once())
            ->method('obtain')
            ->with($this->isString())
            ->willReturn($restaurant);
        $this->restaurantRepository
            ->expects($this->once())
            ->method('save')
            ->with($restaurant);
        $request = new RemoveDiningAreaCommand(restaurantId: $this->faker->uuid, diningAreaId: $diningArea->getId());
        $ApplicationService = new RemoveDiningAreaService($this->restaurantObtainer, $this->restaurantRepository);

        $ApplicationService->execute($request);

        $this->assertSame(1, count($restaurant->getDiningAreas()));
        $events = $restaurant->getEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(DiningAreaRemoved::class, $events[0]);
        $event = $events[0];
        $this->assertSame($diningArea, $event->getPayload()['diningArea']);
        $this->assertSame($restaurant->getId(), $event->getPayload()['restaurantId']);
    }
}
