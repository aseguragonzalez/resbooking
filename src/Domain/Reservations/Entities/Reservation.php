<?php

declare(strict_types=1);

namespace Domain\Reservations\Entities;

use Domain\Reservations\Events\ReservationCreated;
use Domain\Reservations\Events\ReservationStatusUpdated;
use Domain\Reservations\Events\ReservationUpdated;
use Domain\Reservations\ValueObjects\ReservationStatus;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\TimeSlot;
use Seedwork\Domain\AggregateRoot;

final class Reservation extends AggregateRoot
{
    private function __construct(
        string $id,
        private readonly string $restaurantId,
        private readonly \DateTimeImmutable $date,
        private readonly TimeSlot $turn,
        private string $name,
        private Email $email,
        private Phone $phone,
        private Capacity $numberOfDiners,
        private ReservationStatus $status,
    ) {
        parent::__construct($id);
    }

    public static function new(
        string $restaurantId,
        \DateTimeImmutable $date,
        TimeSlot $turn,
        string $name,
        Email $email,
        Phone $phone,
        Capacity $numberOfDiners,
        ?string $id = null
    ): self {
        $reservation = new self(
            id: $id ?? uniqid(),
            restaurantId: $restaurantId,
            date: $date,
            turn: $turn,
            name: $name,
            email: $email,
            phone: $phone,
            numberOfDiners: $numberOfDiners,
            status: ReservationStatus::Pending
        );

        $reservation->addEvent(
            ReservationCreated::new(reservationId: $reservation->getId(), reservation: $reservation)
        );

        return $reservation;
    }

    public static function build(
        string $id,
        string $restaurantId,
        \DateTimeImmutable $date,
        TimeSlot $turn,
        string $name,
        Email $email,
        Phone $phone,
        Capacity $numberOfDiners,
        ReservationStatus $status
    ): self {
        return new self(
            id: $id,
            restaurantId: $restaurantId,
            date: $date,
            turn: $turn,
            name: $name,
            email: $email,
            phone: $phone,
            numberOfDiners: $numberOfDiners,
            status: $status
        );
    }

    public function getRestaurantId(): string
    {
        return $this->restaurantId;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getTurn(): TimeSlot
    {
        return $this->turn;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPhone(): Phone
    {
        return $this->phone;
    }

    public function getNumberOfDiners(): Capacity
    {
        return $this->numberOfDiners;
    }

    public function getStatus(): ReservationStatus
    {
        return $this->status;
    }

    public function updateDetails(string $name, Email $email, Phone $phone): void
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;

        $this->addEvent(
            ReservationUpdated::new(reservationId: $this->getId(), reservation: $this)
        );
    }

    public function updateStatus(ReservationStatus $newStatus): void
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;

        $this->addEvent(
            ReservationStatusUpdated::new(
                reservationId: $this->getId(),
                reservation: $this,
                oldStatus: $oldStatus,
                newStatus: $newStatus
            )
        );
    }
}
