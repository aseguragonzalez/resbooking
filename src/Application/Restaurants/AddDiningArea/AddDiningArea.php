<?php

declare(strict_types=1);

namespace Application\Restaurants\AddDiningArea;

use Seedwork\Application\CommandHandler;

/**
 * @extends CommandHandler<AddDiningAreaCommand>
 */
interface AddDiningArea extends CommandHandler
{
    public function execute(AddDiningAreaCommand $command): void;
}
