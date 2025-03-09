<?php

declare(strict_types=1);

namespace Seedwork\Domain;

abstract class AggregateRoot
{
    /**
     * @param array<DomainEvent> $domainEvents
     */
    public function __construct(private string $id, private array $domainEvents = [])
    {
    }

    public function equals(AggregateRoot $other): bool
    {
        return $this->id === $other->getId();
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
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

    protected function addEvent(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }
}
