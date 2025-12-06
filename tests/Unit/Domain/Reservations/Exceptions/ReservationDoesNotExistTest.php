<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reservations\Exceptions;

use Domain\Reservations\Exceptions\ReservationDoesNotExist;
use PHPUnit\Framework\TestCase;
use Seedwork\Domain\Exceptions\DomainException;

final class ReservationDoesNotExistTest extends TestCase
{
    public function testReservationDoesNotExistExtendsDomainException(): void
    {
        $exception = new ReservationDoesNotExist();

        $this->assertInstanceOf(DomainException::class, $exception);
        $this->assertSame('Reservation does not exist', $exception->getMessage());
    }
}
