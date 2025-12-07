<?php

declare(strict_types=1);

namespace Application\Projects\AddOpenCloseEvent;

final readonly class AddOpenCloseEventCommand
{
    public function __construct(
        public string $projectId,
        public \DateTimeImmutable $date,
        public bool $isAvailable,
        public string $startTime
    ) {
    }
}
