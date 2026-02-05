<?php

declare(strict_types=1);

namespace Seedwork\Application\Messaging;

use Seedwork\Domain\AggregateRoot;
use Seedwork\Domain\Repository;

/**
 * Decorator that publishes domain events from an aggregate after saving.
 * Wraps any Repository<T> and publishes events from getEvents() after each save.
 *
 * @template T of AggregateRoot
 * @implements Repository<T>
 */
final readonly class EventPublishingRepositoryDecorator implements Repository
{
    /**
     * @param Repository<T> $inner
     */
    public function __construct(
        private Repository $inner,
        private DomainEventsBus $domainEventsBus,
    ) {
    }

    /**
     * @param T $aggregateRoot
     */
    public function save($aggregateRoot): void
    {
        $this->inner->save($aggregateRoot);

        foreach ($aggregateRoot->getEvents() as $event) {
            $this->domainEventsBus->publish($event);
        }
    }

    /**
     * @return T|null
     */
    public function getById(string $id)
    {
        return $this->inner->getById($id);
    }
}
