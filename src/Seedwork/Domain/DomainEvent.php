<?php

declare(strict_types=1);

namespace Seedwork\Domain;

/**
 * Base class for domain events: immutable records of something that happened.
 *
 * Events are raised by aggregates (via addEvent()), collected via getEvents(),
 * and published to a DomainEventsBus. Subscribers (DomainEventHandler) react for
 * side effects (e.g. email, projections). The payload is read-only; consumers
 * must not mutate it.
 *
 * Conventions for subclasses:
 * - Declare event classes as readonly.
 * - Use a protected constructor and a static factory new(...): self that builds
 *   the event with a new EntityId for the event, a clear type, and a payload.
 * - Use the event class name (FQCN) as $eventType when subscribing to the bus
 *   (e.g. RestaurantCreated::class).
 */
abstract readonly class DomainEvent
{
    /**
     * @param EntityId              $id        Unique identifier for this event instance.
     * @param string                 $type      Event type; short name or FQCN. Bus routes by FQCN.
     * @param string                 $version   Optional version; default "1.0".
     * @param array<string, mixed>   $payload   Event data. Consumers must not mutate.
     * @param \DateTimeImmutable    $createdAt When the event was created (default UTC now).
     */
    protected function __construct(
        public EntityId $id,
        public string $type = "DomainEvent",
        public string $version = "1.0",
        public array $payload = [],
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(
            'now',
            new \DateTimeZone('UTC')
        )
    ) {
    }

    /**
     * Returns whether this event has the same id as the other.
     */
    public function equals(DomainEvent $other): bool
    {
        return $this->id->equals($other->id);
    }
}
