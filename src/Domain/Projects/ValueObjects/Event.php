<?php

declare(strict_types=1);

namespace App\Domain\Projects;

use DateTimeInmutable;

use App\Domain\Shared\Turn;
use App\Seedwork\Domain\ValueObject;

final class Event extends ValueObject
{
    public function __construct(
        public readonly DateTimeInmutable $date,
        public readonly Turn $turn,
        public readonly bool $available = false,
    ) { }

    public function equals(Event $event): bool
    {
        return $this->turn->equals($event->turn)
            && $this->date->format('Y-m-d') === $event->date->format('Y-m-d');
    }
}
