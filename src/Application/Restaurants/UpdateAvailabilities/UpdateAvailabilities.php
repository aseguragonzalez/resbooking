<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateAvailabilities;

use Seedwork\Application\CommandHandler;

/**
 * @extends CommandHandler<UpdateAvailabilitiesCommand>
 */
interface UpdateAvailabilities extends CommandHandler
{
    public function execute(UpdateAvailabilitiesCommand $command): void;
}
