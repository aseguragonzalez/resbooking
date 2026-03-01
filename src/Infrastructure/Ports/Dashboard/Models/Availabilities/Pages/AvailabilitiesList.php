<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Availabilities\Pages;

use Application\Restaurants\GetRestaurantById\AvailabilityItem;
use Domain\Restaurants\ValueObjects\Availability;
use Infrastructure\Ports\Dashboard\Models\Availabilities\Availability as AvailabilityModel;
use Infrastructure\Ports\Dashboard\Models\PageModel;

final readonly class AvailabilitiesList extends PageModel
{
    /**
     * @param array<AvailabilityModel> $availabilities
     */
    private function __construct(public array $availabilities)
    {
        parent::__construct(pageTitle: '{{availabilities.title}}');
    }

    /**
     * @param array<Availability> $availabilities
     */
    public static function create(array $availabilities = []): AvailabilitiesList
    {
        $availabilityModels = array_map(
            fn (Availability $availability) => new AvailabilityModel(
                time: substr($availability->timeSlot->toString(), 0, 5),
                dayOfWeekId: $availability->dayOfWeek->value,
                timeSlotId: $availability->timeSlot->value,
                capacity: $availability->capacity->value,
            ),
            $availabilities
        );

        usort($availabilityModels, function (AvailabilityModel $a, AvailabilityModel $b): int {
            $timeSlotComparison = $a->timeSlotId <=> $b->timeSlotId;
            if ($timeSlotComparison !== 0) {
                return $timeSlotComparison;
            }
            return $a->dayOfWeekId <=> $b->dayOfWeekId;
        });

        return new AvailabilitiesList(availabilities: $availabilityModels);
    }

    /**
     * @param array<AvailabilityItem> $availabilities
     */
    public static function createFromResultAvailabilities(array $availabilities): self
    {
        $availabilityModels = array_map(
            fn (AvailabilityItem $a) => new AvailabilityModel(
                time: $a->time,
                dayOfWeekId: $a->dayOfWeekId,
                timeSlotId: $a->timeSlotId,
                capacity: $a->capacity,
            ),
            $availabilities
        );
        usort($availabilityModels, function (AvailabilityModel $a, AvailabilityModel $b): int {
            $timeSlotComparison = $a->timeSlotId <=> $b->timeSlotId;
            if ($timeSlotComparison !== 0) {
                return $timeSlotComparison;
            }
            return $a->dayOfWeekId <=> $b->dayOfWeekId;
        });
        return new AvailabilitiesList(availabilities: $availabilityModels);
    }
}
