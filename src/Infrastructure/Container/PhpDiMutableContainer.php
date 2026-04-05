<?php

declare(strict_types=1);

namespace Infrastructure\Container;

use DI\Container;
use Framework\Container\MutableContainer;

/**
 * Adapts PHP-DI's {@see Container} to {@see MutableContainer} for application entrypoints.
 */
final readonly class PhpDiMutableContainer implements MutableContainer
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
