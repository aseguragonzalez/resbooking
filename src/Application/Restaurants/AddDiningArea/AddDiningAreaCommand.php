<?php

declare(strict_types=1);

namespace Application\Restaurants\AddDiningArea;

use Seedwork\Domain\EntityId;

final readonly class AddDiningAreaCommand
{
    public function __construct(
        public EntityId $restaurantId,
        public string $name,
        public int $capacity
    ) {
    }
}
