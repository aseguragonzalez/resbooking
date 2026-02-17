<?php

declare(strict_types=1);

namespace Seedwork\Domain\Exceptions;

/**
 * Exception for value object or invariant violations.
 *
 * Use when a value is invalid (e.g. empty name, min greater than max) or when
 * a value object constructor detects a broken invariant. Throw from value object
 * or entity/aggregate constructors when validation fails. Message should
 * describe the invariant (e.g. "Name is required").
 */
class ValueException extends DomainException
{
    /**
     * @param string $message Description of the invariant violation.
     * @param int    $code    Optional error code (default 0).
     */
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
