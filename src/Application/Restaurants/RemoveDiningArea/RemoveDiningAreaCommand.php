<?php

declare(strict_types=1);

namespace Application\Restaurants\RemoveDiningArea;

final readonly class RemoveDiningAreaCommand
{
    public function __construct(
        public string $restaurantId,
        public string $diningAreaId
    ) {
    }
}
