<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants\AddDiningArea;

use Application\Restaurants\AddDiningArea\AddDiningAreaCommand;
use Application\Restaurants\AddDiningArea\AddDiningAreaHandler;
use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use Domain\Restaurants\ValueObjects\RestaurantId;
use PHPUnit\Framework\TestCase;
use Tests\Unit\RestaurantBuilder;

final class AddDiningAreaTest extends TestCase
{
    private Faker $faker;
    private RestaurantBuilder $restaurantBuilder;
    private MockObject&RestaurantRepository $restaurantRepository;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->restaurantBuilder = new RestaurantBuilder($this->faker);
        $this->restaurantRepository = $this->createMock(RestaurantRepository::class);
    }

    public function testCreateNewDiningArea(): void
    {
        $restaurant = $this->restaurantBuilder->build();
        $restaurantIdString = $restaurant->id->value;
        $restaurantId = RestaurantId::fromString($restaurantIdString);
        $this->restaurantRepository->expects($this->once())
            ->method('findBy')
            ->with($restaurantId)
            ->willReturn($restaurant);
        $this->restaurantRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (Restaurant $r) => count($r->getDiningAreas()) === 2));
        $restaurantObtainer = new RestaurantObtainer($this->restaurantRepository);
        $ApplicationService = new AddDiningAreaHandler($restaurantObtainer, $this->restaurantRepository);
        $request = new AddDiningAreaCommand(
            restaurantId: $restaurantIdString,
            name: $this->faker->name,
            capacity: $this->faker->randomNumber(2)
        );

        $ApplicationService->handle($request);
    }
}
