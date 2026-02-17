<?php

declare(strict_types=1);

namespace Seedwork\Domain;

use Seedwork\Domain\AggregateRoot;

/**
 * Generic contract for persisting and loading aggregate roots.
 *
 * Repositories are defined as interfaces in the domain; infrastructure
 * implements them (e.g. SQL, in-memory). Application handlers depend on the
 * interface. Only aggregate roots are saved and loaded through this contract.
 *
 * Conventions:
 * - Domain interfaces extend this with @extends Repository<YourAggregate> and
 *   may add methods (e.g. findByUserEmail).
 * - Infrastructure implements the domain interface; may call getEvents() on
 *   the aggregate and publish to DomainEventsBus before or after persisting.
 *
 * @template T of AggregateRoot
 */
interface Repository
{
    /**
     * Persists the given aggregate root. Implementation may call getEvents()
     * on the aggregate and publish events to the domain events bus.
     *
     * @param T $aggregateRoot
     */
    public function save($aggregateRoot): void;

    /**
     * Returns the aggregate root with the given identity, or null if not found.
     *
     * @return T|null
     */
    public function getById(EntityId $id);
}
