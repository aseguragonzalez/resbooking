<?php

declare(strict_types=1);

namespace Seedwork\Application;

use Seedwork\Application\Command;

/**
 * @phpstan-template T of Command
 * @template T of Command
 */
abstract class ApplicationService
{
    /**
     * @param T $request
     */
    abstract public function execute($request): void;
}
