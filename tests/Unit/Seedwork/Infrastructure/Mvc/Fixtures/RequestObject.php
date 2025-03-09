<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures;

use Tuupola\Ksuid;

final class RequestObject
{
    public function __construct(
        public readonly int $id = 0,
        public readonly float $amount = 0.0,
        public readonly string $name = '',
        public readonly string $uuid = '',
        public readonly ?Ksuid $ksuid = null,
        public readonly ?\DateTime $date = null,
        public readonly ?\DateTimeImmutable $dateImmutable = null,
        public readonly bool $active = false,
    ) {
    }
}
