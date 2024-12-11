<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Projections;

readonly class Notification
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $serviceId,
        public string $to,
        public string $subject,
        public string $content,
        public int $attempts,
        public string $confSubject,
        public string $confSubjectText,
        public string $from,
        public string $confTo,
        public string $confTemplate,
        public bool $confState,
    ) { }
}
