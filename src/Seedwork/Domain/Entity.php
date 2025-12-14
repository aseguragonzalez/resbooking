<?php

declare(strict_types=1);

namespace Seedwork\Domain;

abstract readonly class Entity
{
    protected function __construct(public string $id)
    {
    }

    public function equals(Entity $other): bool
    {
        return $this->id === $other->id;
    }
}
