<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reservations\ListReservations;

use Application\Reservations\ListReservations\ListReservations;
use Application\Reservations\ListReservations\ListReservationsCommand;
use Application\Reservations\ListReservations\ListReservationsService;
use Domain\Reservations\Entities\Reservation;
use Domain\Reservations\Repositories\ReservationRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ListReservationsTest extends TestCase
{
    private MockObject&ReservationRepository $reservationRepository;
    private ListReservations $service;

    protected function setUp(): void
    {
        $this->reservationRepository = $this->createMock(ReservationRepository::class);
        $this->service = new ListReservationsService($this->reservationRepository);
    }

    public function testExecuteReturnsReservations(): void
    {
        $projectId = 'project-123';
        $from = '2024-12-25';
        $offset = 0;

        $command = new ListReservationsCommand(
            projectId: $projectId,
            from: $from,
            offset: $offset
        );

        $expectedReservations = [];

        $this->reservationRepository->expects($this->once())
            ->method('findByProjectAndDateRange')
            ->with(
                $this->equalTo($projectId),
                $this->isInstanceOf(\DateTimeImmutable::class),
                $this->isInstanceOf(\DateTimeImmutable::class),
                $this->equalTo($offset),
                $this->equalTo(10)
            )
            ->willReturn($expectedReservations);

        $result = $this->service->execute($command);

        $this->assertIsArray($result);
    }

    public function testExecuteHandlesInvalidDate(): void
    {
        $command = new ListReservationsCommand(
            projectId: 'project-123',
            from: 'invalid-date',
            offset: 0
        );

        $this->reservationRepository->expects($this->once())
            ->method('findByProjectAndDateRange')
            ->willReturn([]);

        $result = $this->service->execute($command);

        $this->assertIsArray($result);
    }
}
