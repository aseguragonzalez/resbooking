<?php

declare(strict_types=1);

namespace Domain\Restaurants\Services;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Repositories\RestaurantRepository;
use SeedWork\Domain\AggregateObtainer;

/**
 * @extends AggregateObtainer<Restaurant>
 */
final readonly class RestaurantObtainer extends AggregateObtainer
{
    public function __construct(RestaurantRepository $repository)
    {
        parent::__construct($repository, 'Restaurant');
    }
}
