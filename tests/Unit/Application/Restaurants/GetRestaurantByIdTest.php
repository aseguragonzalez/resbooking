<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants\GetRestaurantById;

use Application\Restaurants\GetRestaurantById\GetRestaurantByIdHandler;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdQuery;
use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Services\RestaurantObtainer;
use Faker\Factory as FakerFactory;
use Seedwork\Domain\EntityId;
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
        $restaurantIdString = $this->faker->uuid;
        $restaurant = $this->restaurantBuilder->build();
        $this->restaurantObtainer->expects($this->once())
            ->method('obtain')
            ->with(EntityId::fromString($restaurantIdString))
            ->willReturn($restaurant);
        $query = new GetRestaurantByIdQuery(id: $restaurantIdString);
        $service = new GetRestaurantByIdHandler($this->restaurantObtainer);

        $result = $service->execute($query);

        $this->assertSame($restaurant, $result);
        $this->assertInstanceOf(Restaurant::class, $result);
    }
}
