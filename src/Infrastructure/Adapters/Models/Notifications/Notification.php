<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Notifications;

final readonly class Notification
{
    public function __construct(
        public int $id,
        public int $projectId,
        public string $to,
        public string $subject,
        public string $header,
        public string $content,
        public string $date,
        public bool $sent,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt = null,
        public ?\DateTimeImmutable $dispatchedAt = null,
    ) {
    }
}
