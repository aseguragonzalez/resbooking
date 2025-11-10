<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Models\Projects;

final readonly class DayOfWeek
{
    public function __construct(public int $id, public int $value, public string $name)
    {
    }
}
