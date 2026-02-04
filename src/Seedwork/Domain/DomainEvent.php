<?php

declare(strict_types=1);

namespace Seedwork\Domain;

/**
 * Base for domain events. Immutable; consumers must not mutate the payload array.
 *
 * @param array<string, mixed> $payload Must not be mutated by consumers.
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
