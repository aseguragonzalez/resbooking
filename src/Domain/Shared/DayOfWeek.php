<?php

declare(strict_types=1);

namespace App\Domain\Shared;

final class DayOfWeek
{
    public static DayOfWeek $sunday;
    public static DayOfWeek $monday;
    public static DayOfWeek $tuesday;
    public static DayOfWeek $wednesday;
    public static DayOfWeek $thursday;
    public static DayOfWeek $friday;
    public static DayOfWeek $saturday;

    private function __construct(
        public readonly int $id,
        public readonly string $name,
    ) { }

    public static function initialize(): void
    {
        self::$sunday = new self(1, 'sunday');
        self::$monday = new self(2, 'monday');
        self::$tuesday = new self(3, 'tuesday');
        self::$wednesday = new self(4, 'wednesday');
        self::$thursday = new self(5, 'thursday');
        self::$friday = new self(6, 'friday');
        self::$saturday = new self(7, 'saturday');
    }

    public static function byId(int $id): DayOfWeek
    {
        if ($id < 1 || $id > 7) {
            throw new \InvalidArgumentException("Invalid day of week id: $id");
        }

        return match ($id) {
            self::$sunday->id => self::$sunday,
            self::$monday->id => self::$monday,
            self::$tuesday->id => self::$tuesday,
            self::$wednesday->id => self::$wednesday,
            self::$thursday->id => self::$thursday,
            self::$friday->id => self::$friday,
            self::$saturday->id => self::$saturday,
        };
    }

    public static function byName(string $name): DayOfWeek
    {
        return match ($name) {
            self::$sunday->name => self::$sunday,
            self::$monday->name => self::$monday,
            self::$tuesday->name => self::$tuesday,
            self::$wednesday->name => self::$wednesday,
            self::$thursday->name => self::$thursday,
            self::$friday->name => self::$friday,
            self::$saturday->name => self::$saturday,
            default => throw new \InvalidArgumentException("Invalid day of week name: $name"),
        };
    }
}
