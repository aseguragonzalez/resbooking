<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Middlewares;

final readonly class RestaurantContextSettings
{
    public function __construct(
        public string $cookieName = 'restaurant',
        public string $selectionPath = '/restaurants/select',
        public string $contextKey = 'restaurantId',
    ) {
    }
}
