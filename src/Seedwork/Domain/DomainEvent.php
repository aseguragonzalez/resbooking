<?php

declare(strict_types=1);

namespace Seedwork\Domain;

/**
 * Base for domain events.
 *
 * Domain events expose an immutable payload array via the {@see $payload} property,
 * which consumers must not mutate.
 */
abstract readonly class DomainEvent
{
    /**
     * @param array<string, mixed> $payload Must not be mutated by consumers.
     */
    protected function __construct(
        public string $id,
        public string $type = "DomainEvent",
        public string $version = "1.0",
        public array $payload = [],
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(
            'now',
            new \DateTimeZone('UTC')
        )
    ) {
    }

    public function equals(DomainEvent $other): bool
    {
        return $this->id === $other->id;
    }
}
