<?php

declare(strict_types=1);

namespace App\Domain\Shared;

final class Turn
{
    public readonly static Turn $twelve = new Turn(1, '12:00:00', '12:30:00');
    public readonly static Turn $twelveThirty = new Turn(2, '12:30:00', '13:00:00');
    public readonly static Turn $thirteen = new Turn(3, '13:00:00', '13:30:00');
    public readonly static Turn $thirteenThirty = new Turn(4, '13:30:00', '14:00:00');
    public readonly static Turn $fourteen = new Turn(5, '14:00:00', '14:30:00');
    public readonly static Turn $fourteenThirty = new Turn(6, '14:30:00', '15:00:00');
    public readonly static Turn $fifteen = new Turn(7, '15:00:00', '15:30:00');
    public readonly static Turn $fifteenThirty = new Turn(8, '15:30:00', '16:00:00');
    public readonly static Turn $sixteen = new Turn(9, '16:00:00', '16:30:00');
    public readonly static Turn $sixteenThirty = new Turn(10, '16:30:00', '17:00:00');
    public readonly static Turn $seventeen = new Turn(11, '17:00:00', '17:30:00');
    public readonly static Turn $seventeenThirty = new Turn(12, '17:30:00', '18:00:00');
    public readonly static Turn $eighteen = new Turn(13, '18:00:00', '18:30:00');
    public readonly static Turn $eighteenThirty = new Turn(14, '18:30:00', '19:00:00');
    public readonly static Turn $nineteen = new Turn(15, '19:00:00', '19:30:00');
    public readonly static Turn $nineteenThirty = new Turn(16, '19:30:00', '20:00:00');
    public readonly static Turn $twenty = new Turn(17, '20:00:00', '20:30:00');
    public readonly static Turn $twentyThirty = new Turn(18, '20:30:00', '21:00:00');
    public readonly static Turn $twentyOne = new Turn(19, '21:00:00', '21:30:00');
    public readonly static Turn $twentyOneThirty = new Turn(20, '21:30:00', '22:00:00');
    public readonly static Turn $twentyTwo = new Turn(21, '22:00:00', '22:30:00');
    public readonly static Turn $twentyTwoThirty = new Turn(22, '22:30:00', '23:00:00');
    public readonly static Turn $twentyThree = new Turn(23, '23:00:00', '23:30:00');
    public readonly static Turn $twentyThreeThirty = new Turn(24, '23:30:00', '00:00:00');

    private function __construct(
        public readonly int $id,
        public readonly string $startTime,
        public readonly string $endTime,
    ) { }

    public function equals(Turn $turn): bool
    {
        return $this->id === $turn->id;
    }
}
