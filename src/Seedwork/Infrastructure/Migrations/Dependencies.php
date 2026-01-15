<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations;

use DI\Container;
use PDO;
use Seedwork\Application\Logging\Logger;
use Seedwork\Infrastructure\Files\DefaultFileManager;
use Seedwork\Infrastructure\Files\FileManager;
use Seedwork\Infrastructure\Logging\MonoLoggerAdapter;
use Seedwork\Infrastructure\Migrations\Application\RunMigrations;
use Seedwork\Infrastructure\Migrations\Application\RunMigrationsService;
use Seedwork\Infrastructure\Migrations\Application\TestMigration;
use Seedwork\Infrastructure\Migrations\Application\TestMigrationService;
use Seedwork\Infrastructure\Migrations\Domain\Clients\DbClient;
use Seedwork\Infrastructure\Migrations\Domain\Repositories\MigrationRepository;
use Seedwork\Infrastructure\Migrations\Domain\Services\DatabaseBackupManager;
use Seedwork\Infrastructure\Migrations\Domain\Services\MigrationExecutor;
use Seedwork\Infrastructure\Migrations\Domain\Services\MigrationExecutorService;
use Seedwork\Infrastructure\Migrations\Domain\Services\MigrationFileManager;
use Seedwork\Infrastructure\Migrations\Domain\Services\MigrationFileManagerService;
use Seedwork\Infrastructure\Migrations\Domain\Services\RollbackExecutor;
use Seedwork\Infrastructure\Migrations\Domain\Services\RollbackExecutorService;
use Seedwork\Infrastructure\Migrations\Domain\Services\SchemaComparator;
use Seedwork\Infrastructure\Migrations\Domain\Services\SchemaComparatorService;
use Seedwork\Infrastructure\Migrations\Domain\Services\SchemaSnapshotExecutor;
use Seedwork\Infrastructure\Migrations\Domain\Services\TestMigrationExecutor;
use Seedwork\Infrastructure\Migrations\Domain\Services\TestMigrationExecutorService;
use Seedwork\Infrastructure\Migrations\Infrastructure\SqlSchemaSnapshotExecutor;
use Seedwork\Infrastructure\Migrations\Infrastructure\MigrationSettings;
use Seedwork\Infrastructure\Migrations\Infrastructure\ShellDatabaseBackupManager;
use Seedwork\Infrastructure\Migrations\Infrastructure\SqlDbClient;
use Seedwork\Infrastructure\Migrations\Infrastructure\SqlMigrationRepository;

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
