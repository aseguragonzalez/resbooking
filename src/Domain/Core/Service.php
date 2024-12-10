<?php

declare(strict_types=1);

namespace App\Domain\Core;

final class Service
{
    private function __construct(
        public ?int $id,
        public string $description,
        public string $name,
        public string $path,
        public string $platform,
        private array $roles = [],
    ) { }

    public static function new(
        string $description,
        string $name,
        string $path,
        string $platform,
    ): self {
        return new self(NULL, $description, $name, $path, $platform);
    }

    public static function stored(
        int $id,
        string $description,
        string $name,
        string $path,
        string $platform,
        array $roles = [],
    ): self
    {
        return new self($id, $description, $name, $path, $platform, $roles);
    }

    public function addRole(Role $role): void
    {
        $this->roles[] = $role;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}
