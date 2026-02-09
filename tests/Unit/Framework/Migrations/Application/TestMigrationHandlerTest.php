<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Migrations\Application;

use Framework\Migrations\Application\TestMigrationCommand;
use Framework\Migrations\Application\TestMigrationHandler;
use Framework\Migrations\Domain\Entities\Migration;
use Framework\Migrations\Domain\Services\DatabaseBackupManager;
use Framework\Migrations\Domain\Services\MigrationFileManager;
use Framework\Migrations\Domain\Services\MigrationTestScope;
use Framework\Migrations\Domain\Services\MigrationTestScopeFactory;
use Framework\Migrations\Domain\Services\RollbackExecutor;
use Framework\Migrations\Domain\Services\SchemaComparator;
use Framework\Migrations\Domain\Services\SchemaComparisonResult;
use Framework\Migrations\Domain\Services\SchemaSnapshotExecutor;
use Framework\Migrations\Domain\Services\TestMigrationExecutor;
use Framework\Migrations\Domain\ValueObjects\SchemaSnapshot;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class TestMigrationHandlerTest extends TestCase
{
    private MigrationFileManager&Stub $migrationFileManager;
    private DatabaseBackupManager&Stub $databaseBackupManager;
    private MigrationTestScopeFactory&Stub $scopeFactory;
    private SchemaComparator&Stub $schemaComparator;
    private TestMigrationHandler $service;

    protected function setUp(): void
    {
        $this->migrationFileManager = $this->createStub(MigrationFileManager::class);
        $this->databaseBackupManager = $this->createStub(DatabaseBackupManager::class);
        $this->scopeFactory = $this->createStub(MigrationTestScopeFactory::class);
        $this->schemaComparator = $this->createStub(SchemaComparator::class);

        $this->service = new TestMigrationHandler(
            $this->migrationFileManager,
            $this->databaseBackupManager,
            $this->scopeFactory,
            $this->schemaComparator,
        );
    }

    public function testExecuteThrowsExceptionWhenMigrationNotFound(): void
    {
        $command = new TestMigrationCommand('non_existent', '/test/migrations');
        $this->migrationFileManager->method('getMigrationByName')
            ->with('/test/migrations', 'non_existent')
            ->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Migration 'non_existent' not found");

        $this->service->execute($command);
    }

    public function testExecuteSuccessfullyTestsMigrationWhenSchemasMatch(): void
    {
        $migration = Migration::new('test_migration', []);
        $command = new TestMigrationCommand('test_migration', '/test/migrations');
        $initialSnapshot = SchemaSnapshot::new([]);
        $finalSnapshot = SchemaSnapshot::new([]);
        $comparisonResult = SchemaComparisonResult::new(true);
        $backupFilePath = '/tmp/backup.sql';
        $testDbName = 'test_abc123';

        $schemaSnapshotExecutor = $this->createStub(SchemaSnapshotExecutor::class);
        $schemaSnapshotExecutor->method('capture')
            ->willReturnOnConsecutiveCalls($initialSnapshot, $finalSnapshot);
        $testMigrationExecutor = $this->createStub(TestMigrationExecutor::class);
        $testMigrationExecutor->method('execute')->with($migration);
        $rollbackExecutor = $this->createStub(RollbackExecutor::class);
        $rollbackExecutor->method('rollback')->with($migration->scripts);
        $scope = new MigrationTestScope(
            schemaSnapshotExecutor: $schemaSnapshotExecutor,
            testMigrationExecutor: $testMigrationExecutor,
            rollbackExecutor: $rollbackExecutor,
        );

        $this->migrationFileManager->method('getMigrationByName')
            ->with('/test/migrations', 'test_migration')
            ->willReturn($migration);
        $this->databaseBackupManager->method('backup')->willReturn($backupFilePath);
        $this->databaseBackupManager->method('createTestDatabaseFromBackup')
            ->with($backupFilePath)->willReturn($testDbName);
        $this->databaseBackupManager->method('destroyTestDatabase')->with($testDbName);
        $this->scopeFactory->method('createScope')->with($testDbName)->willReturn($scope);
        $this->schemaComparator->method('compare')
            ->with($initialSnapshot, $finalSnapshot)
            ->willReturn($comparisonResult);

        $this->service->execute($command);
    }

    public function testExecuteThrowsExceptionWhenSchemasDiffer(): void
    {
        $migration = Migration::new('test_migration', []);
        $command = new TestMigrationCommand('test_migration', '/test/migrations');
        $initialSnapshot = SchemaSnapshot::new([]);
        $finalSnapshot = SchemaSnapshot::new([]);
        $comparisonResult = SchemaComparisonResult::new(false, ['Table users was added']);
        $backupFilePath = '/tmp/backup.sql';
        $testDbName = 'test_abc123';

        $schemaSnapshotExecutor = $this->createStub(SchemaSnapshotExecutor::class);
        $schemaSnapshotExecutor->method('capture')
            ->willReturnOnConsecutiveCalls($initialSnapshot, $finalSnapshot);
        $scope = new MigrationTestScope(
            schemaSnapshotExecutor: $schemaSnapshotExecutor,
            testMigrationExecutor: $this->createStub(TestMigrationExecutor::class),
            rollbackExecutor: $this->createStub(RollbackExecutor::class),
        );

        $this->migrationFileManager->method('getMigrationByName')
            ->with('/test/migrations', 'test_migration')
            ->willReturn($migration);
        $this->databaseBackupManager->method('backup')->willReturn($backupFilePath);
        $this->databaseBackupManager->method('createTestDatabaseFromBackup')
            ->with($backupFilePath)->willReturn($testDbName);
        $this->databaseBackupManager->method('destroyTestDatabase')->with($testDbName);
        $this->scopeFactory->method('createScope')->with($testDbName)->willReturn($scope);
        $this->schemaComparator->method('compare')
            ->with($initialSnapshot, $finalSnapshot)
            ->willReturn($comparisonResult);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Migration rollback test failed. Schema differences detected.');

        $this->service->execute($command);
    }

    public function testExecuteDestroysTestDatabaseOnError(): void
    {
        $migration = Migration::new('test_migration', []);
        $command = new TestMigrationCommand('test_migration', '/test/migrations');
        $backupFilePath = '/tmp/backup.sql';
        $testDbName = 'test_abc123';

        $schemaSnapshotExecutor = $this->createStub(SchemaSnapshotExecutor::class);
        $schemaSnapshotExecutor->method('capture')
            ->willThrowException(new \RuntimeException('Database error'));
        $scope = new MigrationTestScope(
            schemaSnapshotExecutor: $schemaSnapshotExecutor,
            testMigrationExecutor: $this->createStub(TestMigrationExecutor::class),
            rollbackExecutor: $this->createStub(RollbackExecutor::class),
        );

        $this->migrationFileManager->method('getMigrationByName')
            ->with('/test/migrations', 'test_migration')
            ->willReturn($migration);
        $this->databaseBackupManager->method('backup')->willReturn($backupFilePath);
        $this->databaseBackupManager->method('createTestDatabaseFromBackup')
            ->with($backupFilePath)->willReturn($testDbName);
        $this->databaseBackupManager->method('destroyTestDatabase')->with($testDbName);
        $this->scopeFactory->method('createScope')->with($testDbName)->willReturn($scope);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Database error');

        $this->service->execute($command);
    }

    public function testExecuteDestroysTestDatabaseWhenMigrationExecutionFails(): void
    {
        $migration = Migration::new('test_migration', []);
        $command = new TestMigrationCommand('test_migration', '/test/migrations');
        $initialSnapshot = SchemaSnapshot::new([]);
        $backupFilePath = '/tmp/backup.sql';
        $testDbName = 'test_abc123';

        $schemaSnapshotExecutor = $this->createStub(SchemaSnapshotExecutor::class);
        $schemaSnapshotExecutor->method('capture')->willReturn($initialSnapshot);
        $testMigrationExecutor = $this->createStub(TestMigrationExecutor::class);
        $testMigrationExecutor->method('execute')->with($migration)
            ->willThrowException(new \RuntimeException('Migration execution failed'));
        $scope = new MigrationTestScope(
            schemaSnapshotExecutor: $schemaSnapshotExecutor,
            testMigrationExecutor: $testMigrationExecutor,
            rollbackExecutor: $this->createStub(RollbackExecutor::class),
        );

        $this->migrationFileManager->method('getMigrationByName')
            ->with('/test/migrations', 'test_migration')
            ->willReturn($migration);
        $this->databaseBackupManager->method('backup')->willReturn($backupFilePath);
        $this->databaseBackupManager->method('createTestDatabaseFromBackup')
            ->with($backupFilePath)->willReturn($testDbName);
        $this->databaseBackupManager->method('destroyTestDatabase')->with($testDbName);
        $this->scopeFactory->method('createScope')->with($testDbName)->willReturn($scope);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Migration execution failed');

        $this->service->execute($command);
    }
}
