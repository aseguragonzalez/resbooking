<?php

declare(strict_types=1);

namespace Seedwork\Domain\Exceptions;

/**
 * Base class for domain-level errors.
 *
 * Use when a business rule is violated or a domain operation cannot be
 * performed (e.g. entity not found, duplicate resource, invalid state transition).
 *
 * Conventions: Create a concrete subclass per domain error (e.g. RestaurantDoesNotExist).
 * Use clear, business-oriented messages. Application or infrastructure catches
 * these and translates them (e.g. to HTTP 404 or form errors).
 */
abstract class DomainException extends \Exception
{
    /**
     * @param string $message Business-relevant error message.
     * @param int    $code    Optional error code (default 0).
     */
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
