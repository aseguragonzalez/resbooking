<?php

declare(strict_types=1);

namespace Application\Restaurants\GetRestaurantById;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Services\RestaurantObtainer;
use Seedwork\Domain\EntityId;

final readonly class GetRestaurantByIdHandler implements GetRestaurantById
{
    public function __construct(private RestaurantObtainer $restaurantObtainer)
    {
    }

    public function execute(GetRestaurantByIdQuery $query): Restaurant
    {
        return $this->restaurantObtainer->obtain(EntityId::fromString($query->id));
    }
}
