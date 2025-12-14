<?php

declare(strict_types=1);

namespace Domain\Restaurants\ValueObjects;

use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Seedwork\Domain\Exceptions\ValueException;
use Seedwork\Domain\ValueObject;

final readonly class Settings extends ValueObject
{
    public function __construct(
        public Email $email,
        public bool $hasReminders,
        public string $name,
        public Capacity $maxNumberOfDiners,
        public Capacity $minNumberOfDiners,
        public Capacity $numberOfTables,
        public Phone $phone,
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

    public function equals(ValueObject $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }
        /** @var self $other */
        return $this->email->equals($other->email)
            && $this->hasReminders === $other->hasReminders
            && $this->name === $other->name
            && $this->maxNumberOfDiners->equals($other->maxNumberOfDiners)
            && $this->minNumberOfDiners->equals($other->minNumberOfDiners)
            && $this->numberOfTables->equals($other->numberOfTables)
            && $this->phone->equals($other->phone);
    }
}
