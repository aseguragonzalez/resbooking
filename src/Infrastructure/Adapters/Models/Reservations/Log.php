<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class Log
{
    public function __construct(
        public int $id,
        public int $bookingId,
        public string $address,
        public string $date,
        public string $information,
    ) {
    }
}
