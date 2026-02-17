<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain\Fixtures;

use Seedwork\Domain\DomainEvent;
use Seedwork\Domain\EntityId;

final readonly class DummyEvent extends DomainEvent
{
    public function __construct(?EntityId $id = null)
    {
        parent::__construct($id ?? EntityId::fromString('event-id'));
    }
}
