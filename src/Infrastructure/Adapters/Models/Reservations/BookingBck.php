<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class BookingBck
{
    public function __construct(
        public int $id,
        public int $idBooking,
        public string $data,
        public string $date,
    ) { }
}
