<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain\Services;

use Seedwork\Infrastructure\Migrations\Domain\Clients\DbClient;
use Seedwork\Infrastructure\Migrations\Domain\Entities\Migration;
use Seedwork\Infrastructure\Migrations\Domain\Exceptions\MigrationException;
use Seedwork\Infrastructure\Migrations\Domain\Repositories\MigrationRepository;

final readonly class MigrationExecutorService implements MigrationExecutor
{
    public function __construct(private MigrationRepository $repository, private DbClient $dbClient)
    {
    }

    public function execute(Migration $migration): void
    {
        $scripts = [];
        try {
            foreach ($migration->scripts as $script) {
                $scripts[] = $script;
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
