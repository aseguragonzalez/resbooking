<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reservations\UpdateReservation;

use Application\Reservations\UpdateReservation\UpdateReservation;
use Application\Reservations\UpdateReservation\UpdateReservationCommand;
use Application\Reservations\UpdateReservation\UpdateReservationHandler;
use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Repositories\ReservationRepository;
use Domain\Reservations\Services\ReservationObtainer;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\TimeSlot;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateReservationTest extends TestCase
{
    private MockObject&ReservationObtainer $reservationObtainer;
    private MockObject&ReservationRepository $reservationRepository;
    private UpdateReservation $service;

    protected function setUp(): void
    {
        $this->reservationObtainer = $this->createMock(ReservationObtainer::class);
        $this->reservationRepository = $this->createMock(ReservationRepository::class);
        $this->service = new UpdateReservationHandler($this->reservationObtainer, $this->reservationRepository);
    }

    public function testExecuteUpdatesAndSavesReservation(): void
    {
        $reservationId = 'reservation-123';
        $command = new UpdateReservationCommand(
            reservationId: $reservationId,
            name: 'Jane Doe',
            email: 'jane@example.com',
            phone: '+34-555-0200'
        );

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

        $this->reservationRepository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($reservation));

        $this->service->execute($command);

        $this->assertSame('Jane Doe', $reservation->getName());
        $this->assertSame('jane@example.com', $reservation->getEmail()->value);
        $this->assertSame('+34-555-0200', $reservation->getPhone()->value);
    }
}
