<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reservations\Repositories;

use Domain\Reservations\Repositories\ReservationRepository;
use PHPUnit\Framework\TestCase;
use Seedwork\Domain\Repository;

final class ReservationRepositoryTest extends TestCase
{
    public function testReservationRepositoryExtendsRepository(): void
    {
        $this->assertTrue(
            is_subclass_of(ReservationRepository::class, Repository::class)
        );
    }
}
