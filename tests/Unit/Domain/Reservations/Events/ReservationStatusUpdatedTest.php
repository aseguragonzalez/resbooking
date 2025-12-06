<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reservations\Events;

use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Events\ReservationStatusUpdated;
use Domain\Reservations\ValueObjects\ReservationStatus;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\Turn;
use PHPUnit\Framework\TestCase;

final class ReservationStatusUpdatedTest extends TestCase
{
    public function testCreateReservationStatusUpdatedEvent(): void
    {
        $reservation = Reservation::new(
            projectId: 'project-123',
            date: new \DateTimeImmutable('2024-12-25'),
            turn: Turn::H1900,
            name: 'John Doe',
            email: new Email('john@example.com'),
            phone: new Phone('+34-555-0100'),
            numberOfDiners: new Capacity(4)
        );

        $oldStatus = ReservationStatus::Pending;
        $newStatus = ReservationStatus::Accepted;

        $event = ReservationStatusUpdated::new(
            reservationId: $reservation->getId(),
            reservation: $reservation,
            oldStatus: $oldStatus,
            newStatus: $newStatus
        );

        $this->assertInstanceOf(ReservationStatusUpdated::class, $event);
        $this->assertNotEmpty($event->getId());
        $this->assertSame('ReservationStatusUpdated', $event->getType());
        $this->assertSame($reservation->getId(), $event->getPayload()['reservationId']);
        $this->assertSame($reservation, $event->getPayload()['reservation']);
        $this->assertSame($oldStatus->value, $event->getPayload()['oldStatus']);
        $this->assertSame($newStatus->value, $event->getPayload()['newStatus']);
    }
}
