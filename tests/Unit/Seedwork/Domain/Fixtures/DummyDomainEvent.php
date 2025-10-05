<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain\Fixtures;

use Seedwork\Domain\DomainEvent;

class DummyDomainEvent extends DomainEvent
{
    public function __construct(
        string $id = 'event-id',
        string $type = 'DummyType',
        string $version = '2.0',
        array $payload = ['foo' => 'bar'],
        ?\DateTimeImmutable $createdAt = null
    ) {
        parent::__construct(
            $id,
            $type,
            $version,
            $payload,
            $createdAt ?? new \DateTimeImmutable('2020-01-01T00:00:00Z')
        );
    }
}
