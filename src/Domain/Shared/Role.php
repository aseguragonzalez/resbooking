<?php

declare(strict_types=1);

namespace App\Domain\Shared;

final class Role
{
    public static Role $admin;
    public static Role $user;

    private function __construct(
        public readonly int $id,
        public readonly string $name,
    ) { }

    public static function initialize(): void
    {
        self::$admin = new self(1, 'admin');
        self::$user = new self(2, 'host');
    }

    public static function byId(int $id): Role
    {
        if ($id < 1 || $id > 2) {
            throw new \InvalidArgumentException("Invalid role id: $id");
        }

        return match ($id) {
            self::$admin->id => self::$admin,
            self::$user->id => self::$user,
        };
    }

    public static function byName(string $name): Role
    {
        return match ($name) {
            self::$admin->name => self::$admin,
            self::$user->name => self::$user,
            default => throw new \InvalidArgumentException("Invalid role name: $name"),
        };
    }
}
