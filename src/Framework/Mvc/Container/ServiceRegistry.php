<?php

declare(strict_types=1);

namespace Framework\Mvc\Container;

/**
 * Mutable service locator used by {@see \Framework\Mvc\Application} implementations.
 * Composition roots may adapt a concrete DI container (e.g. PHP-DI) behind this port.
 */
interface ServiceRegistry
{
    public function get(string $id): mixed;

    public function set(string $id, mixed $value): void;

    public function has(string $id): bool;
}
