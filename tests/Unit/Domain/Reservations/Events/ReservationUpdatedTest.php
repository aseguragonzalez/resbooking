<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reservations\Events;

use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Events\ReservationUpdated;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\TimeSlot;
use PHPUnit\Framework\TestCase;

final class ReservationUpdatedTest extends TestCase
{
    public function testCreateReservationUpdatedEvent(): void
    {
        $reservation = Reservation::new(
            restaurantId: 'restaurant-123',
            date: new \DateTimeImmutable('2024-12-25'),
            turn: TimeSlot::H1900,
            name: 'John Doe',
            email: new Email('john@example.com'),
            phone: new Phone('+34-555-0100'),
            numberOfDiners: new Capacity(4)
        );

        $event = ReservationUpdated::new(
            reservationId: $reservation->getId(),
            reservation: $reservation
        );

        $this->assertInstanceOf(ReservationUpdated::class, $event);
        $this->assertNotEmpty($event->getId());
        $this->assertSame('ReservationUpdated', $event->getType());
        $this->assertSame($reservation->getId(), $event->getPayload()['reservationId']);
        $this->assertSame($reservation, $event->getPayload()['reservation']);
    }
}
