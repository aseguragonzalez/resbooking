<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants\GetRestaurantById;

use Application\Restaurants\GetRestaurantById\GetRestaurantByIdHandler;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdQuery;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdResult;
use Domain\Restaurants\Services\RestaurantObtainer;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Seedwork\Domain\EntityId;
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

    public function testItRetrievesRestaurantByIdAndReturnsResult(): void
    {
        $restaurantIdString = $this->faker->uuid;
        $restaurant = $this->restaurantBuilder->build();
        $this->restaurantObtainer->expects($this->once())
            ->method('obtain')
            ->with(EntityId::fromString($restaurantIdString))
            ->willReturn($restaurant);
        $query = new GetRestaurantByIdQuery(id: $restaurantIdString);
        $service = new GetRestaurantByIdHandler($this->restaurantObtainer);

        $result = $service->handle($query);

        $this->assertInstanceOf(GetRestaurantByIdResult::class, $result);
        $settings = $restaurant->getSettings();
        $this->assertSame($restaurant->getId()->value, $result->id);
        $this->assertSame($settings->email->value, $result->email);
        $this->assertSame($settings->hasReminders, $result->hasReminders);
        $this->assertSame($settings->name, $result->name);
        $this->assertSame($settings->maxNumberOfDiners->value, $result->maxNumberOfDiners);
        $this->assertSame($settings->minNumberOfDiners->value, $result->minNumberOfDiners);
        $this->assertSame($settings->numberOfTables->value, $result->numberOfTables);
        $this->assertSame($settings->phone->value, $result->phone);
    }
}
