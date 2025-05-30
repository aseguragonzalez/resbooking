<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Reservations;

final readonly class State
{
    public function __construct(public int $id, public int $value, public string $name)
    {
    }
}
