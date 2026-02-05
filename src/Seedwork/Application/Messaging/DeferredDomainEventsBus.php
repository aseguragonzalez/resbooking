<?php

declare(strict_types=1);

namespace Seedwork\Application\Messaging;

use Seedwork\Domain\DomainEvent;

/**
 * Domain events bus that stores events on publish and delivers them only when notify() is called.
 */
final class DeferredDomainEventsBus implements DomainEventsBus
{
    /**
     * @var array<DomainEvent>
     */
    private array $buffer = [];

    /**
     * @var array<string, array<callable(DomainEvent): void>>
     */
    private array $handlers = [];

    public function publish(DomainEvent $event): void
    {
        $this->buffer[] = $event;
    }

    public function subscribe(string $eventType, callable $handler): void
    {
        if (!isset($this->handlers[$eventType])) {
            $this->handlers[$eventType] = [];
        }
        $this->handlers[$eventType][] = $handler;
    }

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
                $handler($event);
            }
        }
    }
}
