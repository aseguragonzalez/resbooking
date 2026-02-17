<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain;

use PHPUnit\Framework\TestCase;
use Seedwork\Domain\EntityId;
use Tests\Unit\Seedwork\Domain\Fixtures\DummyAggregateRoot;
use Tests\Unit\Seedwork\Domain\Fixtures\DummyEvent;

class AggregateRootTest extends TestCase
{
    public function testEqualsReturnsTrueForSameId(): void
    {
        $agg1 = new DummyAggregateRoot(EntityId::fromString('id-1'));
        $agg2 = new DummyAggregateRoot(EntityId::fromString('id-1'));
        $this->assertTrue($agg1->equals($agg2));
    }

    public function testEqualsReturnsFalseForDifferentId(): void
    {
        $agg1 = new DummyAggregateRoot(EntityId::fromString('id-1'));
        $agg2 = new DummyAggregateRoot(EntityId::fromString('id-2'));
        $this->assertFalse($agg1->equals($agg2));
    }

    public function testGetIdReturnsId(): void
    {
        $agg = new DummyAggregateRoot(EntityId::fromString('my-id'));
        $this->assertSame('my-id', $agg->getId()->value);
    }

    public function testGetEventsReturnsEmptyInitially(): void
    {
        $agg = new DummyAggregateRoot(EntityId::fromString('id'));
        $this->assertSame([], $agg->getEvents());
    }

    public function testAddEventAndGetEvents(): void
    {
        $agg = new DummyAggregateRoot(EntityId::fromString('id'));
        $event = new DummyEvent(EntityId::fromString('event-1'));
        $agg->addDomainEvent($event);
        $events = $agg->getEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(DummyEvent::class, $events[0]);
        $this->assertSame('event-1', $events[0]->id->value);
    }

    public function testGetEventsClearsEvents(): void
    {
        $agg = new DummyAggregateRoot(EntityId::fromString('id'));
        $event = new DummyEvent(EntityId::fromString('event-1'));
        $agg->addDomainEvent($event);
        $agg->getEvents();
        $this->assertSame([], $agg->getEvents());
    }

    public function testMultipleEvents(): void
    {
        $agg = new DummyAggregateRoot(EntityId::fromString('id'));
        $event1 = new DummyEvent(EntityId::fromString('event-1'));
        $event2 = new DummyEvent(EntityId::fromString('event-2'));
        $agg->addDomainEvent($event1);
        $agg->addDomainEvent($event2);
        $events = $agg->getEvents();
        $this->assertCount(2, $events);
        $this->assertSame('event-1', $events[0]->id->value);
        $this->assertSame('event-2', $events[1]->id->value);
    }
}
