<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateDiningArea;

use SeedWork\Application\Command;

final readonly class UpdateDiningAreaCommand extends Command
{
    public function __construct(
        public string $restaurantId,
        public string $diningAreaId,
        public string $name,
        public int $capacity
    ) {
        parent::__construct();
    }
}
