<?php

declare(strict_types=1);

namespace Framework\Migrations;

use DI\Container;
use PDO;
use Framework\Logging\Logger;
use Framework\Files\DefaultFileManager;
use Framework\Files\FileManager;
use Framework\Logging\MonoLoggerAdapter;
use Framework\Migrations\Application\RunMigrations;
use Framework\Migrations\Application\RunMigrationsService;
use Framework\Migrations\Application\TestMigration;
use Framework\Migrations\Application\TestMigrationService;
use Framework\Migrations\Domain\Clients\DbClient;
use Framework\Migrations\Domain\Repositories\MigrationRepository;
use Framework\Migrations\Domain\Services\DatabaseBackupManager;
use Framework\Migrations\Domain\Services\MigrationExecutor;
use Framework\Migrations\Domain\Services\MigrationExecutorService;
use Framework\Migrations\Domain\Services\MigrationFileManager;
use Framework\Migrations\Domain\Services\MigrationFileManagerService;
use Framework\Migrations\Domain\Services\RollbackExecutor;
use Framework\Migrations\Domain\Services\RollbackExecutorService;
use Framework\Migrations\Domain\Services\SchemaComparator;
use Framework\Migrations\Domain\Services\SchemaComparatorService;
use Framework\Migrations\Domain\Services\SchemaSnapshotExecutor;
use Framework\Migrations\Domain\Services\TestMigrationExecutor;
use Framework\Migrations\Domain\Services\TestMigrationExecutorService;
use Framework\Migrations\Infrastructure\SqlSchemaSnapshotExecutor;
use Framework\Migrations\Infrastructure\MigrationSettings;
use Framework\Migrations\Infrastructure\ShellDatabaseBackupManager;
use Framework\Migrations\Infrastructure\SqlDbClient;
use Framework\Migrations\Infrastructure\SqlMigrationRepository;

final class Dependencies
{
    public static function configure(Container $container): void
    {
        /** @var MigrationSettings $settings */
        $settings = $container->get(MigrationSettings::class);
        $connection = new PDO(
            $settings->getDsn(),
            $settings->user,
            $settings->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        $container->set(PDO::class, $connection);

        $container->set(FileManager::class, $container->get(DefaultFileManager::class));
        $container->set(Logger::class, $container->get(MonoLoggerAdapter::class));
        $container->set(MigrationRepository::class, $container->get(SqlMigrationRepository::class));
        $container->set(DbClient::class, $container->get(SqlDbClient::class));
        $container->set(MigrationExecutor::class, $container->get(MigrationExecutorService::class));
        $container->set(MigrationFileManager::class, $container->get(MigrationFileManagerService::class));
        $container->set(RollbackExecutor::class, $container->get(RollbackExecutorService::class));
        $container->set(RunMigrations::class, $container->get(RunMigrationsService::class));

        // Test migration services
        $container->set(SchemaSnapshotExecutor::class, $container->get(SqlSchemaSnapshotExecutor::class));
        $container->set(SchemaComparator::class, $container->get(SchemaComparatorService::class));
        $container->set(TestMigrationExecutor::class, $container->get(TestMigrationExecutorService::class));
        $container->set(DatabaseBackupManager::class, $container->get(ShellDatabaseBackupManager::class));
        $container->set(TestMigration::class, $container->get(TestMigrationService::class));
    }
}
