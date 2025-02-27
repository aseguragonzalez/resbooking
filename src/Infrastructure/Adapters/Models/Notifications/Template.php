<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Notifications;

final readonly class Template
{
    public function __construct(
        public int $id,
        public string $body,
        public string $from,
        public string $name,
        public string $to,
        public string $subject,
    ) {
    }
}
