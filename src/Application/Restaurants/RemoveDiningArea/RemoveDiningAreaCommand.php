<?php

declare(strict_types=1);

namespace Application\Restaurants\RemoveDiningArea;

use Seedwork\Domain\EntityId;

final readonly class RemoveDiningAreaCommand
{
    public function __construct(
        public EntityId $restaurantId,
        public EntityId $diningAreaId
    ) {
    }
}
