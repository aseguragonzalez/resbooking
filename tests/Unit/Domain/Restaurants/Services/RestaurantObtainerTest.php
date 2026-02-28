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
use Domain\Restaurants\ValueObjects\RestaurantId;
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
        $restaurantId = RestaurantId::fromString($this->faker->uuid);
        $restaurant = Restaurant::create($this->faker->email, $restaurantId->value);
        $this->restaurantRepository->expects($this->once())
            ->method('findBy')
            ->with($restaurantId)
            ->willReturn($restaurant);

        $result = $this->restaurantObtainer->obtain($restaurantId);

        $this->assertInstanceOf(Restaurant::class, $result);
        $this->assertSame($restaurant, $result);
        $this->assertTrue($restaurantId->equals($result->id));
    }

    public function testObtainThrowsExceptionWhenRestaurantDoesNotExist(): void
    {
        $restaurantId = RestaurantId::fromString($this->faker->uuid);
        $this->restaurantRepository->expects($this->once())
            ->method('findBy')
            ->with($restaurantId)
            ->willReturn(null);
        $this->expectException(RestaurantDoesNotExist::class);

        $this->restaurantObtainer->obtain($restaurantId);
    }
}
