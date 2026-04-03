<?php

declare(strict_types=1);

namespace Framework\Mvc\Migrations\Infrastructure;

use Framework\Mvc\Migrations\Domain\Services\MigrationTestScope;
use Framework\Mvc\Migrations\Domain\Services\MigrationTestScopeFactory;
use Framework\Mvc\Migrations\Domain\Services\RollbackExecutorHandler;
use Framework\Mvc\Migrations\Domain\Services\TestMigrationExecutorHandler;
use Framework\Mvc\Migrations\MigrationSettings;
use PDO;

final readonly class MigrationTestScopeFactoryHandler implements MigrationTestScopeFactory
{
    public function __construct(private MigrationSettings $settings)
    {
    }

    public function createScope(string $databaseName): MigrationTestScope
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $this->settings->host,
            $databaseName,
            $this->settings->charset,
        );
        $pdo = new PDO(
            $dsn,
            $this->settings->user,
            $this->settings->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        $dbClient = new SqlDbClient($pdo);
        $schemaSnapshotExecutor = new SqlSchemaSnapshotExecutor($pdo);
        $testMigrationExecutor = new TestMigrationExecutorHandler($dbClient, $databaseName);
        $rollbackExecutor = new RollbackExecutorHandler($dbClient, $databaseName);

        return new MigrationTestScope(
            schemaSnapshotExecutor: $schemaSnapshotExecutor,
            testMigrationExecutor: $testMigrationExecutor,
            rollbackExecutor: $rollbackExecutor,
        );
    }
}
