<?php

declare(strict_types=1);

namespace Framework\Mvc\Migrations\Domain\Services;

use Framework\Mvc\Migrations\Domain\Clients\DbClient;
use Framework\Mvc\Migrations\Domain\Entities\Migration;

final readonly class TestMigrationExecutorHandler implements TestMigrationExecutor
{
    public function __construct(private DbClient $dbClient, private string $databaseName)
    {
    }

    public function execute(Migration $migration): void
    {
        foreach ($migration->scripts as $script) {
            $this->dbClient->useDatabase($this->databaseName);
            $this->dbClient->execute(statements: $script->getStatements());
        }
    }
}
