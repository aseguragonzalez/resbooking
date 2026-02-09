<?php

declare(strict_types=1);

namespace Framework\Migrations\Domain\Services;

use Framework\Migrations\Domain\Clients\DbClient;
use Framework\Migrations\Domain\Entities\Migration;
use Framework\Migrations\Domain\Exceptions\MigrationException;
use Framework\Migrations\Domain\Repositories\MigrationRepository;

final readonly class MigrationExecutorHandler implements MigrationExecutor
{
    public function __construct(
        private MigrationRepository $repository,
        private DbClient $dbClient,
        private string $databaseName,
    ) {
    }

    public function execute(Migration $migration): void
    {
        $scripts = [];
        try {
            foreach ($migration->scripts as $script) {
                $scripts[] = $script;
                $this->dbClient->useDatabase($this->databaseName);
                $this->dbClient->execute(statements: $script->getStatements());
            }

            $this->dbClient->beginTransaction();
            $this->repository->save($migration);
            $this->dbClient->commit();
        } catch (\Throwable $e) {
            if ($this->dbClient->inTransaction()) {
                $this->dbClient->rollBack();
            }

            throw new MigrationException(scripts: $scripts, message: $e->getMessage());
        }
    }
}
