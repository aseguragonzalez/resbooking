<?php

declare(strict_types=1);

namespace Framework\BackgroundTasks\Infrastructure;

use Framework\BackgroundTasks\Domain\TransactionRunner;
use PDO;

final readonly class PdoTransactionRunner implements TransactionRunner
{
    public function __construct(private PDO $db)
    {
    }

    public function runInTransaction(\Closure $operation): void
    {
        $this->db->beginTransaction();

        try {
            $operation();
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
