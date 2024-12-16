<?php

declare(strict_types=1);

namespace App\Domain\Shared;

enum Role: int
{
    case ADMIN = 1;
    case USER = 2;

    public static function getById(int $id): Self
    {
        return match ($id) {
            1 => self::ADMIN,
            2 => self::USER,
            default => throw new \InvalidArgumentException("Invalid role id: $id"),
        };
    }

    public static function getByName(string $name): Self
    {
        return match (strtolower($name)) {
            'admin' => self::ADMIN,
            'user' => self::USER,
            default => throw new \InvalidArgumentException("Invalid role name: $name"),
        };
    }
}
