<?php

declare(strict_types=1);

namespace Application\Restaurants\CreateNewRestaurant;

use Seedwork\Application\CommandHandler;

/**
 * @extends CommandHandler<CreateNewRestaurantCommand>
 */
interface CreateNewRestaurant extends CommandHandler
{
    public function execute(CreateNewRestaurantCommand $command): void;
}
