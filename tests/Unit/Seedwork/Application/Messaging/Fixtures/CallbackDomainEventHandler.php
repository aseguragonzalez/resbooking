<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Application\Messaging\Fixtures;

use Seedwork\Application\Messaging\DomainEventHandler;
use Seedwork\Domain\DomainEvent;

/**
 * Test helper that wraps a callable and implements DomainEventHandler.
 */
final class CallbackDomainEventHandler implements DomainEventHandler
{
    /**
     * @param \Closure(DomainEvent): void $callback
     */
    public function __construct(
        private readonly \Closure $callback,
    ) {
    }

    public function execute(DomainEvent $event): void
    {
        ($this->callback)($event);
    }
}
