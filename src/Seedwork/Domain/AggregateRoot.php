<?php

declare(strict_types=1);

namespace App\Seedwork\Domain;

abstract class AggregateRoot
{
    public function __construct(private ?int $id = null, private array $events = []) { }

    public function equals(AggregateRoot $other): bool
    {
      return $this->id === $other->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvents(): array
    {
        $events = array_map(fn(DomainEvent $event) => clone $event, $events);
        $this->events = [];
        return $events;
    }

    protected function addEvent(DomainEvent $event): void
    {
        $this->events[] = $event;
    }

    protected function addEvents(array $events): void
    {
        $this->events[] = $events;
    }
}
