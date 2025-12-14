<?php

declare(strict_types=1);

namespace Application\Restaurants\CreateNewRestaurant;

interface CreateNewRestaurant
{
    public function execute(CreateNewRestaurantCommand $command): void;
}
