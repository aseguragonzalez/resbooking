<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateDiningArea;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Shared\Capacity;
use Seedwork\Domain\EntityId;

final readonly class UpdateDiningAreaHandler implements UpdateDiningArea
{
    public function __construct(
        private RestaurantObtainer $restaurantObtainer,
        private RestaurantRepository $restaurantRepository,
    ) {
    }

    public function execute(UpdateDiningAreaCommand $command): void
    {
        $restaurant = $this->restaurantObtainer->obtain(id: EntityId::fromString($command->restaurantId));
        $updatedDiningArea = DiningArea::build(
            id: $command->diningAreaId->value,
            capacity: new Capacity(value: $command->capacity),
            name: $command->name
        );
        $restaurant->updateDiningArea(diningArea: $updatedDiningArea);
        $this->restaurantRepository->save($restaurant);
    }
}
