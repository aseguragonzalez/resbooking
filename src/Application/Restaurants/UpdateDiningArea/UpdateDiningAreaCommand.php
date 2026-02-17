<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateDiningArea;

use Seedwork\Domain\EntityId;

final readonly class UpdateDiningAreaCommand
{
    public function __construct(
        public EntityId $restaurantId,
        public EntityId $diningAreaId,
        public string $name,
        public int $capacity
    ) {
    }
}
