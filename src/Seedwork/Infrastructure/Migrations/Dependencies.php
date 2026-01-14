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
use Seedwork\Infrastructure\Migrations\Domain\DbClient;
use Seedwork\Infrastructure\Migrations\Domain\MigrationExecutor;
use Seedwork\Infrastructure\Migrations\Domain\MigrationExecutorService;
use Seedwork\Infrastructure\Migrations\Domain\MigrationFileManager;
use Seedwork\Infrastructure\Migrations\Domain\MigrationFileManagerService;
use Seedwork\Infrastructure\Migrations\Domain\MigrationRepository;
use Seedwork\Infrastructure\Migrations\Domain\RollbackExecutor;
use Seedwork\Infrastructure\Migrations\Domain\RollbackExecutorService;
use Seedwork\Infrastructure\Migrations\Infrastructure\MigrationSettings;
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
        $container->set(RunMigrations::class, $container->get(RunMigrationsService::class));
        $container->set(MigrationExecutor::class, $container->get(MigrationExecutorService::class));
        $container->set(MigrationFileManager::class, $container->get(MigrationFileManagerService::class));
        $container->set(RollbackExecutor::class, $container->get(RollbackExecutorService::class));
    }
}
