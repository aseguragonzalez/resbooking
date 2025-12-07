<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Places;

final readonly class Place
{
    public function __construct(
        public string $id,
        public string $name,
        public int $capacity
    ) {
    }
}
