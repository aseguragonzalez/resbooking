<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Migrations\Application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Framework\Logging\Logger;
use Framework\Migrations\Application\RunMigrationsHandler;
use Framework\Migrations\Domain\Clients\DbClient;
use Framework\Migrations\Domain\Entities\Migration;
use Framework\Migrations\Domain\Entities\Script;
use Framework\Migrations\Domain\Exceptions\MigrationException;
use Framework\Migrations\Domain\Services\MigrationExecutor;
use Framework\Migrations\Domain\Services\MigrationFileManager;
use Framework\Migrations\Domain\Repositories\MigrationRepository;
use Framework\Migrations\Domain\Services\RollbackExecutor;

final class RunMigrationsServiceTest extends TestCase
{
    private Logger&MockObject $logger;
    private MigrationExecutor&MockObject $migrationExecutor;
    private MigrationFileManager&MockObject $migrationFileManager;
    private MigrationRepository&MockObject $migrationRepository;
    private RollbackExecutor&MockObject $rollbackExecutor;
    private RunMigrationsHandler $service;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(Logger::class);
        $this->migrationExecutor = $this->createMock(MigrationExecutor::class);
        $this->migrationFileManager = $this->createMock(MigrationFileManager::class);
        $this->migrationRepository = $this->createMock(MigrationRepository::class);
        $this->rollbackExecutor = $this->createMock(RollbackExecutor::class);
        $this->service = new RunMigrationsHandler(
            $this->logger,
            $this->migrationExecutor,
            $this->migrationFileManager,
            $this->migrationRepository,
            $this->rollbackExecutor
        );
    }

    public function testExecuteGetsAllMigrationsFromFileManager(): void
    {
        $basePath = '/migrations';
        $allMigrations = [
            Migration::new(name: '20240115', scripts: []),
            Migration::new(name: '20240116', scripts: []),
        ];

        $logMessages = [];
        $this->logger->expects($this->exactly(3))
            ->method('info')
            ->willReturnCallback(function (string $message) use (&$logMessages): void {
                $logMessages[] = $message;
            });

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrations')
            ->with($basePath)
            ->willReturn($allMigrations);

        $this->migrationRepository->expects($this->once())
            ->method('getMigrations')
            ->willReturn([]);

        $this->migrationExecutor->expects($this->exactly(2))
            ->method('execute');

        $this->service->execute($basePath);

        $this->assertSame(
            [
                'Getting pending migrations...',
                'Running migrations for all databases...',
                'All migrations completed successfully.',
            ],
            $logMessages
        );
    }

    public function testExecuteGetsExecutedMigrationsFromRepository(): void
    {
        $basePath = '/migrations';
        $allMigrations = [
            Migration::new(name: '20240115', scripts: []),
        ];
        $executedMigrations = [
            Migration::new(name: '20240114', scripts: []),
        ];

        $logMessages = [];
        $this->logger->expects($this->exactly(3))
            ->method('info')
            ->willReturnCallback(function (string $message) use (&$logMessages): void {
                $logMessages[] = $message;
            });

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrations')
            ->with($basePath)
            ->willReturn($allMigrations);

        $this->migrationRepository->expects($this->once())
            ->method('getMigrations')
            ->willReturn($executedMigrations);

        $this->migrationExecutor->expects($this->once())
            ->method('execute');

        $this->service->execute($basePath);

        $this->assertSame(
            [
                'Getting pending migrations...',
                'Running migrations for all databases...',
                'All migrations completed successfully.',
            ],
            $logMessages
        );
    }

    public function testExecuteFiltersOutAlreadyExecutedMigrations(): void
    {
        $basePath = '/migrations';
        $allMigrations = [
            Migration::new(name: '20240115', scripts: []),
            Migration::new(name: '20240116', scripts: []),
            Migration::new(name: '20240117', scripts: []),
        ];
        $executedMigrations = [
            Migration::new(name: '20240115', scripts: []),
            Migration::new(name: '20240116', scripts: []),
        ];

        $logMessages = [];
        $this->logger->expects($this->exactly(3))
            ->method('info')
            ->willReturnCallback(function (string $message) use (&$logMessages): void {
                $logMessages[] = $message;
            });

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrations')
            ->with($basePath)
            ->willReturn($allMigrations);

        $this->migrationRepository->expects($this->once())
            ->method('getMigrations')
            ->willReturn($executedMigrations);

        $this->migrationExecutor->expects($this->once())
            ->method('execute')
            ->with($this->callback(function (Migration $migration) {
                return $migration->name === '20240117';
            }));

        $this->service->execute($basePath);

        $this->assertSame(
            [
                'Getting pending migrations...',
                'Running migrations for all databases...',
                'All migrations completed successfully.',
            ],
            $logMessages
        );
    }

    public function testExecuteReturnsEarlyWhenNoPendingMigrations(): void
    {
        $basePath = '/migrations';
        $allMigrations = [
            Migration::new(name: '20240115', scripts: []),
        ];
        $executedMigrations = [
            Migration::new(name: '20240115', scripts: []),
        ];

        $logMessages = [];
        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->willReturnCallback(function (string $message) use (&$logMessages): void {
                $logMessages[] = $message;
            });

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrations')
            ->with($basePath)
            ->willReturn($allMigrations);

        $this->migrationRepository->expects($this->once())
            ->method('getMigrations')
            ->willReturn($executedMigrations);

        $this->migrationExecutor->expects($this->never())
            ->method('execute');

        $this->service->execute($basePath);

        $this->assertSame(
            [
                'Getting pending migrations...',
                'No pending migrations found.',
            ],
            $logMessages
        );
    }

    public function testExecuteLogsGettingPendingMigrationsAtStart(): void
    {
        $basePath = '/migrations';

        $logMessages = [];
        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->willReturnCallback(function (string $message) use (&$logMessages): void {
                $logMessages[] = $message;
            });

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrations')
            ->willReturn([]);

        $this->migrationRepository->expects($this->once())
            ->method('getMigrations')
            ->willReturn([]);

        $this->service->execute($basePath);

        $this->assertSame('Getting pending migrations...', $logMessages[0]);
        $this->assertSame('No pending migrations found.', $logMessages[1]);
    }

    public function testExecuteLogsNoPendingMigrationsFoundWhenEmpty(): void
    {
        $basePath = '/migrations';

        $logMessages = [];
        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->willReturnCallback(function (string $message) use (&$logMessages): void {
                $logMessages[] = $message;
            });

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrations')
            ->willReturn([]);

        $this->migrationRepository->expects($this->once())
            ->method('getMigrations')
            ->willReturn([]);

        $this->service->execute($basePath);

        $this->assertSame(
            [
                'Getting pending migrations...',
                'No pending migrations found.',
            ],
            $logMessages
        );
    }

    public function testExecuteLogsRunningMigrationsForAllDatabases(): void
    {
        $basePath = '/migrations';
        $allMigrations = [
            Migration::new(name: '20240115', scripts: []),
        ];

        $logMessages = [];
        $this->logger->expects($this->exactly(3))
            ->method('info')
            ->willReturnCallback(function (string $message) use (&$logMessages): void {
                $logMessages[] = $message;
            });

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrations')
            ->willReturn($allMigrations);

        $this->migrationRepository->expects($this->once())
            ->method('getMigrations')
            ->willReturn([]);

        $this->migrationExecutor->expects($this->once())
            ->method('execute');

        $this->service->execute($basePath);

        $this->assertSame('Running migrations for all databases...', $logMessages[1]);
    }

    public function testExecuteExecutesAllPendingMigrations(): void
    {
        $basePath = '/migrations';
        $allMigrations = [
            Migration::new(name: '20240115', scripts: []),
            Migration::new(name: '20240116', scripts: []),
            Migration::new(name: '20240117', scripts: []),
        ];

        $executedMigrations = [];
        $this->migrationExecutor->expects($this->exactly(3))
            ->method('execute')
            ->willReturnCallback(function (Migration $migration) use (&$executedMigrations): void {
                $executedMigrations[] = $migration->name;
            });

        $this->logger->expects($this->atLeastOnce())
            ->method('info');

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrations')
            ->willReturn($allMigrations);

        $this->migrationRepository->expects($this->once())
            ->method('getMigrations')
            ->willReturn([]);

        $this->service->execute($basePath);

        $this->assertSame(['20240115', '20240116', '20240117'], $executedMigrations);
    }

    public function testExecuteLogsAllMigrationsCompletedSuccessfullyOnSuccess(): void
    {
        $basePath = '/migrations';
        $allMigrations = [
            Migration::new(name: '20240115', scripts: []),
        ];

        $logMessages = [];
        $this->logger->expects($this->exactly(3))
            ->method('info')
            ->willReturnCallback(function (string $message) use (&$logMessages): void {
                $logMessages[] = $message;
            });

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrations')
            ->willReturn($allMigrations);

        $this->migrationRepository->expects($this->once())
            ->method('getMigrations')
            ->willReturn([]);

        $this->migrationExecutor->expects($this->once())
            ->method('execute');

        $this->service->execute($basePath);

        $this->assertSame('All migrations completed successfully.', $logMessages[2]);
    }

    public function testExecuteCatchesMigrationExceptionAndTriggersRollback(): void
    {
        $basePath = '/migrations';
        $script1 = Script::build('001_create_table.sql');
        $script2 = Script::build('002_add_column.sql');
        $scripts = [$script1, $script2];
        $migration = Migration::new(name: '20240115', scripts: $scripts);
        $allMigrations = [$migration];

        $migrationException = new MigrationException(
            scripts: $scripts,
            message: 'Migration failed'
        );

        $logMessages = [];
        $this->logger->expects($this->exactly(6))
            ->method('info')
            ->willReturnCallback(function (string $message) use (&$logMessages): void {
                $logMessages[] = $message;
            });

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrations')
            ->willReturn($allMigrations);

        $this->migrationRepository->expects($this->once())
            ->method('getMigrations')
            ->willReturn([]);

        $this->migrationExecutor->expects($this->once())
            ->method('execute')
            ->willThrowException($migrationException);

        $this->rollbackExecutor->expects($this->once())
            ->method('rollback')
            ->with($scripts);

        $this->service->execute($basePath);

        $this->assertSame('Error running migrations', $logMessages[2]);
        $this->assertSame('Rolling back script: 001_create_table.sql', $logMessages[3]);
        $this->assertSame('Rolling back script: 002_add_column.sql', $logMessages[4]);
        $this->assertSame('Rollback completed successfully.', $logMessages[5]);
    }

    public function testExecuteLogsRollbackForEachScriptOnError(): void
    {
        $basePath = '/migrations';
        $script1 = Script::build('001_create_table.sql');
        $script2 = Script::build('002_add_column.sql');
        $scripts = [$script1, $script2];
        $migration = Migration::new(name: '20240115', scripts: $scripts);
        $allMigrations = [$migration];

        $migrationException = new MigrationException(
            scripts: $scripts,
            message: 'Migration failed'
        );

        $logMessages = [];
        $this->logger->expects($this->exactly(6))
            ->method('info')
            ->willReturnCallback(function (string $message) use (&$logMessages): void {
                $logMessages[] = $message;
            });

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrations')
            ->willReturn($allMigrations);

        $this->migrationRepository->expects($this->once())
            ->method('getMigrations')
            ->willReturn([]);

        $this->migrationExecutor->expects($this->once())
            ->method('execute')
            ->willThrowException($migrationException);

        $this->rollbackExecutor->expects($this->once())
            ->method('rollback');

        $this->service->execute($basePath);

        $this->assertSame('Rolling back script: 001_create_table.sql', $logMessages[3]);
        $this->assertSame('Rolling back script: 002_add_column.sql', $logMessages[4]);
    }

    public function testExecuteCallsRollbackExecutorWithScriptsFromException(): void
    {
        $basePath = '/migrations';
        $script1 = Script::build('001_create_table.sql');
        $script2 = Script::build('002_add_column.sql');
        $scripts = [$script1, $script2];
        $migration = Migration::new(name: '20240115', scripts: $scripts);
        $allMigrations = [$migration];

        $migrationException = new MigrationException(
            scripts: $scripts,
            message: 'Migration failed'
        );

        $this->logger->expects($this->exactly(6))
            ->method('info');

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrations')
            ->willReturn($allMigrations);

        $this->migrationRepository->expects($this->once())
            ->method('getMigrations')
            ->willReturn([]);

        $this->migrationExecutor->expects($this->once())
            ->method('execute')
            ->willThrowException($migrationException);

        $this->rollbackExecutor->expects($this->once())
            ->method('rollback')
            ->with($this->equalTo($scripts));

        $this->service->execute($basePath);
    }

    public function testExecuteLogsRollbackCompletedSuccessfullyAfterRollback(): void
    {
        $basePath = '/migrations';
        $script = Script::build('001_create_table.sql');
        $scripts = [$script];
        $migration = Migration::new(name: '20240115', scripts: $scripts);
        $allMigrations = [$migration];

        $migrationException = new MigrationException(
            scripts: $scripts,
            message: 'Migration failed'
        );

        $logMessages = [];
        $this->logger->expects($this->exactly(5))
            ->method('info')
            ->willReturnCallback(function (string $message) use (&$logMessages): void {
                $logMessages[] = $message;
            });

        $this->migrationFileManager->expects($this->once())
            ->method('getMigrations')
            ->willReturn($allMigrations);

        $this->migrationRepository->expects($this->once())
            ->method('getMigrations')
            ->willReturn([]);

        $this->migrationExecutor->expects($this->once())
            ->method('execute')
            ->willThrowException($migrationException);

        $this->rollbackExecutor->expects($this->once())
            ->method('rollback');

        $this->service->execute($basePath);

        $this->assertSame('Rollback completed successfully.', $logMessages[4]);
    }
}
