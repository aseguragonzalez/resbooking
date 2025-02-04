<?php

declare(strict_types=1);

namespace App\Seedwork\Domain;

abstract class DomainEvent
{
    /**
     * @param string $id
     * @param string $type
     * @param string $version
     * @param array<string, mixed> $payload
     * @param \DateTimeImmutable $createdAt
     */
    protected function __construct(
        private readonly string $id,
        private readonly string $type = "DomainEvent",
        private readonly string $version = "1.0",
        private readonly array $payload = [],
        private readonly \DateTimeImmutable $createdAt = new \DateTimeImmutable(
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
