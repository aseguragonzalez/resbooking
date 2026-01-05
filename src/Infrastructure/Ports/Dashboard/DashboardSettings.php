<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard;

use Seedwork\Infrastructure\Mvc\Settings;

final class DashboardSettings extends Settings
{
    public function __construct(
        string $basePath,
        public readonly string $restaurantCookieName = 'restaurant',
        public readonly string $restaurantSelectionUrl = '/restaurants/select',
        public readonly string $restaurantIdContextKey = 'restaurantId',
    ) {
        parent::__construct(basePath: $basePath);
    }
}
