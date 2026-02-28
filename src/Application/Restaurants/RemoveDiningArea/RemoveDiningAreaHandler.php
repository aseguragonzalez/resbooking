<?php

declare(strict_types=1);

namespace Application\Restaurants\RemoveDiningArea;

use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Restaurants\ValueObjects\DiningAreaId;
use Domain\Restaurants\ValueObjects\RestaurantId;
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
        $restaurantId = RestaurantId::fromString($command->restaurantId);
        $diningAreaId = DiningAreaId::fromString($command->diningAreaId);
        $restaurant = $this->restaurantObtainer
            ->obtain(id: $restaurantId)
            ->removeDiningAreasById(diningAreaId: $diningAreaId);
        $this->restaurantRepository->save($restaurant);
    }
}
