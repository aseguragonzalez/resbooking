<?php

declare(strict_types=1);

namespace Domain\Offers\ValueObjects;

use Seedwork\Domain\ValueObject;
use Seedwork\Domain\Exceptions\ValueException;
use Domain\Offers\Exceptions\InvalidDateRange;

final class Settings extends ValueObject
{
    public function __construct(
        public readonly string $description,
        public readonly string $title,
        public readonly string $termsAndConditions,
        public readonly \DateTimeImmutable $startDate,
        public readonly ?\DateTimeImmutable $endDate = null,
    ) {
        $this->checkStartDateAndEndDateRange();
        $this->checkDescription();
        $this->checkTitle();
        $this->checkTermsAndConditions();
    }

    private function checkStartDateAndEndDateRange(): void
    {
        if ($this->endDate !== null && $this->endDate < $this->startDate) {
            throw new InvalidDateRange();
        }
    }

    private function checkDescription(): void
    {
        if (empty($this->description)) {
            throw new ValueException('Description is required');
        }
    }

    private function checkTitle(): void
    {
        if (empty($this->title)) {
            throw new ValueException('Title is required');
        }
    }

    private function checkTermsAndConditions(): void
    {
        if (empty($this->termsAndConditions)) {
            throw new ValueException('Terms and conditions is required');
        }
    }

    public function equals(Settings $settings): bool
    {
        return $this->description === $settings->description
            && $this->title === $settings->title
            && $this->termsAndConditions === $settings->termsAndConditions
            && $this->startDate === $settings->startDate
            && $this->endDate === $settings->endDate;
    }
}
