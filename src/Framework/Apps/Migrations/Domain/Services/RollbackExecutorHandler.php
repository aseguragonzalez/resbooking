<?php

declare(strict_types=1);

namespace Framework\Migrations\Domain\Services;

use Framework\Migrations\Domain\Clients\DbClient;
use Framework\Migrations\Domain\Entities\Script;

final readonly class RollbackExecutorHandler implements RollbackExecutor
{
    public function __construct(private DbClient $dbClient, private string $databaseName)
    {
    }

    /**
     * @param array<Script> $scripts
     */
    public function rollback(array $scripts): void
    {
        $scriptsToRevert = array_reverse($scripts);
        foreach ($scriptsToRevert as $script) {
            $this->dbClient->useDatabase($this->databaseName);
            $this->dbClient->execute(statements: $script->getRollbackStatements());
        }
    }
}
