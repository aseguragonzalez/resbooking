<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Reservations\Pages;

use Infrastructure\Ports\Dashboard\Models\PageModel;
use Infrastructure\Ports\Dashboard\Models\Reservations\Reservation;

final readonly class Reservations extends PageModel
{
    public bool $hasReservations;
    public bool $prevDisabled;

    /**
     * @param array<Reservation> $reservations
     */
    private function __construct(
        public string $date,
        public int $prev,
        public int $next,
        public int $offset,
        public array $reservations
    ) {
        parent::__construct('{{reservations.title}}');
        $this->hasReservations = !empty($reservations);
        $this->prevDisabled = $prev === 0;
    }

    /**
     * @param array<Reservation> $reservations
     */
    public static function create(
        string $from = 'now',
        int $current = 0,
        array $reservations = []
    ): Reservations {
        return new Reservations(
            date: self::tryDateTimeParse($from)->format('Y-m-d'),
            prev: $current > 0 ? $current - 1 : 0,
            next: $current + 1,
            offset: $current,
            reservations: $reservations
        );
    }

    private static function tryDateTimeParse(string $date): \DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable($date);
        } catch (\Exception) {
            return new \DateTimeImmutable('now');
        }
    }
}
