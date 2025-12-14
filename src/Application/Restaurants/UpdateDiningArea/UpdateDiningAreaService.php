<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateDiningArea;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Shared\Capacity;

final readonly class UpdateDiningAreaService implements UpdateDiningArea
{
    public function __construct(
        private RestaurantObtainer $restaurantObtainer,
        private RestaurantRepository $restaurantRepository,
    ) {
    }

    public function execute(UpdateDiningAreaCommand $command): void
    {
        $restaurant = $this->restaurantObtainer->obtain(id: $command->restaurantId);

        $restaurant->removeDiningAreas(fn (DiningArea $diningArea) => $diningArea->getId() === $command->diningAreaId);

        $updatedDiningArea = DiningArea::build(
            id: $command->diningAreaId,
            capacity: new Capacity(value: $command->capacity),
            name: $command->name
        );

        $restaurant->addDiningArea(diningArea: $updatedDiningArea);
        $this->restaurantRepository->save($restaurant);
    }
}
