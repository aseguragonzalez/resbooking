<?php

declare(strict_types=1);

namespace Application\Restaurants\GetRestaurantById;

final readonly class DiningAreaItem
{
    public function __construct(
        public string $id,
        public string $name,
        public int $capacity,
    ) {
    }
}
