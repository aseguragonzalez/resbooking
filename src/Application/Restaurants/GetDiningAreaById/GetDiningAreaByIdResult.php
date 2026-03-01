<?php

declare(strict_types=1);

namespace Application\Restaurants\GetDiningAreaById;

use Application\Restaurants\GetRestaurantById\DiningAreaItem;
use SeedWork\Application\QueryResult;

final readonly class GetDiningAreaByIdResult extends QueryResult
{
    public function __construct(
        public DiningAreaItem $diningArea,
    ) {
        parent::__construct();
    }
}
