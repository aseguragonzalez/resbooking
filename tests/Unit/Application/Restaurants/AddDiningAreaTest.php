<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants\AddDiningArea;

use Application\Restaurants\AddDiningArea\AddDiningAreaCommand;
use Application\Restaurants\AddDiningArea\AddDiningAreaService;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\RestaurantBuilder;

final class AddDiningAreaTest extends TestCase
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

    public function testCreateNewDiningArea(): void
    {
        $restaurant = $this->restaurantBuilder->build();
        $this->restaurantObtainer->expects($this->once())
            ->method('obtain')
            ->with($this->isString())
            ->willReturn($restaurant);
        $this->restaurantRepository
            ->expects($this->once())
            ->method('save')
            ->with($restaurant);
        $ApplicationService = new AddDiningAreaService($this->restaurantObtainer, $this->restaurantRepository);
        $request = new AddDiningAreaCommand(
            restaurantId: $this->faker->uuid,
            name: $this->faker->name,
            capacity: $this->faker->randomNumber(2)
        );

        $ApplicationService->execute($request);

        $this->assertSame(1, count($restaurant->getDiningAreas()));
    }
}
