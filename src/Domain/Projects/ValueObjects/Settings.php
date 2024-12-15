<?php

declare(strict_types=1);

namespace App\Domain\Projects\ValueObjects;

use DateTimeImmutable;
use App\Seedwork\Domain\ValueObject;

final class Settings extends ValueObject
{
    public function __construct(
        public readonly int $diners,
        public readonly int $maxDiners,
        public readonly int $minDiners,
        public readonly bool $enableRemainders,
        public int $timespan,
        public int $timeFilter,
    ) { }
}
