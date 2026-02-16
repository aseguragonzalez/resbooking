<?php

declare(strict_types=1);

namespace Seedwork\Application;

use Seedwork\Domain\DomainEvent;

/**
 * Domain events bus for publishing and subscribing to domain events.
 *
 * Events are stored on publish and delivered only when notify() is called.
 * Supports multiple handlers per event type.
 */
interface DomainEventsBus
{
    /**
     * Store a domain event for later delivery. Does not deliver until notify() is called.
     */
    public function publish(DomainEvent $event): void;

    /**
     * Subscribe a handler for a specific event type.
     * Use FQCN (e.g. RestaurantCreated::class) for event type matching.
     * Multiple handlers per event type are allowed.
     *
     * @param string               $eventType         Event class name (FQCN)
     * @param DomainEventHandler   $domainEventHandler Handler to invoke on event delivery
     */
    public function subscribe(string $eventType, DomainEventHandler $domainEventHandler): void;

    /**
     * Deliver all stored events to their subscribed handlers, then clear the buffer.
     */
    public function notify(): void;
}
