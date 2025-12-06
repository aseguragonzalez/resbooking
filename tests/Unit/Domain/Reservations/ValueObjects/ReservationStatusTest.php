<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reservations\ValueObjects;

use Domain\Reservations\ValueObjects\ReservationStatus;
use PHPUnit\Framework\TestCase;

final class ReservationStatusTest extends TestCase
{
    public function testReservationStatusHasAllCases(): void
    {
        $this->assertInstanceOf(ReservationStatus::class, ReservationStatus::Pending);
        $this->assertInstanceOf(ReservationStatus::class, ReservationStatus::Accepted);
        $this->assertInstanceOf(ReservationStatus::class, ReservationStatus::Cancelled);
    }

    public function testReservationStatusValues(): void
    {
        $this->assertSame('PENDING', ReservationStatus::Pending->value);
        $this->assertSame('ACCEPTED', ReservationStatus::Accepted->value);
        $this->assertSame('CANCELLED', ReservationStatus::Cancelled->value);
    }

    public function testReservationStatusFromString(): void
    {
        $this->assertSame(ReservationStatus::Pending, ReservationStatus::from('PENDING'));
        $this->assertSame(ReservationStatus::Accepted, ReservationStatus::from('ACCEPTED'));
        $this->assertSame(ReservationStatus::Cancelled, ReservationStatus::from('CANCELLED'));
    }
}
