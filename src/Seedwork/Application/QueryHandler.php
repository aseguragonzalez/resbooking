<?php

declare(strict_types=1);

namespace Seedwork\Application;

/**
 * Marker interface for read-only application services (query handlers).
 * Implementations handle a query and return a result.
 *
 * @template TQuery of object
 * @template TResult
 */
interface QueryHandler
{
}
