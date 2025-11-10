<?php

declare(strict_types=1);

namespace App\Domain\Shared;

enum DayOfWeek: int
{
    case Sunday = 1;
    case Monday = 2;
    case Tuesday = 3;
    case Wednesday = 4;
    case Thursday = 5;
    case Friday = 6;
    case Saturday = 7;

    public static function getById(int $id): self
    {
        return match ($id) {
            1 => self::Sunday,
            2 => self::Monday,
            3 => self::Tuesday,
            4 => self::Wednesday,
            5 => self::Thursday,
            6 => self::Friday,
            7 => self::Saturday,
            default => throw new \InvalidArgumentException("Invalid day of week id: $id"),
        };
    }

    public static function getByName(string $name): self
    {
        return match (strtolower($name)) {
            'sunday' => self::Sunday,
            'monday' => self::Monday,
            'tuesday' => self::Tuesday,
            'wednesday' => self::Wednesday,
            'thursday' => self::Thursday,
            'friday' => self::Friday,
            'saturday' => self::Saturday,
            default => throw new \InvalidArgumentException("Invalid day of week name: $name"),
        };
    }

    /**
     * @return array<DayOfWeek>
     */
    public static function all(): array
    {
        return self::cases();
    }
}
