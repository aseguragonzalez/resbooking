<?php

declare(strict_types=1);

namespace Seedwork\Application;

/**
 * Marker interface for write-only application services (command handlers).
 * Implementations handle a command and return void.
 *
 * @template TCommand of object
 */
interface CommandHandler
{
}
