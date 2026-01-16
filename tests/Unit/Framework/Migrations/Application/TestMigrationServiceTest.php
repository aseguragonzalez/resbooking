<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Migrations\Application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Framework\Logging\Logger;
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

final class TestMigrationServiceTest extends TestCase
{
    private Logger&MockObject $logger;
    private MigrationFileManager&MockObject $migrationFileManager;
    private TestMigrationExecutor&MockObject $testMigrationExecutor;
    private RollbackExecutor&MockObject $rollbackExecutor;
    private SchemaSnapshotExecutor&MockObject $schemaSnapshotExecutor;
    private SchemaComparator&MockObject $schemaComparator;
    private DatabaseBackupManager&MockObject $databaseBackupService;
    private TestMigrationHandler $service;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(Logger::class);
        $this->migrationFileManager = $this->createMock(MigrationFileManager::class);
        $this->testMigrationExecutor = $this->createMock(TestMigrationExecutor::class);
        $this->rollbackExecutor = $this->createMock(RollbackExecutor::class);
        $this->schemaSnapshotExecutor = $this->createMock(SchemaSnapshotExecutor::class);
        $this->schemaComparator = $this->createMock(SchemaComparator::class);
        $this->databaseBackupService = $this->createMock(DatabaseBackupManager::class);

        $this->service = new TestMigrationHandler(
            $this->logger,
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

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrationByName')
            ->with('/test/migrations', 'test_migration')
            ->willReturn($migration);

        $this->databaseBackupService->expects($this->once())
            ->method('backup')
            ->willReturn($backupFilePath);

        $this->schemaSnapshotExecutor->expects($this->exactly(2))
            ->method('capture')
            ->willReturnOnConsecutiveCalls($initialSnapshot, $finalSnapshot);

        $this->testMigrationExecutor->expects($this->once())
            ->method('execute')
            ->with($migration);

        $this->rollbackExecutor->expects($this->once())
            ->method('rollback')
            ->with($migration->scripts);

        $this->schemaComparator->expects($this->once())
            ->method('compare')
            ->with($initialSnapshot, $finalSnapshot)
            ->willReturn($comparisonResult);

        $this->databaseBackupService->expects($this->once())
            ->method('restore')
            ->with($backupFilePath);

        $this->logger->expects($this->atLeastOnce())->method('info');

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

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrationByName')
            ->with('/test/migrations', 'test_migration')
            ->willReturn($migration);

        $this->databaseBackupService->expects($this->once())
            ->method('backup')
            ->willReturn($backupFilePath);

        $this->schemaSnapshotExecutor->expects($this->exactly(2))
            ->method('capture')
            ->willReturnOnConsecutiveCalls($initialSnapshot, $finalSnapshot);

        $this->testMigrationExecutor->expects($this->once())
            ->method('execute')
            ->with($migration);

        $this->rollbackExecutor->expects($this->once())
            ->method('rollback')
            ->with($migration->scripts);

        $this->schemaComparator->expects($this->once())
            ->method('compare')
            ->with($initialSnapshot, $finalSnapshot)
            ->willReturn($comparisonResult);

        $this->databaseBackupService->expects($this->once())
            ->method('restore')
            ->with($backupFilePath);

        $this->logger->expects($this->atLeastOnce())->method('info');
        $this->logger->expects($this->once())->method('error');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Migration rollback test failed. Schema differences detected.');

        $this->service->execute($command);
    }

    public function testExecuteRestoresDatabaseOnError(): void
    {
        $migration = Migration::new('test_migration', []);
        $command = new TestMigrationCommand('test_migration', '/test/migrations');
        $backupFilePath = '/tmp/backup.sql';

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrationByName')
            ->with('/test/migrations', 'test_migration')
            ->willReturn($migration);

        $this->databaseBackupService->expects($this->once())
            ->method('backup')
            ->willReturn($backupFilePath);

        $this->schemaSnapshotExecutor->expects($this->once())
            ->method('capture')
            ->willThrowException(new \RuntimeException('Database error'));

        $this->databaseBackupService->expects($this->once())
            ->method('restore')
            ->with($backupFilePath);

        $this->logger->expects($this->atLeastOnce())->method('info');
        $this->logger->expects($this->once())->method('error');

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

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrationByName')
            ->with('/test/migrations', 'test_migration')
            ->willReturn($migration);

        $this->databaseBackupService->expects($this->once())
            ->method('backup')
            ->willReturn($backupFilePath);

        $this->schemaSnapshotExecutor->expects($this->once())
            ->method('capture')
            ->willReturn($initialSnapshot);

        $this->testMigrationExecutor->expects($this->once())
            ->method('execute')
            ->with($migration)
            ->willThrowException(new \RuntimeException('Migration execution failed'));

        $this->databaseBackupService->expects($this->once())
            ->method('restore')
            ->with($backupFilePath);

        $this->logger->expects($this->atLeastOnce())->method('info');
        $this->logger->expects($this->once())->method('error');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Migration execution failed');

        $this->service->execute($command);
    }

    public function testExecuteRestoresDatabaseWhenBackupFails(): void
    {
        $migration = Migration::new('test_migration', []);
        $command = new TestMigrationCommand('test_migration', '/test/migrations');

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrationByName')
            ->with('/test/migrations', 'test_migration')
            ->willReturn($migration);

        $this->databaseBackupService->expects($this->once())
            ->method('backup')
            ->willThrowException(new \RuntimeException('Backup failed'));

        $this->databaseBackupService->expects($this->never())
            ->method('restore');

        $this->logger->expects($this->atLeastOnce())->method('info');
        $this->logger->expects($this->once())->method('error');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Backup failed');

        $this->service->execute($command);
    }
}
