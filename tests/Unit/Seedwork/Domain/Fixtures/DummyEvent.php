<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain\Fixtures;

use Seedwork\Domain\DomainEvent;

final readonly class DummyEvent extends DomainEvent
{
    public function __construct(string $id = 'event-id')
    {
        parent::__construct($id);
    }
}
