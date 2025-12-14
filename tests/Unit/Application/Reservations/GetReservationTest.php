<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reservations\GetReservation;

use Application\Reservations\GetReservation\GetReservation;
use Application\Reservations\GetReservation\GetReservationCommand;
use Application\Reservations\GetReservation\GetReservationService;
use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Services\ReservationObtainer;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\TimeSlot;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetReservationTest extends TestCase
{
    private MockObject&ReservationObtainer $reservationObtainer;
    private GetReservation $service;

    protected function setUp(): void
    {
        $this->reservationObtainer = $this->createMock(ReservationObtainer::class);
        $this->service = new GetReservationService($this->reservationObtainer);
    }

    public function testExecuteReturnsReservation(): void
    {
        $reservationId = 'reservation-123';
        $command = new GetReservationCommand(reservationId: $reservationId);

        $reservation = Reservation::new(
            restaurantId: 'restaurant-123',
            date: new \DateTimeImmutable('2024-12-25'),
            turn: TimeSlot::H1900,
            name: 'John Doe',
            email: new Email('john@example.com'),
            phone: new Phone('+34-555-0100'),
            numberOfDiners: new Capacity(4),
            id: $reservationId
        );

        $this->reservationObtainer->expects($this->once())
            ->method('obtain')
            ->with($this->equalTo($reservationId))
            ->willReturn($reservation);

        $result = $this->service->execute($command);

        $this->assertInstanceOf(Reservation::class, $result);
        $this->assertSame($reservationId, $result->getId());
    }
}
