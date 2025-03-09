<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures;

final class RequestObject
{
    public function __construct(
        public string $name,
        public int $age
    ) {
    }
}
