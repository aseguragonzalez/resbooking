<?php

declare(strict_types=1);

namespace App\Domain\Shared;

final class Turn
{
    public static Turn $twelve;
    public static Turn $twelveThirty;
    public static Turn $thirteen;
    public static Turn $thirteenThirty;
    public static Turn $fourteen;
    public static Turn $fourteenThirty;
    public static Turn $fifteen;
    public static Turn $fifteenThirty;
    public static Turn $sixteen;
    public static Turn $sixteenThirty;
    public static Turn $seventeen;
    public static Turn $seventeenThirty;
    public static Turn $eighteen;
    public static Turn $eighteenThirty;
    public static Turn $nineteen;
    public static Turn $nineteenThirty;
    public static Turn $twenty;
    public static Turn $twentyThirty;
    public static Turn $twentyOne;
    public static Turn $twentyOneThirty;
    public static Turn $twentyTwo;
    public static Turn $twentyTwoThirty;
    public static Turn $twentyThree;
    public static Turn $twentyThreeThirty;

    private function __construct(
        public readonly int $id,
        public readonly string $startTime,
        public readonly string $endTime,
    ) { }

    public static function initialize()
    {
        self::$twelve = new Turn(1, '12:00:00', '12:30:00');
        self::$twelveThirty = new Turn(2, '12:30:00', '13:00:00');
        self::$thirteen = new Turn(3, '13:00:00', '13:30:00');
        self::$thirteenThirty = new Turn(4, '13:30:00', '14:00:00');
        self::$fourteen = new Turn(5, '14:00:00', '14:30:00');
        self::$fourteenThirty = new Turn(6, '14:30:00', '15:00:00');
        self::$fifteen = new Turn(7, '15:00:00', '15:30:00');
        self::$fifteenThirty = new Turn(8, '15:30:00', '16:00:00');
        self::$sixteen = new Turn(9, '16:00:00', '16:30:00');
        self::$sixteenThirty = new Turn(10, '16:30:00', '17:00:00');
        self::$seventeen = new Turn(11, '17:00:00', '17:30:00');
        self::$seventeenThirty = new Turn(12, '17:30:00', '18:00:00');
        self::$eighteen = new Turn(13, '18:00:00', '18:30:00');
        self::$eighteenThirty = new Turn(14, '18:30:00', '19:00:00');
        self::$nineteen = new Turn(15, '19:00:00', '19:30:00');
        self::$nineteenThirty = new Turn(16, '19:30:00', '20:00:00');
        self::$twenty = new Turn(17, '20:00:00', '20:30:00');
        self::$twentyThirty = new Turn(18, '20:30:00', '21:00:00');
        self::$twentyOne = new Turn(19, '21:00:00', '21:30:00');
        self::$twentyOneThirty = new Turn(20, '21:30:00', '22:00:00');
        self::$twentyTwo = new Turn(21, '22:00:00', '22:30:00');
        self::$twentyTwoThirty = new Turn(22, '22:30:00', '23:00:00');
        self::$twentyThree = new Turn(23, '23:00:00', '23:30:00');
        self::$twentyThreeThirty = new Turn(24, '23:30:00', '00:00:00');
    }

    public static function byId(int $id): Turn
    {
        $turns = [
            self::$twelve,
            self::$twelveThirty,
            self::$thirteen,
            self::$thirteenThirty,
            self::$fourteen,
            self::$fourteenThirty,
            self::$fifteen,
            self::$fifteenThirty,
            self::$sixteen,
            self::$sixteenThirty,
            self::$seventeen,
            self::$seventeenThirty,
            self::$eighteen,
            self::$eighteenThirty,
            self::$nineteen,
            self::$nineteenThirty,
            self::$twenty,
            self::$twentyThirty,
            self::$twentyOne,
            self::$twentyOneThirty,
            self::$twentyTwo,
            self::$twentyTwoThirty,
            self::$twentyThree,
            self::$twentyThreeThirty,
        ];

        $turn = array_values(array_filter($turns, fn (Turn $turn) => $turn->id === $id))[0] ?? null;

        if ($turn === null) {
            throw new \InvalidArgumentException('Invalid turn id');
        }

        return $turn;
    }

    public static function byStartTime(string $startTime): Turn
    {
        $turns = [
            self::$twelve,
            self::$twelveThirty,
            self::$thirteen,
            self::$thirteenThirty,
            self::$fourteen,
            self::$fourteenThirty,
            self::$fifteen,
            self::$fifteenThirty,
            self::$sixteen,
            self::$sixteenThirty,
            self::$seventeen,
            self::$seventeenThirty,
            self::$eighteen,
            self::$eighteenThirty,
            self::$nineteen,
            self::$nineteenThirty,
            self::$twenty,
            self::$twentyThirty,
            self::$twentyOne,
            self::$twentyOneThirty,
            self::$twentyTwo,
            self::$twentyTwoThirty,
            self::$twentyThree,
            self::$twentyThreeThirty,
        ];

        $turn = array_values(
            array_filter($turns, fn (Turn $turn) => $turn->startTime === $startTime)
        )[0] ?? null;

        if ($turn === null) {
            throw new \InvalidArgumentException('Invalid turn start time');
        }

        return $turn;
    }
}
