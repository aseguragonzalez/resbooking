<?php

declare(strict_types=1);

namespace Domain\Restaurants\Repositories;

use Domain\Restaurants\Entities\Restaurant;
use SeedWork\Domain\Repository;

/**
 * @extends Repository<Restaurant>
 */
interface RestaurantRepository extends Repository
{
    /**
     * @return array<Restaurant>
     */
    public function findByUserEmail(string $email): array;
}
