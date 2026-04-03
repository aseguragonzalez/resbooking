<?php

declare(strict_types=1);

namespace Framework\Mvc\Migrations;

/**
 * MySQL connection parameters registered by the migrations instance bootstrap (PDO + CLI tools).
 */
final readonly class MigrationsMysqlConnection
{
    public function __construct(
        public string $host,
        public string $database,
        public string $user,
        public string $password,
        public string $charset = 'utf8mb4',
    ) {
    }

    public function getPdoDsn(): string
    {
        return "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
    }
}
