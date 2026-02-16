<?php

declare(strict_types=1);

namespace Application\Restaurants\UpdateDiningArea;

use Seedwork\Application\CommandHandler;

/**
 * @extends CommandHandler<UpdateDiningAreaCommand>
 */
interface UpdateDiningArea extends CommandHandler
{
    public function execute(UpdateDiningAreaCommand $command): void;
}
