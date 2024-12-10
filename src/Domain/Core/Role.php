<?php

declare(strict_types=1);

namespace App\Domain\Core;

final class Role
{
    private function __construct(
        public ?int $id,
        public string $description,
        public string $name,
    ) { }

    public static function new(string $description, string $name): self {
        return new self(NULL, $description, $name);
    }

    public static function stored(int $id, string $description, string $name): self
    {
        return new self($id, $description, $name);
    }
}
