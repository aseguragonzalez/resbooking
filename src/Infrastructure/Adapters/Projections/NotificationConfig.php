<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Projections;

readonly class NotificationConfig
{
    public function __construct(
        public int $id,
        public int $projectId,
        public string $ProjectName,
        public int $serviceId,
        public string $serviceName,
        public string $subject,
        public string $text,
        public string $from,
        public string $to,
        public string $template,
        public bool $state,
    ) { }
}
