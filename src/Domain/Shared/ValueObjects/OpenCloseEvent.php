<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use App\Domain\Shared\Turn;
use App\Seedwork\Domain\ValueObject;

final class OpenCloseEvent extends ValueObject
{
    public function __construct(
        public readonly \DateTimeImmutable $date,
        public readonly bool $isAvailable,
        public readonly Turn $turn,
    ) { }

    public function equals(OpenCloseEvent $other): bool
    {
        return $this->turn === $other->turn
            && $this->date->format('Y-m-d') === $other->date->format('Y-m-d');
    }
}
