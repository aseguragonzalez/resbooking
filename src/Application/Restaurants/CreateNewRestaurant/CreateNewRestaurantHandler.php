<?php

declare(strict_types=1);

namespace Application\Restaurants\CreateNewRestaurant;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Repositories\RestaurantRepository;
use SeedWork\Application\Command;

final readonly class CreateNewRestaurantHandler implements CreateNewRestaurant
{
    public function __construct(private RestaurantRepository $restaurantRepository)
    {
    }

    /**
     * @param CreateNewRestaurantCommand $command
     */
    public function handle(Command $command): void
    {
        $restaurant = Restaurant::new(email: $command->email);

        $this->restaurantRepository->save($restaurant);
    }
}
