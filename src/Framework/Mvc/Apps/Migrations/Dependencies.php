<?php

declare(strict_types=1);

namespace Framework\Mvc\Migrations;

use Framework\Mvc\Container\MutableContainer;
use Framework\Mvc\Files\DefaultFileManager;
use Framework\Mvc\Files\FileManager;
use Framework\Mvc\Migrations\Application\RunMigrations;
use Framework\Mvc\Migrations\Application\RunMigrationsHandler;
use Framework\Mvc\Migrations\Application\TestMigration;
use Framework\Mvc\Migrations\Application\TestMigrationHandler;
use Framework\Mvc\Migrations\Domain\Clients\DbClient;
use Framework\Mvc\Migrations\Domain\Repositories\MigrationRepository;
use Framework\Mvc\Migrations\Domain\Services\DatabaseBackupManager;
use Framework\Mvc\Migrations\Domain\Services\MigrationExecutor;
use Framework\Mvc\Migrations\Domain\Services\MigrationExecutorHandler;
use Framework\Mvc\Migrations\Domain\Services\MigrationFileManager;
use Framework\Mvc\Migrations\Domain\Services\MigrationFileManagerHandler;
use Framework\Mvc\Migrations\Domain\Services\MigrationTestScopeFactory;
use Framework\Mvc\Migrations\Domain\Services\RollbackExecutor;
use Framework\Mvc\Migrations\Domain\Services\RollbackExecutorHandler;
use Framework\Mvc\Migrations\Domain\Services\SchemaComparator;
use Framework\Mvc\Migrations\Domain\Services\SchemaComparatorHandler;
use Framework\Mvc\Migrations\Domain\Services\SchemaSnapshotExecutor;
use Framework\Mvc\Migrations\Domain\Services\TestMigrationExecutor;
use Framework\Mvc\Migrations\Domain\Services\TestMigrationExecutorHandler;
use Framework\Mvc\Migrations\Infrastructure\MigrationTestScopeFactoryHandler;
use Framework\Mvc\Migrations\Infrastructure\ShellDatabaseBackupManager;
use Framework\Mvc\Migrations\Infrastructure\SqlDbClient;
use Framework\Mvc\Migrations\Infrastructure\SqlMigrationRepository;
use Framework\Mvc\Migrations\Infrastructure\SqlSchemaSnapshotExecutor;
use PDO;

use function DI\factory;

final class Dependencies
{
    /**
     * Registers framework migrations bindings. The composition root must register {@see PDO}
     * and {@see MigrationsMysqlConnection} before calling this method.
     */
    public static function configure(MutableContainer $container): void
    {
        // Infrastructure services
        $container->set(FileManager::class, $container->get(DefaultFileManager::class));
        $container->set(MigrationRepository::class, $container->get(SqlMigrationRepository::class));
        $container->set(DbClient::class, $container->get(SqlDbClient::class));
        $container->set(
            MigrationExecutorHandler::class,
            factory(function (MigrationRepository $r, DbClient $c, MigrationsMysqlConnection $mysql) {
                return new MigrationExecutorHandler($r, $c, $mysql->database);
            }),
        );
        $container->set(MigrationExecutor::class, $container->get(MigrationExecutorHandler::class));
        $container->set(MigrationFileManager::class, $container->get(MigrationFileManagerHandler::class));
        $container->set(
            RollbackExecutorHandler::class,
            factory(function (DbClient $c, MigrationsMysqlConnection $mysql) {
                return new RollbackExecutorHandler($c, $mysql->database);
            }),
        );
        $container->set(RollbackExecutor::class, $container->get(RollbackExecutorHandler::class));
        $container->set(RunMigrations::class, $container->get(RunMigrationsHandler::class));
        $container->set(SchemaSnapshotExecutor::class, $container->get(SqlSchemaSnapshotExecutor::class));
        $container->set(SchemaComparator::class, $container->get(SchemaComparatorHandler::class));
        $container->set(
            TestMigrationExecutorHandler::class,
            factory(function (DbClient $c, MigrationsMysqlConnection $mysql) {
                return new TestMigrationExecutorHandler($c, $mysql->database);
            }),
        );
        $container->set(TestMigrationExecutor::class, $container->get(TestMigrationExecutorHandler::class));
        $container->set(DatabaseBackupManager::class, $container->get(ShellDatabaseBackupManager::class));
        $container->set(
            MigrationTestScopeFactory::class,
            $container->get(MigrationTestScopeFactoryHandler::class),
        );
        $container->set(TestMigration::class, $container->get(TestMigrationHandler::class));
    }
}
