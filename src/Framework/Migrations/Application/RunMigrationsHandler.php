<?php

declare(strict_types=1);

namespace Framework\Migrations\Application;

use Framework\Migrations\Domain\Entities\Migration;
use Framework\Migrations\Domain\Exceptions\MigrationException;
use Framework\Migrations\Domain\Repositories\MigrationRepository;
use Framework\Migrations\Domain\Services\MigrationExecutor;
use Framework\Migrations\Domain\Services\MigrationFileManager;
use Framework\Migrations\Domain\Services\RollbackExecutor;

final readonly class RunMigrationsHandler implements RunMigrations
{
    public function __construct(
        private MigrationExecutor $migrationExecutor,
        private MigrationFileManager $migrationFileManager,
        private MigrationRepository $migrationRepository,
        private RollbackExecutor $rollbackExecutor,
    ) {
    }

    public function execute(string $basePath): void
    {
        $allMigrations = $this->migrationFileManager->getMigrations(basePath: $basePath);
        $executedMigrations = $this->migrationRepository->getMigrations();
        $executedMigrationNames = array_map(fn (Migration $migration) => $migration->name, $executedMigrations);
        $migrations =  array_filter($allMigrations, function (Migration $migration) use ($executedMigrationNames) {
            return !in_array($migration->name, $executedMigrationNames);
        });

        if (empty($migrations)) {
            return;
        }

        try {
            foreach ($migrations as $migration) {
                $this->migrationExecutor->execute($migration);
            }
        } catch (MigrationException $e) {
            $this->rollbackExecutor->rollback(scripts: $e->scripts);
        }
    }
}
