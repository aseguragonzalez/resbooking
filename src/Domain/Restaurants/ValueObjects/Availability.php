<?php

declare(strict_types=1);

namespace Domain\Restaurants\ValueObjects;

use Domain\Shared\Capacity;
use Domain\Shared\DayOfWeek;
use Domain\Shared\TimeSlot;
use Seedwork\Domain\ValueObject;

final readonly class Availability extends ValueObject
{
    public function __construct(
        public Capacity $capacity,
        public DayOfWeek $dayOfWeek,
        public TimeSlot $timeSlot,
    ) {
    }

    public function equals(ValueObject $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }
        /** @var self $other */
        return $this->dayOfWeek === $other->dayOfWeek
            && $this->timeSlot === $other->timeSlot
            && $this->capacity->equals($other->capacity);
    }
}
