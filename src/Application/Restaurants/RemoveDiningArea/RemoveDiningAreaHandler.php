<?php

declare(strict_types=1);

namespace Application\Restaurants\RemoveDiningArea;

use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Seedwork\Domain\EntityId;
use SeedWork\Application\Command;

final readonly class RemoveDiningAreaHandler implements RemoveDiningArea
{
    public function __construct(
        private RestaurantObtainer $restaurantObtainer,
        private RestaurantRepository $restaurantRepository,
    ) {
    }

    /**
     * @param RemoveDiningAreaCommand $command
     */
    public function handle(Command $command): void
    {
        $restaurant = $this->restaurantObtainer->obtain(id: EntityId::fromString($command->restaurantId));
        $restaurant->removeDiningAreasById(diningAreaId: EntityId::fromString($command->diningAreaId));
        $this->restaurantRepository->save($restaurant);
    }
}
