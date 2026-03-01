<?php

declare(strict_types=1);

namespace Application\Restaurants\GetDiningAreaById;

use Application\Restaurants\GetRestaurantById\DiningAreaItem;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Restaurants\ValueObjects\DiningAreaId;
use Domain\Restaurants\ValueObjects\RestaurantId;
use SeedWork\Application\Query;
use SeedWork\Application\QueryResult;

final readonly class GetDiningAreaByIdHandler implements GetDiningAreaById
{
    public function __construct(private RestaurantObtainer $restaurantObtainer)
    {
    }

    /**
     * @param GetDiningAreaByIdQuery $query
     * @return GetDiningAreaByIdResult
     */
    public function handle(Query $query): QueryResult
    {
        $restaurant = $this->restaurantObtainer->obtain(RestaurantId::fromString($query->restaurantId));
        $diningArea = $restaurant->getDiningAreaById(DiningAreaId::fromString($query->diningAreaId));

        $item = new DiningAreaItem(
            id: $diningArea->id->value,
            name: $diningArea->name,
            capacity: $diningArea->capacity->value,
        );

        return new GetDiningAreaByIdResult(diningArea: $item);
    }
}
