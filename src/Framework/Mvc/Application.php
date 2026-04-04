<?php

declare(strict_types=1);

namespace Framework\Mvc;

use Framework\Mvc\Container\ServiceRegistry;

/**
 * The base class for all applications.
 *
 * The container must be configured by the composition root (e.g. a bootstrap invoked from index.php)
 * before constructing the application. Implementations of {@see run()} expect required services to
 * already be registered.
 */
abstract class Application
{
    /**
     * Create a new application instance.
     * @param ServiceRegistry $container Service registry (often a PHP-DI adapter from the composition root).
     * @param string $basePath The base path of the application.
     */
    protected function __construct(
        protected ServiceRegistry $container,
        protected string $basePath,
    ) {
    }

    /**
     * Run the application with the given arguments.
     * @param int|null $argc The number of arguments passed to the application. Default is null.
     * @param array<string> $argv The arguments to pass to the application. Default is an empty array.
     * @return int The exit code of the application.
     */
    abstract public function run(?int $argc = null, array $argv = []): int;
}
