<?php

declare(strict_types=1);

namespace Seedwork\Domain;

/**
 * Base class for aggregate roots.
 *
 * Each aggregate has a unique identity and an internal buffer of domain events.
 * Events are added via addEvent() when something meaningful happens and are drained
 * (and cleared) when getEvents() is called—typically by the repository or by
 * infrastructure that publishes them to the domain events bus.
 *
 * Conventions for subclasses:
 * - Use a private constructor and static named constructors: new() for creation,
 *   build() when reconstituting from persistence.
 * - Call addEvent() only from inside the aggregate when something meaningful happens.
 * - Do not call getEvents() from domain or application logic; let infrastructure
 *   call it and publish each event via DomainEventsBus::publish().
 */
abstract class AggregateRoot
{
    /**
     * @param EntityId             $id            Aggregate identity.
     * @param array<DomainEvent>   $domainEvents  Initial event buffer (subclasses typically pass none).
     */
    public function __construct(private EntityId $id, private array $domainEvents = [])
    {
    }

    /**
     * Returns whether this aggregate has the same identity as the other.
     */
    public function equals(AggregateRoot $other): bool
    {
        return $this->id->equals($other->getId());
    }

    /**
     * Returns the aggregate identity.
     */
    public function getId(): EntityId
    {
        return $this->id;
    }

    /**
     * Returns a copy of all buffered domain events and clears the buffer.
     * Infrastructure (e.g. repository) should call this and publish events to the bus.
     *
     * @return array<DomainEvent>
     */
    public function getEvents(): array
    {
        $domainEvents = array_map(
            fn (DomainEvent $domainEvent) => clone $domainEvent,
            $this->domainEvents
        );
        $this->domainEvents = [];
        return $domainEvents;
    }

    /**
     * Appends an event to the buffer. Call from within the aggregate when a
     * domain-relevant change occurs (e.g. entity added, settings changed).
     */
    protected function addEvent(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }
}
