<?php

declare(strict_types=1);

namespace Application\Restaurants\GetRestaurantById;

use Domain\Restaurants\Entities\Restaurant;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Restaurants\ValueObjects\RestaurantId;
use SeedWork\Application\Query;
use SeedWork\Application\QueryResult;

final readonly class GetRestaurantByIdHandler implements GetRestaurantById
{
    public function __construct(private RestaurantObtainer $restaurantObtainer)
    {
    }

    /**
     * @param GetRestaurantByIdQuery $query
     * @return GetRestaurantByIdResult
     */
    public function handle(Query $query): QueryResult
    {
        $restaurant = $this->restaurantObtainer->obtain(RestaurantId::fromString($query->id));

        return $this->mapToResult($restaurant);
    }

    private function mapToResult(Restaurant $restaurant): GetRestaurantByIdResult
    {
        $settings = $restaurant->settings;
        $diningAreas = array_map(
            fn ($da) => new DiningAreaItem(
                id: $da->id->value,
                name: $da->name,
                capacity: $da->capacity->value,
            ),
            $restaurant->getDiningAreas()
        );
        $availabilities = array_map(
            fn ($a) => new AvailabilityItem(
                time: substr($a->timeSlot->toString(), 0, 5),
                dayOfWeekId: $a->dayOfWeek->value,
                timeSlotId: $a->timeSlot->value,
                capacity: $a->capacity->value,
            ),
            $restaurant->getAvailabilities()
        );

        return new GetRestaurantByIdResult(
            id: $restaurant->id->value,
            email: $settings->email->value,
            hasReminders: $settings->hasReminders,
            name: $settings->name,
            maxNumberOfDiners: $settings->maxNumberOfDiners->value,
            minNumberOfDiners: $settings->minNumberOfDiners->value,
            numberOfTables: $settings->numberOfTables->value,
            phone: $settings->phone->value,
            diningAreas: $diningAreas,
            availabilities: $availabilities,
        );
    }
}
