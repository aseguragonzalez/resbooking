<?php

declare(strict_types=1);

namespace App\Domain\Projects\ValueObjects;

use Seedwork\Domain\ValueObject;
use Seedwork\Domain\Exceptions\ValueException;
use App\Domain\Shared\{Capacity, Email, Phone};

final class Settings extends ValueObject
{
    public function __construct(
        public readonly Email $email,
        public readonly bool $hasRemainders,
        public readonly string $name,
        public readonly Capacity $maxNumberOfDiners,
        public readonly Capacity $minNumberOfDiners,
        public readonly Capacity $numberOfTables,
        public readonly Phone $phone,
    ) {
        $this->checkName();
        $this->checkMinMaxNumberOfDinners();
    }

    private function checkName(): void
    {
        if (empty($this->name)) {
            throw new ValueException('Name is required');
        }
    }

    private function checkMinMaxNumberOfDinners(): void
    {
        if ($this->minNumberOfDiners->value > $this->maxNumberOfDiners->value) {
            throw new ValueException('Min number of diners must be less than or equal to max number of diners');
        }
    }
}
