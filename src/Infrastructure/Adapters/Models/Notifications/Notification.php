<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class Notification
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $serviceId,
        public string $to,
        public string $subject,
        public string $header,
        public string $content,
        public string $date,
        public bool $dispatched,
    ) {
    }
}
