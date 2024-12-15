<?php

declare(strict_types=1);

namespace App\Domain\Shared;

final class DayOfWeek
{
    public readonly static DayOfWeek $sunday = new DayOfWeek(1, 'sunday');
    public readonly static DayOfWeek $monday = new DayOfWeek(2, 'monday');
    public readonly static DayOfWeek $tuesday = new DayOfWeek(3, 'tuesday');
    public readonly static DayOfWeek $wednesday = new DayOfWeek(4, 'wednesday');
    public readonly static DayOfWeek $thursday = new DayOfWeek(5, 'thursday');
    public readonly static DayOfWeek $friday = new DayOfWeek(6, 'friday');
    public readonly static DayOfWeek $saturday = new DayOfWeek(7, 'saturday');

    private function __construct(
        public readonly int $id,
        public readonly string $name,
    ) { }

    public function equals(DayOfWeek $dayOfWeek): bool
    {
        return $this->id === $dayOfWeek->id;
    }
}
