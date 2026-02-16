<?php

declare(strict_types=1);

namespace Application\Restaurants\RemoveDiningArea;

use Seedwork\Application\CommandHandler;

/**
 * @extends CommandHandler<RemoveDiningAreaCommand>
 */
interface RemoveDiningArea extends CommandHandler
{
    public function execute(RemoveDiningAreaCommand $command): void;
}
