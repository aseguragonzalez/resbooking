<?php

declare(strict_types=1);

namespace Seedwork\Application;

use Seedwork\Domain\DomainEvent;

/**
 * Interface for publishing and subscribing to domain events.
 *
 * Events are stored when published and delivered only when notify() is called,
 * so the same transaction/request can complete (e.g. persist aggregates) before
 * side effects run. Multiple handlers per event type are supported.
 *
 * Conventions:
 * - Publish events after collecting them from aggregates (e.g. in repository
 *   save(): getEvents() then publish() for each).
 * - Subscribe at bootstrap: for each event type, register DomainEventHandler(s).
 * - Notify after the request is handled (e.g. in middleware after response).
 * - Use event FQCN for $eventType (e.g. RestaurantCreated::class) so the bus
 *   can match $event::class to subscribers.
 */
interface DomainEventsBus
{
    /**
     * Stores the event for later delivery. Does not invoke handlers until notify() is called.
     */
    public function publish(DomainEvent $event): void;

    /**
     * Registers a handler for a specific event type. Use FQCN for event type matching.
     * Multiple handlers per event type are allowed.
     *
     * @param string             $eventType         Event class name (FQCN), e.g. RestaurantCreated::class.
     * @param DomainEventHandler $domainEventHandler Handler to invoke on event delivery.
     */
    public function subscribe(string $eventType, DomainEventHandler $domainEventHandler): void;

    /**
     * Delivers all stored events to their subscribed handlers, then clears the buffer.
     */
    public function notify(): void;
}
