<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reservations\CreateReservation;

use Application\Reservations\CreateReservation\CreateReservation;
use Application\Reservations\CreateReservation\CreateReservationCommand;
use Application\Reservations\CreateReservation\CreateReservationService;
use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Repositories\ReservationRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateReservationTest extends TestCase
{
    private MockObject&ReservationRepository $reservationRepository;
    private CreateReservation $service;

    protected function setUp(): void
    {
        $this->reservationRepository = $this->createMock(ReservationRepository::class);
        $this->service = new CreateReservationService($this->reservationRepository);
    }

    public function testExecuteCreatesAndSavesReservation(): void
    {
        $command = new CreateReservationCommand(
            restaurantId: 'restaurant-123',
            date: '2024-12-25',
            turn: 15,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '+34-555-0100',
            numberOfDiners: 4
        );

        $this->reservationRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($reservation) {
                return $reservation instanceof Reservation;
            }));

        $this->service->execute($command);
    }

    public function testExecuteHandlesInvalidDate(): void
    {
        $command = new CreateReservationCommand(
            restaurantId: 'restaurant-123',
            date: 'invalid-date',
            turn: 15,
            name: 'John Doe',
            email: 'john@example.com',
            phone: '+34-555-0100',
            numberOfDiners: 4
        );

        $this->reservationRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Reservation::class));

        $this->service->execute($command);
    }
}
