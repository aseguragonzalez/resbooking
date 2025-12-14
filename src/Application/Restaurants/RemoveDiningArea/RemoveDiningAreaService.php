<?php

declare(strict_types=1);

namespace Application\Restaurants\RemoveDiningArea;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;

final readonly class RemoveDiningAreaService implements RemoveDiningArea
{
    public function __construct(
        private RestaurantObtainer $restaurantObtainer,
        private RestaurantRepository $restaurantRepository,
    ) {
    }

    public function execute(RemoveDiningAreaCommand $command): void
    {
        $restaurant = $this->restaurantObtainer->obtain(id: $command->restaurantId);
        $restaurant->removeDiningAreasById(diningAreaId: $command->diningAreaId);
        $this->restaurantRepository->save($restaurant);
    }
}
