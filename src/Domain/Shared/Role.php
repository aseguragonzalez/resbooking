<?php

declare(strict_types=1);

namespace App\Domain\Shared;

enum Role: int
{
    case Admin = 1;
    case User = 2;

    public static function getById(int $id): self
    {
        return match ($id) {
            1 => self::Admin,
            2 => self::User,
            default => throw new \InvalidArgumentException("Invalid role id: $id"),
        };
    }

    public static function getByName(string $name): self
    {
        return match (strtolower($name)) {
            'admin' => self::Admin,
            'user' => self::User,
            default => throw new \InvalidArgumentException("Invalid role name: $name"),
        };
    }
}
