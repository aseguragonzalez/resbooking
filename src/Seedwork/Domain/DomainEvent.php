<?php

declare(strict_types=1);

namespace App\Seedwork\Domain;

abstract class DomainEvent
{
    public function __construct(
        public readonly string $eventType = "DomainEvent",
        public readonly DateTimeImmutable $createdAt = new DateTimeImmutable(
            'now', new DateTimeZone('UTC')
        )
    ) { }
}
