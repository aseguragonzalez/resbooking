<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reservations\GetReservation;

use Application\Reservations\GetReservation\GetReservation;
use Application\Reservations\GetReservation\GetReservationCommand;
use Application\Reservations\GetReservation\GetReservationService;
use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Exceptions\ReservationDoesNotExist;
use Domain\Reservations\Repositories\ReservationRepository;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\Turn;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetReservationTest extends TestCase
{
    private MockObject&ReservationRepository $reservationRepository;
    private GetReservation $service;

    protected function setUp(): void
    {
        $this->reservationRepository = $this->createMock(ReservationRepository::class);
        $this->service = new GetReservationService($this->reservationRepository);
    }

    public function testExecuteReturnsReservation(): void
    {
        $reservationId = 'reservation-123';
        $command = new GetReservationCommand(reservationId: $reservationId);

        $reservation = Reservation::new(
            projectId: 'project-123',
            date: new \DateTimeImmutable('2024-12-25'),
            turn: Turn::H1900,
            name: 'John Doe',
            email: new Email('john@example.com'),
            phone: new Phone('+34-555-0100'),
            numberOfDiners: new Capacity(4),
            id: $reservationId
        );

        $this->reservationRepository->expects($this->once())
            ->method('exist')
            ->with($this->equalTo($reservationId))
            ->willReturn(true);

        $this->reservationRepository->expects($this->once())
            ->method('getById')
            ->with($this->equalTo($reservationId))
            ->willReturn($reservation);

        $result = $this->service->execute($command);

        $this->assertInstanceOf(Reservation::class, $result);
        $this->assertSame($reservationId, $result->getId());
    }

    public function testExecuteThrowsExceptionWhenReservationDoesNotExist(): void
    {
        $reservationId = 'reservation-123';
        $command = new GetReservationCommand(reservationId: $reservationId);

        $this->reservationRepository->expects($this->once())
            ->method('exist')
            ->with($this->equalTo($reservationId))
            ->willReturn(false);

        $this->expectException(ReservationDoesNotExist::class);

        $this->service->execute($command);
    }
}
