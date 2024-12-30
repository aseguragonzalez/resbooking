<?php

declare(strict_types=1);

namespace App\Domain\Offers\Entities;

use App\Domain\Offers\Exceptions\InvalidDateRange;
use App\Domain\Shared\Exceptions\{
    OpenCloseEventAlreadyExist,
    OpenCloseEventDoesNotExist,
    OpenCloseEventOutOfRange,
    TurnAlreadyExist,
    TurnDoesNotExist,
};
use App\Domain\Shared\ValueObjects\{OpenCloseEvent, TurnAvailability};
use App\Seedwork\Domain\AggregateRoot;
use App\Seedwork\Domain\Exceptions\ValueException;

final class Offer extends AggregateRoot
{
    /**
     * @param $openCloseEvents array<OpenCloseEvent>
     * @param $turns array<TurnAvailability>
     */
    public function __construct(
        private readonly string $id,
        private string $description,
        private string $title,
        private string $termsAndConditions,
        private \DateTimeImmutable $startDate,
        private ?\DateTimeImmutable $endDate = null,
        private bool $isEnable = true,
        private array $openCloseEvents = [],
        private array $turns = [],
    ) {
        parent::__construct($id);
        $this->checkStartDateAndEndDateRange();
        $this->checkDescription();
        $this->checkTitle();
        $this->checkTermsAndConditions();
    }

    private function checkStartDateAndEndDateRange(): void
    {
        if ($this->endDate !== null && $this->endDate < $this->startDate) {
            throw new InvalidDateRange('End date must be greater than start date');
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTermsAndConditions(): string
    {
        return $this->termsAndConditions;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function update(
        string $description,
        string $title,
        string $termsAndConditions,
        \DateTimeImmutable $startDate,
        ?\DateTimeImmutable $endDate = null,
    ): void {
        $this->description = $description;
        $this->title = $title;
        $this->termsAndConditions = $termsAndConditions;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->checkDescription();
        $this->checkStartDateAndEndDateRange();
        $this->checkTitle();
        $this->checkTermsAndConditions();
    }

    public function disable(): void
    {
        $this->isEnable = false;
    }

    public function enable(): void
    {
        $this->isEnable = true;
    }

    public function isEnable(): bool
    {
        return $this->isEnable;
    }

    /**
     * @return array<TurnAvailability>
     */
    public function getTurns(): array
    {
        return $this->turns;
    }

    public function addTurn(TurnAvailability $turn): void
    {
        $turns = array_filter($this->turns, fn (TurnAvailability $s) => $s->equals($turn));
        if (!empty($turns)) {
            throw new TurnAlreadyExist();
        }
        $this->turns[] = $turn;
    }

    public function removeTurn(TurnAvailability $turn): void
    {
        $turns = array_filter($this->turns, fn (TurnAvailability $s) => $s->equals($turn));
        if (empty($turns)) {
            throw new TurnDoesNotExist();
        }
        $this->turns = array_filter(
            $this->turns,
            fn (TurnAvailability $s) => !$s->equals($turn)
        );
    }

    /**
     * @return array<OpenCloseEvent>
     */
    public function getOpenCloseEvents(): array
    {
        return $this->openCloseEvents;
    }

    public function addOpenCloseEvent(OpenCloseEvent $event): void
    {
        $events = array_filter($this->openCloseEvents, fn (OpenCloseEvent $s) => $s->equals($event));
        if (!empty($events)) {
            throw new OpenCloseEventAlreadyExist();
        }
        $yesterday = (new \DateTimeImmutable())->sub(new \DateInterval('P1D'));

        if ($event->date <= $yesterday) {
            throw new OpenCloseEventOutOfRange();
        }

        $this->openCloseEvents[] = $event;
    }

    public function removeOpenCloseEvent(OpenCloseEvent $event): void
    {
        $events = array_filter($this->openCloseEvents, fn (OpenCloseEvent $s) => $s->equals($event));
        if (empty($events)) {
            throw new OpenCloseEventDoesNotExist();
        }
        $this->openCloseEvents = array_filter(
            $this->openCloseEvents,
            fn (OpenCloseEvent $event) => !$event->equals($event)
        );
    }
}
