<?php

declare(strict_types=1);

namespace App\Domain\Projects\ValueObjects;

use App\Seedwork\Domain\ValueObject;
use App\Seedwork\Domain\Exceptions\ValueException;
use App\Domain\Shared\{Capacity, Email, Phone};

final class Settings extends ValueObject
{
    public function __construct(
        public readonly Email $email,
        public readonly bool $hasRemainders,
        public readonly string $name,
        public readonly Capacity $maxNumberOfDiners,
        public readonly Capacity $minNumberOfDiners,
        public readonly Phone $phone,
    ) {
        if (empty($name)) {
            throw new ValueException('Name is required');
        }
    }
}
