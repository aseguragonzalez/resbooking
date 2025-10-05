<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain\Fixtures;

use Seedwork\Domain\AggregateRoot;
use Seedwork\Domain\DomainEvent;

class DummyAggregateRoot extends AggregateRoot
{
    public function __construct(string $id)
    {
        parent::__construct($id);
    }

    public function addDomainEvent(DomainEvent $event): void
    {
        $this->addEvent($event);
    }
}
