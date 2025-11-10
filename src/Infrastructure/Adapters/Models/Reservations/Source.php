<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Models\Reservations;

final readonly class Source
{
    public function __construct(public int $id, public int $value, public string $name)
    {
    }
}
