<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Reservations\ListReservations;

use Application\Reservations\ListReservations\ListReservations;
use Application\Reservations\ListReservations\ListReservationsCommand;
use Application\Reservations\ListReservations\ListReservationsService;
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
        $restaurantId = 'restaurant-123';
        $from = '2024-12-25';
        $offset = 0;

        $command = new ListReservationsCommand(
            restaurantId: $restaurantId,
            from: $from,
            offset: $offset
        );

        $expectedReservations = [];

        $this->reservationRepository->expects($this->once())
            ->method('findByRestaurantAndDateRange')
            ->with(
                $this->equalTo($restaurantId),
                $this->isInstanceOf(\DateTimeImmutable::class),
                $this->isInstanceOf(\DateTimeImmutable::class),
                $this->equalTo($offset),
                $this->equalTo(10)
            )
            ->willReturn($expectedReservations);

        $result = $this->service->execute($command);

        $this->assertCount(0, $result);
    }

    public function testExecuteHandlesInvalidDate(): void
    {
        $command = new ListReservationsCommand(
            restaurantId: 'restaurant-123',
            from: 'invalid-date',
            offset: 0
        );

        $this->reservationRepository->expects($this->once())
            ->method('findByRestaurantAndDateRange')
            ->willReturn([]);

        $result = $this->service->execute($command);

        $this->assertCount(0, $result);
    }
}
