<?php

declare(strict_types=1);

namespace Application\Projects\AddOpenCloseEvent;

final class AddOpenCloseEventCommand
{
    public function __construct(
        public readonly string $projectId,
        public readonly \DateTimeImmutable $date,
        public readonly bool $isAvailable,
        public readonly string $startTime
    ) {
    }
}
