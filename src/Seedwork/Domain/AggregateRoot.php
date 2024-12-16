<?php

declare(strict_types=1);

namespace App\Seedwork\Domain;

abstract class AggregateRoot
{
    public function __construct(private ?int $id = null, private array $domainEvents = []) { }

    public function equals(AggregateRoot $other): bool
    {
      return $this->id === $other->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomainEvents(): array
    {
        $domainEvents = array_map(
            fn(DomainEvent $domainEvent) => clone $domainEvent, $this->domainEvents
        );
        $this->domainEvents = [];
        return $domainEvents;
    }

    protected function addDomainEvent(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }

    protected function addDomainEvents(array $domainEvents): void
    {
        $this->domainEvents[] = $domainEvents;
    }
}
