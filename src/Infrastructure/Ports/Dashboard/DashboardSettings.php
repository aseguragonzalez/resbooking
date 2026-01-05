<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

final class DashboardSettings
{
    public function __construct(
        public readonly string $restaurantCookieName = 'restaurant',
        public readonly string $restaurantSelectionUrl = '/restaurants/select',
        public readonly string $restaurantIdContextKey = 'restaurantId',
    ) {
    }
}
