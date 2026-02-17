<?php

declare(strict_types=1);

namespace Application\Restaurants\GetRestaurantById;

use Seedwork\Domain\EntityId;

final readonly class GetRestaurantByIdQuery
{
    public function __construct(public EntityId $id)
    {
    }
}
