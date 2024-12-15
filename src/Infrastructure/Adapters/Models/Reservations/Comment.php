<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class Comment
{
    public function __construct(
        public int $id,
        public int $idBooking,
        public string $text,
        public string $date,
        public string $username,
    ) { }
}
