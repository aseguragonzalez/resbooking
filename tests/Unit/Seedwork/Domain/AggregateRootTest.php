<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain;

use PHPUnit\Framework\TestCase;
use Tests\Unit\Seedwork\Domain\Fixtures\DummyAggregateRoot;
use Tests\Unit\Seedwork\Domain\Fixtures\DummyEvent;

class AggregateRootTest extends TestCase
{
    public function testEqualsReturnsTrueForSameId(): void
    {
        $agg1 = new DummyAggregateRoot('id-1');
        $agg2 = new DummyAggregateRoot('id-1');
        $this->assertTrue($agg1->equals($agg2));
    }

    public function testEqualsReturnsFalseForDifferentId(): void
    {
        $agg1 = new DummyAggregateRoot('id-1');
        $agg2 = new DummyAggregateRoot('id-2');
        $this->assertFalse($agg1->equals($agg2));
    }

    public function testGetIdReturnsId(): void
    {
        $agg = new DummyAggregateRoot('my-id');
        $this->assertSame('my-id', $agg->getId());
    }

    public function testGetEventsReturnsEmptyInitially(): void
    {
        $agg = new DummyAggregateRoot('id');
        $this->assertSame([], $agg->getEvents());
    }

    public function testAddEventAndGetEvents(): void
    {
        $agg = new DummyAggregateRoot('id');
        $event = new DummyEvent('event-1');
        $agg->addDomainEvent($event);
        $events = $agg->getEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(DummyEvent::class, $events[0]);
        $this->assertSame('event-1', $events[0]->getId());
    }

    public function testGetEventsClearsEvents(): void
    {
        $agg = new DummyAggregateRoot('id');
        $event = new DummyEvent('event-1');
        $agg->addDomainEvent($event);
        $agg->getEvents();
        $this->assertSame([], $agg->getEvents());
    }

    public function testMultipleEvents(): void
    {
        $agg = new DummyAggregateRoot('id');
        $event1 = new DummyEvent('event-1');
        $event2 = new DummyEvent('event-2');
        $agg->addDomainEvent($event1);
        $agg->addDomainEvent($event2);
        $events = $agg->getEvents();
        $this->assertCount(2, $events);
        $this->assertSame('event-1', $events[0]->getId());
        $this->assertSame('event-2', $events[1]->getId());
    }
}
