<?php

declare(strict_types=1);

namespace Framework\Container;

use Psr\Container\ContainerInterface;

/**
 * Mutable container port for framework {@see Dependencies} classes (composition root registers services).
 * Extends PSR-11 for {@see ContainerInterface::get} / {@see ContainerInterface::has}; adds {@see set}.
 */
interface MutableContainer extends ContainerInterface
{
    public function set(string $id, mixed $value): void;
}
