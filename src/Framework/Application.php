<?php

declare(strict_types=1);

namespace Framework;

use DI\Container;

/**
 * The base class for all applications.
 */
abstract class Application
{
    /**
     * Create a new application instance.
     * @param Container $container The container instance.
     * @param string $basePath The base path of the application.
     */
    protected function __construct(
        protected Container $container,
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

    /**
     * Configure the dependencies of the application.
     * @return void
     */
    abstract protected function configureDependencies(): void;

    /**
     * Configure the settings of the application.
     * @return void
     */
    abstract protected function configureSettings(): void;

    /**
     * Configure the logging of the application.
     * @return void
     */
    abstract protected function configureLogging(): void;
}
