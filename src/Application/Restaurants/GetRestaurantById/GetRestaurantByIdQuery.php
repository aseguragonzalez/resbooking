<?php

declare(strict_types=1);

namespace Application\Restaurants\GetRestaurantById;

final readonly class GetRestaurantByIdQuery
{
    public function __construct(public string $id)
    {
    }
}
