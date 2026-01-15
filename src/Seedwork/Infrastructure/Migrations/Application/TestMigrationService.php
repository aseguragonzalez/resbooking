<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Application;

use Seedwork\Application\Logging\Logger;
use Seedwork\Infrastructure\Migrations\Domain\Services\DatabaseBackupManager;
use Seedwork\Infrastructure\Migrations\Domain\Services\MigrationFileManager;
use Seedwork\Infrastructure\Migrations\Domain\Services\RollbackExecutor;
use Seedwork\Infrastructure\Migrations\Domain\Services\SchemaComparator;
use Seedwork\Infrastructure\Migrations\Domain\Services\SchemaSnapshotExecutor;
use Seedwork\Infrastructure\Migrations\Domain\Services\TestMigrationExecutor;

final readonly class TestMigrationService implements TestMigration
{
    public function __construct(
        private Logger $logger,
        private MigrationFileManager $migrationFileManager,
        private TestMigrationExecutor $testMigrationExecutor,
        private RollbackExecutor $rollbackExecutor,
        private SchemaSnapshotExecutor $schemaSnapshotExecutor,
        private SchemaComparator $schemaComparator,
        private DatabaseBackupManager $databaseBackupService,
    ) {
    }

    public function execute(TestMigrationCommand $command): void
    {
        $this->logger->info("Testing migration: {$command->migrationName}");

        $migration = $this->migrationFileManager->getMigrationByName(
            $command->basePath,
            $command->migrationName
        );

        if ($migration === null) {
            throw new \RuntimeException("Migration '{$command->migrationName}' not found");
        }

        $backupFilePath = null;
        try {
            // Create database backup
            $this->logger->info("Creating database backup...");
            $backupFilePath = $this->databaseBackupService->backup();

            // Capture initial schema
            $this->logger->info("Capturing initial schema...");
            $initialSnapshot = $this->schemaSnapshotExecutor->capture();

            // Run migration
            $this->logger->info("Running migration...");
            $this->testMigrationExecutor->execute($migration);

            // Run rollback
            $this->logger->info("Running rollback...");
            $this->rollbackExecutor->rollback($migration->scripts);

            // Capture final schema
            $this->logger->info("Capturing final schema...");
            $finalSnapshot = $this->schemaSnapshotExecutor->capture();

            // Compare schemas
            $this->logger->info("Comparing schemas...");
            $comparisonResult = $this->schemaComparator->compare($initialSnapshot, $finalSnapshot);

            if ($comparisonResult->areEqual) {
                $this->logger->info("✓ Migration test passed: Schema matches after rollback");
            } else {
                $this->logger->info("✗ Migration test failed: Schema differences detected");
                foreach ($comparisonResult->differences as $difference) {
                    $this->logger->info("  - {$difference}");
                }
                throw new \RuntimeException("Migration rollback test failed. Schema differences detected.");
            }
        } catch (\Throwable $e) {
            $this->logger->error("Migration test failed with error: {$e->getMessage()}", $e);
            throw $e;
        } finally {
            // Always restore database from backup
            if ($backupFilePath !== null) {
                $this->logger->info("Restoring database from backup...");
                try {
                    $this->databaseBackupService->restore($backupFilePath);
                } catch (\Throwable $restoreError) {
                    $this->logger->error("Failed to restore database: {$restoreError->getMessage()}", $restoreError);
                    throw $restoreError;
                }
            }
        }
    }
}
