<?php

declare(strict_types=1);

namespace App\Domain\Shared;

enum Turn: int
{
    CASE H1200 = 1;
    CASE H1230 = 2;
    CASE H1300 = 3;
    CASE H1330 = 4;
    CASE H1400 = 5;
    CASE H1430 = 6;
    CASE H1500 = 7;
    CASE H1530 = 8;
    CASE H1600 = 9;
    CASE H1630 = 10;
    CASE H1700 = 11;
    CASE H1730 = 12;
    CASE H1800 = 13;
    CASE H1830 = 14;
    CASE H1900 = 15;
    CASE H1930 = 16;
    CASE H2000 = 17;
    CASE H2030 = 18;
    CASE H2100 = 19;
    CASE H2130 = 20;
    CASE H2200 = 21;
    CASE H2230 = 22;
    CASE H2300 = 23;
    CASE H2330 = 24;

    public static function getById(int $id): self
    {
        return match ($id) {
            1 => self::H1200,
            2 => self::H1230,
            3 => self::H1300,
            4 => self::H1330,
            5 => self::H1400,
            6 => self::H1430,
            7 => self::H1500,
            8 => self::H1530,
            9 => self::H1600,
            10 => self::H1630,
            11 => self::H1700,
            12 => self::H1730,
            13 => self::H1800,
            14 => self::H1830,
            15 => self::H1900,
            16 => self::H1930,
            17 => self::H2000,
            18 => self::H2030,
            19 => self::H2100,
            20 => self::H2130,
            21 => self::H2200,
            22 => self::H2230,
            23 => self::H2300,
            24 => self::H2330,
            default => throw new \InvalidArgumentException("Invalid turn id: $id"),
        };
    }

    public static function getByStartTime(string $startTime): self
    {
        return match (substr($startTime, 0, 5)) {
            '12:00' => self::H1200,
            '12:30' => self::H1230,
            '13:00' => self::H1300,
            '13:30' => self::H1330,
            '14:00' => self::H1400,
            '14:30' => self::H1430,
            '15:00' => self::H1500,
            '15:30' => self::H1530,
            '16:00' => self::H1600,
            '16:30' => self::H1630,
            '17:00' => self::H1700,
            '17:30' => self::H1730,
            '18:00' => self::H1800,
            '18:30' => self::H1830,
            '19:00' => self::H1900,
            '19:30' => self::H1930,
            '20:00' => self::H2000,
            '20:30' => self::H2030,
            '21:00' => self::H2100,
            '21:30' => self::H2130,
            '22:00' => self::H2200,
            '22:30' => self::H2230,
            '23:00' => self::H2300,
            '23:30' => self::H2330,
            default => throw new \InvalidArgumentException("Invalid turn start time: $startTime"),
        };
    }

    public function toString(): string
    {
        return match($this->value) {
            1 => '12:00:00',
            2 => '12:30:00',
            3 => '13:00:00',
            4 => '13:30:00',
            5 => '14:00:00',
            6 => '14:30:00',
            7 => '15:00:00',
            8 => '15:30:00',
            9 => '16:00:00',
            10 => '16:30:00',
            11 => '17:00:00',
            12 => '17:30:00',
            13 => '18:00:00',
            14 => '18:30:00',
            15 => '19:00:00',
            16 => '19:30:00',
            17 => '20:00:00',
            18 => '20:30:00',
            19 => '21:00:00',
            20 => '21:30:00',
            21 => '22:00:00',
            22 => '22:30:00',
            23 => '23:00:00',
            24 => '23:30:00',
        };
    }
}
