<?php

declare(strict_types=1);

namespace Application\Restaurants\AddDiningArea;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Shared\Capacity;

final readonly class AddDiningAreaHandler implements AddDiningArea
{
    public function __construct(
        private RestaurantObtainer $restaurantObtainer,
        private RestaurantRepository $restaurantRepository,
    ) {
    }

    public function execute(AddDiningAreaCommand $command): void
    {
        $restaurant = $this->restaurantObtainer->obtain(id: $command->restaurantId);
        $diningArea = DiningArea::new(capacity: new Capacity(value: $command->capacity), name: $command->name);
        $restaurant->addDiningArea(diningArea: $diningArea);
        $this->restaurantRepository->save($restaurant);
    }
}
