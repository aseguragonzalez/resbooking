<?php

declare(strict_types=1);

namespace App\Seedwork\Domain;

abstract class Entity
{
    public function __construct(private ?int $id = null) { }

    public function equals(Entity $other): bool
    {
      return $this->id === $other->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
