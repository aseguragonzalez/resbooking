<?php

declare(strict_types=1);

namespace Application\Restaurants\CreateNewRestaurant;

final readonly class CreateNewRestaurantCommand
{
    public function __construct(public string $email)
    {
    }
}
