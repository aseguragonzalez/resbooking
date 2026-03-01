<?php

declare(strict_types=1);

namespace Infrastructure\Adapters;

use PDO;
use SeedWork\Domain\UnitOfWork;

final readonly class PdoUnitOfWork implements UnitOfWork
{
    public function __construct(private PDO $db)
    {
    }

    public function createSession(): void
    {
        $this->db->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

    public function rollback(): void
    {
        $this->db->rollBack();
    }
}
