<?php

declare(strict_types=1);

namespace Domain\Reservations\Repositories;

use Domain\Reservations\Entities\Reservation;
use Seedwork\Domain\Repository;

/**
 * @extends Repository<Reservation>
 */
interface ReservationRepository extends Repository
{
    /**
     * Find reservations by project ID and date range
     *
     * @param string $projectId
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     * @param int $offset
     * @param int $limit
     * @return array<Reservation>
     */
    public function findByProjectAndDateRange(
        string $projectId,
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
        int $offset = 0,
        int $limit = 10
    ): array;
}
