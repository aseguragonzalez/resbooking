<?php

declare(strict_types=1);

namespace Application\Restaurants\CreateNewRestaurant;

use SeedWork\Application\Command;

final readonly class CreateNewRestaurantCommand extends Command
{
    public function __construct(public string $email)
    {
        parent::__construct();
    }
}
