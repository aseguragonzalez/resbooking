<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain\Fixtures;

use Seedwork\Domain\AggregateRoot;
use Seedwork\Domain\EntityId;
use Seedwork\Domain\DomainEvent;
use Seedwork\Domain\EntityId;

class DummyAggregateRoot extends AggregateRoot
{
    public function __construct(EntityId $id)
    {
        parent::__construct($id);
    }

    public function addDomainEvent(DomainEvent $event): void
    {
        $this->addEvent($event);
    }
}
