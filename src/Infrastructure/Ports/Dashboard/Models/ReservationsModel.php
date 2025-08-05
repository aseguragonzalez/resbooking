<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models;

use DateTimeImmutable;

final class ReservationsModel extends PageModel
{
    public readonly bool $hasReservations;

    /**
     * @param array<Reservation> $reservations
     */
    private function __construct(
        public readonly string $date,
        public readonly int $prev,
        public readonly int $next,
        public readonly int $offset,
        public readonly array $reservations
    ) {
        parent::__construct('Reservations');
        $this->hasReservations = !empty($reservations);
    }

    /**
     * @param array<Reservation> $reservations
     */
    public static function create(
        string $from = 'now',
        int $current = 0,
        array $reservations = []
    ): ReservationsModel {
        return new ReservationsModel(
            date: self::tryDateTimeParse($from)->format('Y-m-d'),
            prev: $current > 0 ? $current - 1 : 0,
            next: $current + 1,
            offset: $current,
            reservations: $reservations
        );
    }

    private static function tryDateTimeParse(string $date): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($date);
        } catch (\Exception) {
            return new DateTimeImmutable('now');
        }
    }
}
