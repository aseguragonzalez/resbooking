<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models;

final readonly class ErrorModel
{
    public function __construct(public string $field, public string $message)
    {
    }
}
