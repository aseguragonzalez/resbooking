<?php

declare(strict_types=1);

namespace Application\Restaurants\AddDiningArea;

final readonly class AddDiningAreaCommand
{
    public function __construct(
        public string $restaurantId,
        public string $name,
        public int $capacity
    ) {
    }
}
