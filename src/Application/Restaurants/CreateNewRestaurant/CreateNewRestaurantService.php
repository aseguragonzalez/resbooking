<?php

declare(strict_types=1);

namespace Application\Restaurants\CreateNewRestaurant;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Repositories\RestaurantRepository;

final readonly class CreateNewRestaurantService implements CreateNewRestaurant
{
    public function __construct(private RestaurantRepository $restaurantRepository)
    {
    }

    public function execute(CreateNewRestaurantCommand $command): void
    {
        $restaurant = Restaurant::new(email: $command->email);

        $this->restaurantRepository->save($restaurant);
    }
}
