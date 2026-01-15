<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Application;

use Seedwork\Application\Logging\Logger;
use Seedwork\Infrastructure\Migrations\Domain\Entities\Migration;
use Seedwork\Infrastructure\Migrations\Domain\Exceptions\MigrationException;
use Seedwork\Infrastructure\Migrations\Domain\Services\MigrationExecutor;
use Seedwork\Infrastructure\Migrations\Domain\Services\MigrationFileManager;
use Seedwork\Infrastructure\Migrations\Domain\Repositories\MigrationRepository;
use Seedwork\Infrastructure\Migrations\Domain\Services\RollbackExecutor;

final readonly class RunMigrationsService implements RunMigrations
{
    public function __construct(
        private Logger $logger,
        private MigrationExecutor $migrationExecutor,
        private MigrationFileManager $migrationFileManager,
        private MigrationRepository $migrationRepository,
        private RollbackExecutor $rollbackExecutor,
    ) {
    }

    public function execute(string $basePath): void
    {
        $this->logger->info("Getting pending migrations...");
        $allMigrations = $this->migrationFileManager->getMigrations(basePath: $basePath);
        $executedMigrations = $this->migrationRepository->getMigrations();
        $executedMigrationNames = array_map(fn (Migration $migration) => $migration->name, $executedMigrations);
        $migrations =  array_filter($allMigrations, function (Migration $migration) use ($executedMigrationNames) {
            return !in_array($migration->name, $executedMigrationNames);
        });

        if (empty($migrations)) {
            $this->logger->info("No pending migrations found.");
            return;
        }

        try {
            $this->logger->info("Running migrations for all databases...");
            foreach ($migrations as $migration) {
                $this->migrationExecutor->execute($migration);
            }
            $this->logger->info("All migrations completed successfully.");
        } catch (MigrationException $e) {
            $this->logger->info("Error running migrations");
            foreach ($e->scripts as $script) {
                $this->logger->info("Rolling back script: " . $script->fileName);
            }
            $this->rollbackExecutor->rollback(scripts: $e->scripts);
            $this->logger->info("Rollback completed successfully.");
        }
    }
}
