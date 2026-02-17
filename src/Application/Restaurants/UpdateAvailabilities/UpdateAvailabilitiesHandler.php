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

final readonly class UpdateAvailabilitiesHandler implements UpdateAvailabilities
{
    public function __construct(
        private RestaurantObtainer $restaurantObtainer,
        private RestaurantRepository $restaurantRepository,
    ) {
    }

    public function execute(UpdateAvailabilitiesCommand $command): void
    {
        $restaurant = $this->restaurantObtainer->obtain(id: EntityId::fromString($command->restaurantId));

        /** @var array<Availability> */
        $availabilities = array_map(
            fn ($availabilityData) => new Availability(
                capacity: new Capacity($availabilityData->capacity),
                dayOfWeek: DayOfWeek::getById($availabilityData->dayOfWeekId),
                timeSlot: TimeSlot::getById($availabilityData->timeSlotId),
            ),
            $command->availabilities
        );

        $restaurant->updateAvailabilities($availabilities);
        $this->restaurantRepository->save($restaurant);
    }
}
