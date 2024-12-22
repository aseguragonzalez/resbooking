<?php

declare(strict_types=1);

namespace App\Seedwork\Domain;

abstract class Entity
{
    public function __construct(private string $id) { }

    public function equals(Entity $other): bool
    {
      return $this->id === $other->getId();
    }

    public function getId(): string
    {
        return $this->id;
    }
}
