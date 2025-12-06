<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models\Places;

final class Place
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly int $capacity
    ) {
    }
}
