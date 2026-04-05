<?php

declare(strict_types=1);

namespace Framework\Web\Actions;

/**
 * Declares the element type for an array request parameter (alternative to @param array<T> docblocks).
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final readonly class RequestArrayElementType
{
    public function __construct(public string $type)
    {
    }
}
