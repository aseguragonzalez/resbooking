<?php

declare(strict_types=1);

namespace Seedwork\Domain;

/**
 * Base class for domain entities that have an identity.
 *
 * Entities are equal when their identities are equal. Subclasses are typically
 * readonly and expose their own properties.
 *
 * Conventions for subclasses:
 * - Declare the class as readonly.
 * - Use a protected constructor and static factories: new() for creation,
 *   build() when reconstituting with a known id.
 * - Use EntityId::new() for new entities and EntityId::fromString($id) when
 *   loading from storage or commands.
 * - Enforce invariants in the constructor; throw ValueException or domain
 *   exceptions as needed.
 */
abstract readonly class Entity
{
    protected function __construct(public EntityId $id)
    {
    }

    /**
     * Returns whether this entity has the same identity as the other.
     */
    public function equals(Entity $other): bool
    {
        return $this->id->equals($other->id);
    }
}
