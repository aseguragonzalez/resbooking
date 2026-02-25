<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateAvailabilities;

use Domain\Restaurants\Repositories\RestaurantRepository;
use Domain\Restaurants\Services\RestaurantObtainer;
use Domain\Restaurants\ValueObjects\Availability;
use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\TimeSlot;
use Seedwork\Domain\EntityId;
use SeedWork\Application\Command;

final readonly class UpdateAvailabilitiesHandler implements UpdateAvailabilities
{
    public function __construct(
        private RestaurantObtainer $restaurantObtainer,
        private RestaurantRepository $restaurantRepository,
    ) {
    }

    /**
     * @param UpdateAvailabilitiesCommand $command
     */
    public function handle(Command $command): void
    {
        $restaurant = $this->restaurantObtainer->obtain(id: EntityId::fromString($command->restaurantId));

        /** @var array<Availability> */
        $availabilities = array_map(
            fn (array $a) => new Availability(
                capacity: new Capacity($a['capacity']),
                dayOfWeek: DayOfWeek::getById($a['dayOfWeekId']),
                timeSlot: TimeSlot::getById($a['timeSlotId']),
            ),
            $command->availabilities
        );

        $restaurant->updateAvailabilities($availabilities);
        $this->restaurantRepository->save($restaurant);
    }
}
