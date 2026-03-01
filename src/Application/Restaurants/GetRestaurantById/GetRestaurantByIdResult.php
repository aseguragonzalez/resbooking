<?php

declare(strict_types=1);

namespace Application\Restaurants\GetRestaurantById;

use SeedWork\Application\QueryResult;

final readonly class GetRestaurantByIdResult extends QueryResult
{
    /**
     * @param array<DiningAreaItem> $diningAreas
     * @param array<AvailabilityItem> $availabilities
     */
    public function __construct(
        public string $id,
        public string $email,
        public bool $hasReminders,
        public string $name,
        public int $maxNumberOfDiners,
        public int $minNumberOfDiners,
        public int $numberOfTables,
        public string $phone,
        /** @var array<DiningAreaItem> */
        public array $diningAreas,
        /** @var array<AvailabilityItem> */
        public array $availabilities,
    ) {
        parent::__construct();
    }
}
