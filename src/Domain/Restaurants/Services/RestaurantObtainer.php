<?php

declare(strict_types=1);

namespace Domain\Restaurants\Services;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Exceptions\RestaurantDoesNotExist;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\ValueObjects\RestaurantId;

readonly class RestaurantObtainer
{
    public function __construct(private RestaurantRepository $restaurantRepository)
    {
    }

    public function obtain(RestaurantId $id): Restaurant
    {
        $restaurant = $this->restaurantRepository->findBy($id);
        if ($restaurant === null) {
            throw new RestaurantDoesNotExist();
        }
        return $restaurant;
    }
}
