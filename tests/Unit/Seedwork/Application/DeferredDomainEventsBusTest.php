<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Application;

use PHPUnit\Framework\TestCase;
use Seedwork\Domain\EntityId;
use Seedwork\Application\DeferredDomainEventsBus;
use Seedwork\Domain\EntityId;
use Tests\Unit\Seedwork\Application\Fixtures\CallbackDomainEventHandler;
use Tests\Unit\Seedwork\Domain\Fixtures\DummyDomainEvent;
use Tests\Unit\Seedwork\Domain\Fixtures\DummyEvent;

/**
 * @internal
 *
 * @coversNothing
 */
final class DeferredDomainEventsBusTest extends TestCase
{
    private DeferredDomainEventsBus $bus;

    protected function setUp(): void
    {
        $this->bus = new DeferredDomainEventsBus();
    }

    public function testPublishStoresEventsWithoutDelivery(): void
    {
        $event = new DummyDomainEvent();
        $received = [];
        $this->bus->subscribe(DummyDomainEvent::class, new CallbackDomainEventHandler(function ($e) use (&$received) {
            $received[] = $e;
        }));

        $this->bus->publish($event);

        $this->assertEmpty($received);
    }

    public function testNotifyDeliversStoredEventsToHandlers(): void
    {
        $event = new DummyDomainEvent(id: EntityId::fromString('evt-1'));
        $received = [];

        $this->bus->subscribe(DummyDomainEvent::class, new CallbackDomainEventHandler(function ($e) use (&$received) {
            $received[] = $e;
        }));

        $this->bus->publish($event);
        $this->bus->notify();

        $this->assertCount(1, $received);
        $this->assertSame('evt-1', $received[0]->id->value);
    }

    public function testBufferClearedAfterNotify(): void
    {
        $event = new DummyDomainEvent();
        $callCount = 0;

        $this->bus->subscribe(DummyDomainEvent::class, new CallbackDomainEventHandler(function () use (&$callCount) {
            ++$callCount;
        }));

        $this->bus->publish($event);
        $this->bus->notify();
        $this->bus->notify();

        $this->assertSame(1, $callCount);
    }

    public function testMultipleHandlersPerEventTypeAreInvoked(): void
    {
        $event = new DummyDomainEvent();
        $received1 = [];
        $received2 = [];

        $this->bus->subscribe(DummyDomainEvent::class, new CallbackDomainEventHandler(function ($e) use (&$received1) {
            $received1[] = $e;
        }));
        $this->bus->subscribe(DummyDomainEvent::class, new CallbackDomainEventHandler(function ($e) use (&$received2) {
            $received2[] = $e;
        }));

        $this->bus->publish($event);
        $this->bus->notify();

        $this->assertCount(1, $received1);
        $this->assertCount(1, $received2);
        $this->assertTrue($event->id->equals($received1[0]->id));
        $this->assertTrue($event->id->equals($received2[0]->id));
    }

    public function testEventTypeMatchingByFqcn(): void
    {
        $dummyDomainEvent = new DummyDomainEvent(id: EntityId::fromString('dd-1'));
        $dummyEvent = new DummyEvent(id: EntityId::fromString('de-1'));

        $receivedDummyDomain = [];
        $receivedDummy = [];

        $this->bus->subscribe(
            DummyDomainEvent::class,
            new CallbackDomainEventHandler(function ($e) use (&$receivedDummyDomain) {
                $receivedDummyDomain[] = $e;
            })
        );
        $this->bus->subscribe(
            DummyEvent::class,
            new CallbackDomainEventHandler(function ($e) use (&$receivedDummy) {
                $receivedDummy[] = $e;
            })
        );

        $this->bus->publish($dummyDomainEvent);
        $this->bus->publish($dummyEvent);
        $this->bus->notify();

        $this->assertCount(1, $receivedDummyDomain);
        $this->assertSame('dd-1', $receivedDummyDomain[0]->id->value);

        $this->assertCount(1, $receivedDummy);
        $this->assertSame('de-1', $receivedDummy[0]->id->value);
    }

    public function testEventsWithNoHandlersAreSkipped(): void
    {
        $event = new DummyDomainEvent();

        $this->bus->publish($event);
        $this->bus->notify();

        $this->expectNotToPerformAssertions();
    }

    public function testEventsDeliveredInPublishOrder(): void
    {
        $event1 = new DummyDomainEvent(id: EntityId::fromString('first'));
        $event2 = new DummyDomainEvent(id: EntityId::fromString('second'));
        $order = [];

        $this->bus->subscribe(DummyDomainEvent::class, new CallbackDomainEventHandler(function ($e) use (&$order) {
            $order[] = $e->id->value;
        }));

        $this->bus->publish($event1);
        $this->bus->publish($event2);
        $this->bus->notify();

        $this->assertSame(['first', 'second'], $order);
    }
}
