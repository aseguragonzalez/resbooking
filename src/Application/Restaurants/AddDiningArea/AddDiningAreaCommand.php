<?php

declare(strict_types=1);

namespace Application\Restaurants\AddDiningArea;

use SeedWork\Application\Command;

final readonly class AddDiningAreaCommand extends Command
{
    public function __construct(
        public string $restaurantId,
        public string $name,
        public int $capacity
    ) {
        parent::__construct();
    }
}
