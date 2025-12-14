<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Restaurants;

use Application\Restaurants\CreateNewRestaurant\CreateNewRestaurant;
use Application\Restaurants\CreateNewRestaurant\CreateNewRestaurantCommand;
use Application\Restaurants\CreateNewRestaurant\CreateNewRestaurantService;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Entities\Restaurant;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateNewRestaurantServiceTest extends TestCase
{
    private MockObject&RestaurantRepository $restaurantRepository;
    private CreateNewRestaurant $service;

    protected function setUp(): void
    {
        $this->restaurantRepository = $this->createMock(RestaurantRepository::class);
        $this->service = new CreateNewRestaurantService($this->restaurantRepository);
    }

    public function testExecuteCreatesAndSavesRestaurant(): void
    {
        $command = new CreateNewRestaurantCommand('test@example.com');
        $this->restaurantRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($restaurant) {
                return $restaurant instanceof Restaurant;
            }));

        $this->service->execute($command);
    }
}
