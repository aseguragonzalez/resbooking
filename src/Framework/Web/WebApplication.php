<?php

declare(strict_types=1);

namespace Framework\Web;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Base class for HTTP-facing applications.
 *
 * Unlike {@see Application} (CLI / long-running process entrypoints), web apps receive the
 * current PSR-7 request explicitly via {@see run()}.
 */
abstract class WebApplication
{
    /**
     * @param ContainerInterface $container PSR-11 container (implementation supplied by the composition root).
     * @param string $basePath The base path of the application.
     */
    protected function __construct(
        protected ContainerInterface $container,
        protected string $basePath,
    ) {
    }

    abstract public function run(ServerRequestInterface $request): int;
}
