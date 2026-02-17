<?php

declare(strict_types=1);

namespace Seedwork\Domain;

/**
 * Base class for immutable value objects.
 *
 * Value objects are defined by their attributes; equality is based on value
 * comparison, not identity. Subclasses must implement equals().
 *
 * Conventions for subclasses:
 * - Declare the class as readonly and keep all properties readonly.
 * - Do not allow modification after construction; no setters.
 * - Implement equals() by comparing all relevant properties (return false if
 *   $other is not the same class, then compare each field).
 * - Use for concepts with no identity (email, capacity, settings). Use Entity
 *   when the object has a stable identity over time.
 * - Throw ValueException in the constructor when invariants are violated.
 */
abstract readonly class ValueObject
{
    /**
     * Value-based comparison. Subclasses implement by comparing all relevant properties.
     */
    abstract public function equals(ValueObject $other): bool;
}
