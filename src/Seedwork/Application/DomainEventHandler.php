<?php

declare(strict_types=1);

namespace Seedwork\Application;

use Seedwork\Domain\DomainEvent;

/**
 * Handler for domain events. Implement this interface to subscribe to events via DomainEventsBus.
 */
interface DomainEventHandler
{
    public function execute(DomainEvent $event): void;
}
