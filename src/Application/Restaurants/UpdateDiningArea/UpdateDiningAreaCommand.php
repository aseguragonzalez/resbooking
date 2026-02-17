<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateDiningArea;

final readonly class UpdateDiningAreaCommand
{
    public function __construct(
        public string $restaurantId,
        public string $diningAreaId,
        public string $name,
        public int $capacity
    ) {
    }
}
