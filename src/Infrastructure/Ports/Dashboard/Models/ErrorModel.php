<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models;

final class ErrorModel
{
    public function __construct(public readonly string $field, public readonly string $message)
    {
    }
}
