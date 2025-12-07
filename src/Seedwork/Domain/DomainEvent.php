<?php

declare(strict_types=1);

namespace Seedwork\Domain;

abstract readonly class DomainEvent
{
    /**
     * @param array<string, mixed> $payload
     */
    protected function __construct(
        private string $id,
        private string $type = "DomainEvent",
        private string $version = "1.0",
        private array $payload = [],
        private \DateTimeImmutable $createdAt = new \DateTimeImmutable(
            'now',
            new \DateTimeZone('UTC')
        )
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    public function equals(DomainEvent $other): bool
    {
        return $this->id === $other->getId();
    }
}
