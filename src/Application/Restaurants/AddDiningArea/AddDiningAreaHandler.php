<?php

declare(strict_types=1);

namespace Application\Restaurants\AddDiningArea;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Restaurants\ValueObjects\RestaurantId;
use Domain\Shared\Capacity;
use SeedWork\Application\Command;

final readonly class AddDiningAreaHandler implements AddDiningArea
{
    public function __construct(
        private RestaurantObtainer $restaurantObtainer,
        private RestaurantRepository $restaurantRepository,
    ) {
    }

    /**
     * @param AddDiningAreaCommand $command
     */
    public function handle(Command $command): void
    {
        $restaurantId = RestaurantId::fromString($command->restaurantId);
        $diningArea = DiningArea::create(capacity: new Capacity(value: $command->capacity), name: $command->name);
        $restaurant = $this->restaurantObtainer->obtain(id: $restaurantId)->addDiningArea(diningArea: $diningArea);
        $this->restaurantRepository->save($restaurant);
    }
}
