<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class BookingSource
{
    public function __construct(
        public int $id,
        public string $sourceName,
        public string $description,
    ) { }
}
