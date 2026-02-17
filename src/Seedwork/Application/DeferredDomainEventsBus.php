<?php

declare(strict_types=1);

namespace Seedwork\Application;

use Seedwork\Domain\DomainEvent;

/**
 * Default implementation of DomainEventsBus.
 *
 * Events are appended to an internal buffer on publish() and delivered to
 * subscribed handlers when notify() is called. Matching is done by event class
 * name ($event::class). Use this class as the concrete DomainEventsBus in the
 * application (e.g. in the container or middleware). Inject it where events are
 * published (e.g. repository) and where notify() is called (e.g. DomainEventsMiddleware).
 *
 * Flow: Aggregates raise events via addEvent(); on save, caller gets getEvents()
 * and publish()s each to the bus; after the response is produced, middleware
 * calls notify(), which runs all subscribed handlers and clears the buffer.
 */
final class DeferredDomainEventsBus implements DomainEventsBus
{
    /** @var array<DomainEvent> */
    private array $buffer = [];

    /** @var array<string, array<DomainEventHandler>> */
    private array $handlers = [];

    /**
     * Appends the event to the buffer. Delivery happens only when notify() is called.
     */
    public function publish(DomainEvent $event): void
    {
        $this->buffer[] = $event;
    }

    /**
     * Adds a handler for the given event type (FQCN). Multiple handlers per type are stored in order.
     *
     * @param string $eventType Event class name (FQCN), e.g. RestaurantCreated::class.
     */
    public function subscribe(string $eventType, DomainEventHandler $domainEventHandler): void
    {
        if (!isset($this->handlers[$eventType])) {
            $this->handlers[$eventType] = [];
        }
        $this->handlers[$eventType][] = $domainEventHandler;
    }

    /**
     * Delivers all buffered events to their subscribed handlers (by $event::class), then clears the buffer.
     */
    public function notify(): void
    {
        $events = $this->buffer;
        $this->buffer = [];

        foreach ($events as $event) {
            $eventType = $event::class;
            if (!isset($this->handlers[$eventType])) {
                continue;
            }

            foreach ($this->handlers[$eventType] as $handler) {
                $handler->execute($event);
            }
        }
    }
}
