<?php

declare(strict_types=1);

namespace Application\Restaurants\GetDiningAreaById;

use SeedWork\Application\Query;

final readonly class GetDiningAreaByIdQuery extends Query
{
    public function __construct(
        public string $restaurantId,
        public string $diningAreaId,
    ) {
        parent::__construct();
    }
}
