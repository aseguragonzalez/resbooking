<?php

declare(strict_types=1);

namespace App\Domain\Projects;

use App\Seedwork\Domain\ValueObject;
use App\Domain\Shared\{DayOfWeek, Turn};

final class TurnConfig extends ValueObject
{
    public function __construct(
        public readonly Turn $turn,
        public readonly DayOfWeek $dayOfWeek,
        public readonly int $capacity,
    ) { }

    public function equals(TurnConfig $turnConfig): bool
    {
        return $this->turn->equals($turnConfig->turn) &&
            $this->dayOfWeek->equals($turnConfig->dayOfWeek);
    }
}
