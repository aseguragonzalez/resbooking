<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class NotificationConfig
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $serviceId,
        public string $subject,
        public string $text,
        public string $from,
        public string $to,
        public string $template,
        public bool $state,
    ) {
    }
}
