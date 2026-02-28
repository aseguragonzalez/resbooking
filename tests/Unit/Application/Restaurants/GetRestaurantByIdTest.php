<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants\GetRestaurantById;

use Application\Restaurants\GetRestaurantById\GetRestaurantByIdHandler;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdQuery;
use Application\Restaurants\GetRestaurantById\GetRestaurantByIdResult;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use Domain\Restaurants\ValueObjects\RestaurantId;
use PHPUnit\Framework\TestCase;
use Tests\Unit\RestaurantBuilder;

final class GetRestaurantByIdTest extends TestCase
{
    private Faker $faker;
    private RestaurantBuilder $restaurantBuilder;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
    }

    public function testItRetrievesRestaurantByIdAndReturnsResult(): void
    {
        $restaurant = $this->restaurantBuilder->build();
        $restaurantIdString = $restaurant->id->value;
        $restaurantId = RestaurantId::fromString($restaurantIdString);
        $repository = $this->createMock(RestaurantRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->with($restaurantId)
            ->willReturn($restaurant);
        $restaurantObtainer = new RestaurantObtainer($repository);
        $query = new GetRestaurantByIdQuery(id: $restaurantIdString);
        $service = new GetRestaurantByIdHandler($restaurantObtainer);

        $result = $service->handle($query);

        $this->assertInstanceOf(GetRestaurantByIdResult::class, $result);
        $settings = $restaurant->getSettings();
        $this->assertSame($restaurant->id->value, $result->id);
        $this->assertSame($settings->email->value, $result->email);
        $this->assertSame($settings->hasReminders, $result->hasReminders);
        $this->assertSame($settings->name, $result->name);
        $this->assertSame($settings->maxNumberOfDiners->value, $result->maxNumberOfDiners);
        $this->assertSame($settings->minNumberOfDiners->value, $result->minNumberOfDiners);
        $this->assertSame($settings->numberOfTables->value, $result->numberOfTables);
        $this->assertSame($settings->phone->value, $result->phone);
    }
}
