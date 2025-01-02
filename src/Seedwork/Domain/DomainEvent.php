<?php

declare(strict_types=1);

namespace App\Seedwork\Domain;

use Tuupola\Ksuid;

abstract class DomainEvent
{
    private readonly string $id;

    protected function __construct(
        ?string $id = null,
        private readonly string $type = "DomainEvent",
        private readonly string $version = "1.0",
        private readonly array $payload = [],
        private readonly ?\DateTimeImmutable $createdAt = new \DateTimeImmutable(
            'now',
            new \DateTimeZone('UTC')
        )
    ) {
        $this->id = $id ?? (string)new Ksuid();
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

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function equals(DomainEvent $other): bool
    {
        return $this->id === $other->getId();
    }
}
