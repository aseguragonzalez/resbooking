<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain\Services;

use Seedwork\Infrastructure\Migrations\Domain\Clients\DbClient;
use Seedwork\Infrastructure\Migrations\Domain\Entities\Migration;

final readonly class TestMigrationExecutorService implements TestMigrationExecutor
{
    public function __construct(private DbClient $dbClient)
    {
    }

    public function execute(Migration $migration): void
    {
        foreach ($migration->scripts as $script) {
            $this->dbClient->execute(statements: $script->getStatements());
        }
    }
}
