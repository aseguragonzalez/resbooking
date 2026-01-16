<?php

declare(strict_types=1);

namespace Framework\Migrations\Infrastructure;

use Framework\Migrations\Domain\Clients\DbClient;
use PDO;

final readonly class SqlDbClient implements DbClient
{
    public function __construct(private PDO $db)
    {
    }

    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

    /**
     * @param array<string> $statements SQL statements to execute
     */
    public function execute(array $statements): void
    {
        foreach ($statements as $statement) {
            $this->db->exec($statement);
        }
    }

    public function inTransaction(): bool
    {
        return $this->db->inTransaction();
    }

    public function rollBack(): void
    {
        $this->db->rollBack();
    }
}
