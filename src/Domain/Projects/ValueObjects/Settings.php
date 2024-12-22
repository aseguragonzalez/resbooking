<?php

declare(strict_types=1);

namespace App\Domain\Projects\ValueObjects;

use DateTimeImmutable;
use App\Seedwork\Domain\ValueObject;
use App\Shared\Capacity;

final class Settings extends ValueObject
{
    public function __construct(
        public readonly Capacity $maxNumberOfDiners,
        public readonly Capacity $minNumberOfDiners,
        public readonly bool $hasRemainders,
        public readonly string $name,
        public readonly string $claim,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $address,
    ) { }
}
