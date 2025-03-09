<?php

declare(strict_types=1);

namespace Seedwork\Domain;

use Seedwork\Domain\AggregateRoot;

/**
 * Interface Repository
 *
 * This interface defines the contract for a generic repository.
 *
 * @package Seedwork\Domain
 * @template T of AggregateRoot
 */
interface Repository
{
    /**
     * Save an entity to the repository.
     * @param T $aggregateRoot
     * @return void
     */
    public function save($aggregateRoot): void;

    /**
     * Retrieve an entity by its id.
     * @param string $id AggreagateRoot id
     * @return T
     */
    public function getById(string $id);

    /**
     * Check if an entity with the given id exists in the repository.
     * @param string $id AggreagateRoot id
     * @return bool
     */
    public function exist(string $id): bool;
}
