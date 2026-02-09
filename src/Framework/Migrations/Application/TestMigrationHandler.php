<?php

declare(strict_types=1);

namespace Framework\Migrations\Application;

use Framework\Migrations\Domain\Services\DatabaseBackupManager;
use Framework\Migrations\Domain\Services\MigrationFileManager;
use Framework\Migrations\Domain\Services\MigrationTestScopeFactory;
use Framework\Migrations\Domain\Services\SchemaComparator;

final readonly class TestMigrationHandler implements TestMigration
{
    public function __construct(
        private MigrationFileManager $migrationFileManager,
        private DatabaseBackupManager $databaseBackupManager,
        private MigrationTestScopeFactory $scopeFactory,
        private SchemaComparator $schemaComparator,
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

        $backupFilePath = $this->databaseBackupManager->backup();
        $testDbName = '';

        try {
            $testDbName = $this->databaseBackupManager->createTestDatabaseFromBackup($backupFilePath);
            $scope = $this->scopeFactory->createScope($testDbName);

            $initialSnapshot = $scope->schemaSnapshotExecutor->capture();
            $scope->testMigrationExecutor->execute($migration);
            $scope->rollbackExecutor->rollback($migration->scripts);
            $finalSnapshot = $scope->schemaSnapshotExecutor->capture();
            $comparisonResult = $this->schemaComparator->compare($initialSnapshot, $finalSnapshot);

            if (!$comparisonResult->areEqual) {
                throw new \RuntimeException("Migration rollback test failed. Schema differences detected.");
            }
        } finally {
            if ($testDbName !== '') {
                $this->databaseBackupManager->destroyTestDatabase($testDbName);
            }
            if (file_exists($backupFilePath)) {
                unlink($backupFilePath);
            }
        }
    }
}
