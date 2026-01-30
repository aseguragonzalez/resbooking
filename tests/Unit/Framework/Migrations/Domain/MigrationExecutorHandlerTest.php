<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Migrations\Domain;

use Framework\Files\FileManager;
use Framework\Migrations\Domain\Clients\DbClient;
use Framework\Migrations\Domain\Entities\Migration;
use Framework\Migrations\Domain\Entities\Script;
use Framework\Migrations\Domain\Exceptions\MigrationException;
use Framework\Migrations\Domain\Repositories\MigrationRepository;
use Framework\Migrations\Domain\Services\MigrationExecutorHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MigrationExecutorHandlerTest extends TestCase
{
    private MigrationRepository&MockObject $repository;
    private DbClient&MockObject $dbClient;
    private MigrationExecutorHandler $executor;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(MigrationRepository::class);
        $this->dbClient = $this->createMock(DbClient::class);
        $this->executor = new MigrationExecutorHandler($this->repository, $this->dbClient);
    }

    public function testExecuteRunsAllScriptsInMigration(): void
    {
        $script1 = $this->createScriptFromFile('001_create_table.sql', 'CREATE TABLE users (id INT);');
        $script2 = $this->createScriptFromFile(
            '002_add_column.sql',
            'ALTER TABLE users ADD COLUMN name VARCHAR(255);',
        );
        $scripts = [$script1, $script2];
        $migration = Migration::new(name: 'test_migration', scripts: $scripts);
        $this->dbClient->expects($this->exactly(2))->method('execute');
        $this->dbClient->expects($this->once())->method('beginTransaction');
        $this->repository->expects($this->once())->method('save')->with($this->equalTo($migration));
        $this->dbClient->expects($this->once())->method('commit');

        $this->executor->execute($migration);
    }

    public function testExecuteSavesMigrationToRepositoryAfterSuccessfulExecution(): void
    {
        $script = $this->createScriptFromFile('001_create_table.sql', 'CREATE TABLE users (id INT);');
        $scripts = [$script];
        $migration = Migration::new(name: 'test_migration', scripts: $scripts);
        $this->dbClient->expects($this->once())->method('execute');
        $this->dbClient->expects($this->once())->method('beginTransaction');
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($savedMigration) use ($migration) {
                return $savedMigration instanceof Migration &&
                    $savedMigration->name === $migration->name;
            }));
        $this->dbClient->expects($this->once())->method('commit');

        $this->executor->execute($migration);
    }

    public function testExecuteUsesTransactionWhenSavingMigration(): void
    {
        $script = $this->createScriptFromFile('001_create_table.sql', 'CREATE TABLE users (id INT);');
        $scripts = [$script];
        $migration = Migration::new(name: 'test_migration', scripts: $scripts);
        $this->dbClient->expects($this->once())->method('execute');
        $this->dbClient->expects($this->once())->method('beginTransaction');
        $this->repository->expects($this->once())->method('save');
        $this->dbClient->expects($this->once())->method('commit');

        $this->executor->execute($migration);
    }

    public function testExecuteCommitsTransactionOnSuccess(): void
    {
        $script = $this->createScriptFromFile('001_create_table.sql', 'CREATE TABLE users (id INT);');
        $scripts = [$script];
        $migration = Migration::new(name: 'test_migration', scripts: $scripts);
        $this->dbClient->expects($this->once())->method('execute');
        $this->dbClient->expects($this->once())->method('beginTransaction');
        $this->repository->expects($this->once())->method('save');
        $this->dbClient->expects($this->once())->method('commit');
        $this->dbClient->expects($this->never())->method('rollBack');

        $this->executor->execute($migration);
    }

    public function testExecuteRollsBackTransactionOnError(): void
    {
        $script = $this->createScriptFromFile('001_create_table.sql', 'CREATE TABLE users (id INT);');
        $scripts = [$script];
        $migration = Migration::new(name: 'test_migration', scripts: $scripts);
        $this->dbClient->expects($this->once())
            ->method('execute')
            ->willThrowException(new \RuntimeException('Database error'));
        $this->dbClient->expects($this->once())->method('inTransaction')->willReturn(true);
        $this->dbClient->expects($this->once())->method('rollBack');
        $this->dbClient->expects($this->never())->method('beginTransaction');
        $this->repository->expects($this->never())->method('save');
        $this->expectException(MigrationException::class);

        try {
            $this->executor->execute($migration);
        } catch (MigrationException $e) {
            $this->assertCount(1, $e->scripts);
            $this->assertSame($script, $e->scripts[0]);
            throw $e;
        }
    }

    public function testExecuteThrowsMigrationExceptionWithExecutedScriptsOnFailure(): void
    {
        $script1 = $this->createScriptFromFile('001_create_table.sql', 'CREATE TABLE users (id INT);');
        $script2 = $this->createScriptFromFile(
            '002_add_column.sql',
            'ALTER TABLE users ADD COLUMN name VARCHAR(255);',
        );
        $scripts = [$script1, $script2];
        $migration = Migration::new(name: 'test_migration', scripts: $scripts);

        $callCount = 0;
        $this->dbClient->expects($this->exactly(2))
            ->method('execute')
            ->willReturnCallback(function () use (&$callCount) {
                $callCount++;
                if ($callCount === 2) {
                    throw new \RuntimeException('Error on second script');
                }
                return null;
            });
        $this->repository->expects($this->never())->method('save');
        $this->dbClient->expects($this->once())->method('inTransaction')->willReturn(true);
        $this->dbClient->expects($this->once())->method('rollBack');
        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage('Error on second script');

        try {
            $this->executor->execute($migration);
        } catch (MigrationException $e) {
            $this->assertCount(2, $e->scripts);
            $this->assertSame($script1, $e->scripts[0]);
            $this->assertSame($script2, $e->scripts[1]);
            throw $e;
        }
    }

    public function testExecuteRollsBackOnlyIfInTransactionOnError(): void
    {
        $script = $this->createScriptFromFile('001_create_table.sql', 'CREATE TABLE users (id INT);');
        $scripts = [$script];
        $migration = Migration::new(name: 'test_migration', scripts: $scripts);

        $this->dbClient
            ->expects($this->once())
            ->method('execute')
            ->willThrowException(new \RuntimeException('Database error'));
        $this->repository->expects($this->never())->method('save');
        $this->dbClient->expects($this->once())->method('inTransaction')->willReturn(false);
        $this->dbClient->expects($this->never())->method('rollBack');
        $this->expectException(MigrationException::class);

        try {
            $this->executor->execute($migration);
        } catch (MigrationException $e) {
            $this->assertCount(1, $e->scripts);
            throw $e;
        }
    }

    public function testExecuteHandlesErrorDuringSave(): void
    {
        $script = $this->createScriptFromFile('001_create_table.sql', 'CREATE TABLE users (id INT);');
        $scripts = [$script];
        $migration = Migration::new(name: 'test_migration', scripts: $scripts);
        $this->dbClient->expects($this->once())->method('execute');
        $this->dbClient->expects($this->once())->method('beginTransaction');
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new \RuntimeException('Save failed'));
        $this->dbClient->expects($this->once())->method('inTransaction')->willReturn(true);
        $this->dbClient->expects($this->once())->method('rollBack');
        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage('Save failed');

        try {
            $this->executor->execute($migration);
        } catch (MigrationException $e) {
            $this->assertCount(1, $e->scripts);
            throw $e;
        }
    }

    private function createScriptFromFile(string $fileName, string $content, ?string $rollbackContent = null): Script
    {
        $fileManager = $this->createStub(FileManager::class);
        $basePath = '/test/migrations';

        if ($rollbackContent !== null) {
            $fileManager->method('readTextPlain')->willReturn($content, $rollbackContent);
        } else {
            $fileManager->method('readTextPlain')->willReturn($content);
        }

        return Script::fromFile($basePath, $fileName, $fileManager);
    }
}
