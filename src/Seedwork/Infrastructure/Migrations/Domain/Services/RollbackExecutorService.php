<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain\Services;

use Seedwork\Infrastructure\Migrations\Domain\Clients\DbClient;
use Seedwork\Infrastructure\Migrations\Domain\Entities\Script;

final readonly class RollbackExecutorService implements RollbackExecutor
{
    public function __construct(private DbClient $dbClient)
    {
    }

    /**
     * @param array<Script> $scripts
     */
    public function rollback(array $scripts): void
    {
        $scripts_to_revert = array_reverse($scripts);
        foreach ($scripts_to_revert as $script) {
            $this->dbClient->execute(statements: $script->getRollbackStatements());
        }
    }
}
