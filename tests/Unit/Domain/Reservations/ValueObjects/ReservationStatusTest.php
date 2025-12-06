<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reservations\ValueObjects;

use Domain\Reservations\ValueObjects\ReservationStatus;
use PHPUnit\Framework\TestCase;

final class ReservationStatusTest extends TestCase
{
    public function testReservationStatusHasAllCases(): void
    {
        $this->assertInstanceOf(ReservationStatus::class, ReservationStatus::PENDING);
        $this->assertInstanceOf(ReservationStatus::class, ReservationStatus::ACCEPTED);
        $this->assertInstanceOf(ReservationStatus::class, ReservationStatus::CANCELLED);
    }

    public function testReservationStatusValues(): void
    {
        $this->assertSame('PENDING', ReservationStatus::PENDING->value);
        $this->assertSame('ACCEPTED', ReservationStatus::ACCEPTED->value);
        $this->assertSame('CANCELLED', ReservationStatus::CANCELLED->value);
    }

    public function testReservationStatusFromString(): void
    {
        $this->assertSame(ReservationStatus::PENDING, ReservationStatus::from('PENDING'));
        $this->assertSame(ReservationStatus::ACCEPTED, ReservationStatus::from('ACCEPTED'));
        $this->assertSame(ReservationStatus::CANCELLED, ReservationStatus::from('CANCELLED'));
    }
}
