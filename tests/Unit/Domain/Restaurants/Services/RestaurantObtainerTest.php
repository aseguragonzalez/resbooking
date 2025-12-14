<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Services;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Exceptions\RestaurantDoesNotExist;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RestaurantObtainerTest extends TestCase
{
    private Faker $faker;
    private MockObject&RestaurantRepository $restaurantRepository;
    private RestaurantObtainer $restaurantObtainer;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->restaurantRepository = $this->createMock(RestaurantRepository::class);
        $this->restaurantObtainer = new RestaurantObtainer($this->restaurantRepository);
    }

    public function testObtainReturnsRestaurantWhenItExists(): void
    {
        $restaurantId = $this->faker->uuid;
        $restaurant = Restaurant::new(email: $this->faker->email, id: $restaurantId);
        $this->restaurantRepository->expects($this->once())
            ->method('getById')
            ->with($restaurantId)
            ->willReturn($restaurant);


        $result = $this->restaurantObtainer->obtain($restaurantId);

        $this->assertInstanceOf(Restaurant::class, $result);
        $this->assertSame($restaurant, $result);
        $this->assertSame($restaurantId, $result->getId());
    }

    public function testObtainThrowsExceptionWhenRestaurantDoesNotExist(): void
    {
        $restaurantId = $this->faker->uuid;
        $this->restaurantRepository->expects($this->once())
            ->method('getById')
            ->with($restaurantId)
            ->willReturn(null);
        $this->expectException(RestaurantDoesNotExist::class);

        $this->restaurantObtainer->obtain($restaurantId);
    }
}
