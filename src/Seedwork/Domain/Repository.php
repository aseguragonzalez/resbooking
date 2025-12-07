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
     * @return T|null
     */
    public function getById(string $id);
}
