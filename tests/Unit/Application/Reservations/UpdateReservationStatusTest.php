<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reservations\UpdateReservationStatus;

use Application\Reservations\UpdateReservationStatus\UpdateReservationStatus;
use Application\Reservations\UpdateReservationStatus\UpdateReservationStatusCommand;
use Application\Reservations\UpdateReservationStatus\UpdateReservationStatusService;
use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Repositories\ReservationRepository;
use Domain\Reservations\Services\ReservationObtainer;
use Domain\Reservations\ValueObjects\ReservationStatus;
use Domain\Shared\Capacity;
use Domain\Shared\Email;
use Domain\Shared\Phone;
use Domain\Shared\Turn;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateReservationStatusTest extends TestCase
{
    private MockObject&ReservationRepository $reservationRepository;
    private MockObject&ReservationObtainer $reservationObtainer;
    private UpdateReservationStatus $service;

    protected function setUp(): void
    {
        $this->reservationRepository = $this->createMock(ReservationRepository::class);
        $this->reservationObtainer = $this->createMock(ReservationObtainer::class);
        $this->service = new UpdateReservationStatusService($this->reservationObtainer, $this->reservationRepository);
    }

    public function testExecuteUpdatesStatusAndSavesReservation(): void
    {
        $reservationId = 'reservation-123';
        $command = new UpdateReservationStatusCommand(
            reservationId: $reservationId,
            status: 'ACCEPTED'
        );

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

        $this->reservationObtainer->expects($this->once())
            ->method('obtain')
            ->with($this->equalTo($reservationId))
            ->willReturn($reservation);

        $this->reservationRepository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($reservation));

        $this->service->execute($command);

        $this->assertSame(ReservationStatus::Accepted, $reservation->getStatus());
    }
}
