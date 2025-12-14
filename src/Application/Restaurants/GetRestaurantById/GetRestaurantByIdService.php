<?php

declare(strict_types=1);

namespace Application\Restaurants\GetRestaurantById;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Services\RestaurantObtainer;

final readonly class GetRestaurantByIdService implements GetRestaurantById
{
    public function __construct(private RestaurantObtainer $restaurantObtainer)
    {
    }

    public function execute(GetRestaurantByIdCommand $command): Restaurant
    {
        return $this->restaurantObtainer->obtain($command->id);
    }
}
