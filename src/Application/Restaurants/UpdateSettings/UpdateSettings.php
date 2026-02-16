<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateSettings;

use Seedwork\Application\CommandHandler;

/**
 * @extends CommandHandler<UpdateSettingsCommand>
 */
interface UpdateSettings extends CommandHandler
{
    public function execute(UpdateSettingsCommand $command): void;
}
