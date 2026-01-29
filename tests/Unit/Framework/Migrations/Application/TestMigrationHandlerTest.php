<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Migrations\Application;

use Framework\Migrations\Application\TestMigrationCommand;
use Framework\Migrations\Application\TestMigrationHandler;
use Framework\Migrations\Domain\Entities\Migration;
use Framework\Migrations\Domain\Services\DatabaseBackupManager;
use Framework\Migrations\Domain\Services\MigrationFileManager;
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
    private TestMigrationExecutor&Stub $testMigrationExecutor;
    private RollbackExecutor&Stub $rollbackExecutor;
    private SchemaSnapshotExecutor&Stub $schemaSnapshotExecutor;
    private SchemaComparator&Stub $schemaComparator;
    private DatabaseBackupManager&Stub $databaseBackupService;
    private TestMigrationHandler $service;

    protected function setUp(): void
    {
        $this->migrationFileManager = $this->createStub(MigrationFileManager::class);
        $this->testMigrationExecutor = $this->createStub(TestMigrationExecutor::class);
        $this->rollbackExecutor = $this->createStub(RollbackExecutor::class);
        $this->schemaSnapshotExecutor = $this->createStub(SchemaSnapshotExecutor::class);
        $this->schemaComparator = $this->createStub(SchemaComparator::class);
        $this->databaseBackupService = $this->createStub(DatabaseBackupManager::class);

        $this->service = new TestMigrationHandler(
            $this->migrationFileManager,
            $this->testMigrationExecutor,
            $this->rollbackExecutor,
            $this->schemaSnapshotExecutor,
            $this->schemaComparator,
            $this->databaseBackupService,
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

        $this->migrationFileManager->method('getMigrationByName')
            ->with('/test/migrations', 'test_migration')
            ->willReturn($migration);

        $this->databaseBackupService->method('backup')
            ->willReturn($backupFilePath);

        $this->schemaSnapshotExecutor->method('capture')
            ->willReturnOnConsecutiveCalls($initialSnapshot, $finalSnapshot);

        $this->testMigrationExecutor->method('execute')
            ->with($migration);

        $this->rollbackExecutor->method('rollback')
            ->with($migration->scripts);

        $this->schemaComparator->method('compare')
            ->with($initialSnapshot, $finalSnapshot)
            ->willReturn($comparisonResult);

        $this->databaseBackupService->method('restore')
            ->with($backupFilePath);

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

        $this->migrationFileManager->method('getMigrationByName')
            ->with('/test/migrations', 'test_migration')
            ->willReturn($migration);

        $this->databaseBackupService->method('backup')
            ->willReturn($backupFilePath);

        $this->schemaSnapshotExecutor->method('capture')
            ->willReturnOnConsecutiveCalls($initialSnapshot, $finalSnapshot);

        $this->testMigrationExecutor->method('execute')
            ->with($migration);

        $this->rollbackExecutor->method('rollback')
            ->with($migration->scripts);

        $this->schemaComparator->method('compare')
            ->with($initialSnapshot, $finalSnapshot)
            ->willReturn($comparisonResult);

        $this->databaseBackupService->method('restore')
            ->with($backupFilePath);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Migration rollback test failed. Schema differences detected.');

        $this->service->execute($command);
    }

    public function testExecuteRestoresDatabaseOnError(): void
    {
        $migration = Migration::new('test_migration', []);
        $command = new TestMigrationCommand('test_migration', '/test/migrations');
        $backupFilePath = '/tmp/backup.sql';

        $this->migrationFileManager->method('getMigrationByName')
            ->with('/test/migrations', 'test_migration')
            ->willReturn($migration);

        $this->databaseBackupService->method('backup')
            ->willReturn($backupFilePath);

        $this->schemaSnapshotExecutor->method('capture')
            ->willThrowException(new \RuntimeException('Database error'));

        $this->databaseBackupService->method('restore')
            ->with($backupFilePath);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Database error');

        $this->service->execute($command);
    }

    public function testExecuteRestoresDatabaseWhenMigrationExecutionFails(): void
    {
        $migration = Migration::new('test_migration', []);
        $command = new TestMigrationCommand('test_migration', '/test/migrations');
        $initialSnapshot = SchemaSnapshot::new([]);
        $backupFilePath = '/tmp/backup.sql';

        $this->migrationFileManager->method('getMigrationByName')
            ->with('/test/migrations', 'test_migration')
            ->willReturn($migration);

        $this->databaseBackupService->method('backup')
            ->willReturn($backupFilePath);

        $this->schemaSnapshotExecutor->method('capture')
            ->willReturn($initialSnapshot);

        $this->testMigrationExecutor->method('execute')
            ->with($migration)
            ->willThrowException(new \RuntimeException('Migration execution failed'));

        $this->databaseBackupService->method('restore')
            ->with($backupFilePath);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Migration execution failed');

        $this->service->execute($command);
    }
}
