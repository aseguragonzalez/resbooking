<?php

declare(strict_types=1);

namespace Application\Restaurants\GetRestaurantById;

use SeedWork\Application\Query;

final readonly class GetRestaurantByIdQuery extends Query
{
    public function __construct(public string $id)
    {
        parent::__construct();
    }
}
