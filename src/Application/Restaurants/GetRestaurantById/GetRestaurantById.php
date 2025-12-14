<?php

declare(strict_types=1);

namespace Application\Restaurants\GetRestaurantById;

use Domain\Restaurants\Entities\Restaurant;

interface GetRestaurantById
{
    public function execute(GetRestaurantByIdCommand $command): Restaurant;
}
