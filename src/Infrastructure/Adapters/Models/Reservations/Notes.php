<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Reservations;

final readonly class Note
{
    public function __construct(public int $id, public string $content)
    {
    }
}
