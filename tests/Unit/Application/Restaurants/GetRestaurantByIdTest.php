<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants\GetRestaurantById;

use Application\Restaurants\GetRestaurantById\GetRestaurantByIdCommand;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdHandler;
use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Services\RestaurantObtainer;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\RestaurantBuilder;

final class GetRestaurantByIdTest extends TestCase
{
    private Faker $faker;
    private RestaurantBuilder $restaurantBuilder;
    private MockObject&RestaurantObtainer $restaurantObtainer;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
        $this->restaurantObtainer = $this->createMock(RestaurantObtainer::class);
    }

    public function testItRetrievesRestaurantById(): void
    {
        // Arrange
        $restaurantId = $this->faker->uuid;
        $restaurant = $this->restaurantBuilder->build();
        $this->restaurantObtainer->expects($this->once())
            ->method('obtain')
            ->with($restaurantId)
            ->willReturn($restaurant);
        $command = new GetRestaurantByIdCommand(id: $restaurantId);
        $service = new GetRestaurantByIdHandler($this->restaurantObtainer);

        // Act
        $result = $service->execute($command);

        // Assert
        $this->assertSame($restaurant, $result);
        $this->assertInstanceOf(Restaurant::class, $result);
    }
}
