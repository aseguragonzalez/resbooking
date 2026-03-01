<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateDiningArea;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Restaurants\ValueObjects\RestaurantId;
use Domain\Shared\Capacity;
use SeedWork\Application\Command;

final readonly class UpdateDiningAreaHandler implements UpdateDiningArea
{
    public function __construct(
        private RestaurantObtainer $restaurantObtainer,
        private RestaurantRepository $restaurantRepository,
    ) {
    }

    /**
     * @param UpdateDiningAreaCommand $command
     */
    public function handle(Command $command): void
    {
        $updatedDiningArea = DiningArea::build(
            id: $command->diningAreaId,
            capacity: new Capacity(value: $command->capacity),
            name: $command->name
        );
        $restaurantId = RestaurantId::fromString($command->restaurantId);
        $restaurant = $this->restaurantObtainer
            ->obtain(id: $restaurantId)
            ->updateDiningArea(diningArea: $updatedDiningArea);
        $this->restaurantRepository->save($restaurant);
    }
}
