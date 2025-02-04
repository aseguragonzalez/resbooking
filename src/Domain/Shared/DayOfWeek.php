<?php

declare(strict_types=1);

namespace App\Domain\Shared;

enum DayOfWeek: int
{
    case SUNDAY = 1;
    case MONDAY = 2;
    case TUESDAY = 3;
    case WEDNESDAY = 4;
    case THURSDAY = 5;
    case FRIDAY = 6;
    case SATURDAY = 7;

    public static function getById(int $id): self
    {
        return match ($id) {
            1 => self::SUNDAY,
            2 => self::MONDAY,
            3 => self::TUESDAY,
            4 => self::WEDNESDAY,
            5 => self::THURSDAY,
            6 => self::FRIDAY,
            7 => self::SATURDAY,
            default => throw new \InvalidArgumentException("Invalid day of week id: $id"),
        };
    }

    public static function getByName(string $name): self
    {
        return match (strtolower($name)) {
            'sunday' => self::SUNDAY,
            'monday' => self::MONDAY,
            'tuesday' => self::TUESDAY,
            'wednesday' => self::WEDNESDAY,
            'thursday' => self::THURSDAY,
            'friday' => self::FRIDAY,
            'saturday' => self::SATURDAY,
            default => throw new \InvalidArgumentException("Invalid day of week name: $name"),
        };
    }
}
