<?php

declare(strict_types=1);

namespace Framework\Mvc\Container;

use DI\Container;

/**
 * Adapts PHP-DI's {@see Container} to {@see ServiceRegistry} for application entrypoints.
 */
final readonly class PhpDiServiceRegistry implements ServiceRegistry
{
    public function __construct(private Container $container)
    {
    }

    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    public function set(string $id, mixed $value): void
    {
        $this->container->set($id, $value);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }
}
