<?php

declare(strict_types=1);

namespace Application\Restaurants\GetRestaurantById;

use SeedWork\Application\QueryHandler;

/**
 * @extends QueryHandler<GetRestaurantByIdQuery, GetRestaurantByIdResult>
 */
interface GetRestaurantById extends QueryHandler
{
}
