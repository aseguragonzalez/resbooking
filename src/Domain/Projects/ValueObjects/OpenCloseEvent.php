<?php

declare(strict_types=1);

namespace Domain\Projects\ValueObjects;

use Domain\Shared\Turn;
use Seedwork\Domain\ValueObject;

final class OpenCloseEvent extends ValueObject
{
    public function __construct(
        public readonly \DateTimeImmutable $date,
        public readonly bool $isAvailable,
        public readonly Turn $turn,
    ) {
    }

    public function equals(OpenCloseEvent $other): bool
    {
        return $this->turn === $other->turn
            && $this->date->format('Y-m-d') === $other->date->format('Y-m-d');
    }
}
