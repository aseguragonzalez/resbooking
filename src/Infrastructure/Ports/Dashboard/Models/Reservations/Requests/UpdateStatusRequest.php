<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Reservations\Requests;

final readonly class UpdateStatusRequest
{
    public function __construct(
        public string $id,
        public string $status,
        public int $offset = 0,
        public string $from = 'now'
    ) {
    }
}
