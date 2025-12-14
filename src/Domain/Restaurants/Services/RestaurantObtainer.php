<?php

declare(strict_types=1);

namespace Domain\Restaurants\Services;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Exceptions\RestaurantDoesNotExist;
use Domain\Restaurants\Repositories\RestaurantRepository;

readonly class RestaurantObtainer
{
    public function __construct(private RestaurantRepository $restaurantRepository)
    {
    }

    public function obtain(string $id): Restaurant
    {
        $restaurant = $this->restaurantRepository->getById($id);
        if ($restaurant === null) {
            throw new RestaurantDoesNotExist();
        }
        return $restaurant;
    }
}
