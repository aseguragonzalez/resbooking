<?php

declare(strict_types=1);

namespace Application\Restaurants\GetRestaurantById;

use Domain\Restaurants\Entities\Restaurant;
use Seedwork\Application\QueryHandler;

/**
 * @extends QueryHandler<GetRestaurantByIdQuery, Restaurant>
 */
interface GetRestaurantById extends QueryHandler
{
    public function execute(GetRestaurantByIdQuery $query): Restaurant;
}
