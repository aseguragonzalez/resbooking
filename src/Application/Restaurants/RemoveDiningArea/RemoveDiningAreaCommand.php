<?php

declare(strict_types=1);

namespace Application\Restaurants\RemoveDiningArea;

use SeedWork\Application\Command;

final readonly class RemoveDiningAreaCommand extends Command
{
    public function __construct(
        public string $restaurantId,
        public string $diningAreaId
    ) {
        parent::__construct();
    }
}
