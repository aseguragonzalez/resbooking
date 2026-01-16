<?php

declare(strict_types=1);

namespace Framework\Migrations\Application;

use Framework\Migrations\Domain\Services\DatabaseBackupManager;
use Framework\Migrations\Domain\Services\MigrationFileManager;
use Framework\Migrations\Domain\Services\RollbackExecutor;
use Framework\Migrations\Domain\Services\SchemaComparator;
use Framework\Migrations\Domain\Services\SchemaSnapshotExecutor;
use Framework\Migrations\Domain\Services\TestMigrationExecutor;

final readonly class TestMigrationHandler implements TestMigration
{
    public function __construct(
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
        $migration = $this->migrationFileManager->getMigrationByName(
            $command->basePath,
            $command->migrationName
        );

        if ($migration === null) {
            throw new \RuntimeException("Migration '{$command->migrationName}' not found");
        }

        $backupFilePath = "";

        try {
            $backupFilePath = $this->databaseBackupService->backup();
            $initialSnapshot = $this->schemaSnapshotExecutor->capture();
            $this->testMigrationExecutor->execute($migration);

            $this->rollbackExecutor->rollback($migration->scripts);
            $finalSnapshot = $this->schemaSnapshotExecutor->capture();
            $comparisonResult = $this->schemaComparator->compare($initialSnapshot, $finalSnapshot);

            if (!$comparisonResult->areEqual) {
                throw new \RuntimeException("Migration rollback test failed. Schema differences detected.");
            }
        } finally {
            $this->databaseBackupService->restore($backupFilePath);
        }
    }
}
