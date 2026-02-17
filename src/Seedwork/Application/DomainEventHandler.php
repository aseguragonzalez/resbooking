<?php

declare(strict_types=1);

namespace Seedwork\Application;

use Seedwork\Domain\DomainEvent;

/**
 * Interface for reacting to domain events.
 *
 * Implementations are subscribed to the bus for specific event types via
 * DomainEventsBus::subscribe($eventType, $handler) and are invoked when the
 * bus delivers events in notify().
 *
 * Conventions: One handler class per side effect (e.g. send email on
 * RestaurantCreated). Inside execute(), cast $event to the concrete type if
 * needed and read from $event->payload; do not mutate the payload. Handlers
 * may depend on infrastructure and are typically registered in the composition root.
 */
interface DomainEventHandler
{
    /**
     * Called by the bus for each event of a type this handler is subscribed to.
     * Cast $event to the concrete event class to read payload.
     */
    public function execute(DomainEvent $event): void;
}
