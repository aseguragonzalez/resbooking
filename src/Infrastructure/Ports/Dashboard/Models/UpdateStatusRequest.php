<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models;

final class UpdateStatusRequest
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly int $offset = 0,
        public readonly string $from = 'now'
    ) {
    }
}
