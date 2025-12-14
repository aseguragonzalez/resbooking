<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\DiningAreas;

final readonly class DiningArea
{
    public function __construct(
        public string $id,
        public string $name,
        public int $capacity
    ) {
    }
}
