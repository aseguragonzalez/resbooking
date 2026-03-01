<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants\GetDiningAreaById;

use Application\Restaurants\GetDiningAreaById\GetDiningAreaByIdHandler;
use Application\Restaurants\GetDiningAreaById\GetDiningAreaByIdQuery;
use Application\Restaurants\GetDiningAreaById\GetDiningAreaByIdResult;
use Domain\Restaurants\Exceptions\DiningAreaNotFound;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Restaurants\ValueObjects\RestaurantId;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;
use Tests\Unit\RestaurantBuilder;

final class GetDiningAreaByIdTest extends TestCase
{
    private Faker $faker;
    private RestaurantBuilder $restaurantBuilder;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
    }

    public function testItReturnsDiningAreaWhenItExists(): void
    {
        $restaurant = $this->restaurantBuilder->build();
        $restaurantIdString = $restaurant->id->value;
        $firstDiningArea = $restaurant->getDiningAreas()[0];
        $diningAreaIdString = $firstDiningArea->id->value;
        $repository = $this->createMock(RestaurantRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->with($this->callback(function (RestaurantId $id) use ($restaurantIdString) {
                return $id->value === $restaurantIdString;
            }))
            ->willReturn($restaurant);
        $restaurantObtainer = new RestaurantObtainer($repository);
        $query = new GetDiningAreaByIdQuery(restaurantId: $restaurantIdString, diningAreaId: $diningAreaIdString);
        $handler = new GetDiningAreaByIdHandler($restaurantObtainer);

        $result = $handler->handle($query);

        $this->assertInstanceOf(GetDiningAreaByIdResult::class, $result);
        $this->assertSame($diningAreaIdString, $result->diningArea->id);
        $this->assertSame($firstDiningArea->name, $result->diningArea->name);
        $this->assertSame($firstDiningArea->capacity->value, $result->diningArea->capacity);
    }

    public function testItThrowsDiningAreaNotFoundWhenDiningAreaDoesNotExist(): void
    {
        $restaurant = $this->restaurantBuilder->build();
        $restaurantIdString = $restaurant->id->value;
        $nonExistentDiningAreaId = $this->faker->uuid();
        $repository = $this->createMock(RestaurantRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->with($this->callback(function (RestaurantId $id) use ($restaurantIdString) {
                return $id->value === $restaurantIdString;
            }))
            ->willReturn($restaurant);
        $restaurantObtainer = new RestaurantObtainer($repository);
        $query = new GetDiningAreaByIdQuery(restaurantId: $restaurantIdString, diningAreaId: $nonExistentDiningAreaId);
        $handler = new GetDiningAreaByIdHandler($restaurantObtainer);

        $this->expectException(DiningAreaNotFound::class);

        $handler->handle($query);
    }
}
