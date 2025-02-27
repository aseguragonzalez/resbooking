<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models\Projects;

final readonly class Turn
{
    public function __construct(public int $id, public int $value, public string $name)
    {
    }
}
