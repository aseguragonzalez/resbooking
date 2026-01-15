<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations;

use DI\Container;
use Seedwork\Infrastructure\Migrations\Application\RunMigrations;
use Seedwork\Infrastructure\Migrations\Application\TestMigration;
use Seedwork\Infrastructure\Migrations\Application\TestMigrationCommand;

final class MigrationApp
{
    public function __construct(
        private readonly Container $container,
        private readonly string $basePath,
    ) {
    }

    public function run(string $command, ?string $testMigrationName): void
    {
        $this->configure();

        if ($this->isTestMigration($command, $testMigrationName)) {
            // Test a specific migration
            /** @var TestMigration $testMigration */
            $testMigration = $this->container->get(TestMigration::class);
            $testMigration->execute(new TestMigrationCommand(
                /** @var string $testMigrationName */
                migrationName: (string) $testMigrationName,
                basePath: $this->basePath,
            ));
            return;
        }

        if ($this->isRunMigrations($command)) {
            // Run all pending migrations
            /** @var RunMigrations $runMigrations */
            $runMigrations = $this->container->get(RunMigrations::class);
            $runMigrations->execute(basePath: $this->basePath);
            return;
        }

        throw new \InvalidArgumentException('Invalid command');
    }

    private function isTestMigration(string $command, ?string $testMigrationName): bool
    {
        return $command === 'test' && $testMigrationName !== null && !empty($testMigrationName);
    }

    private function isRunMigrations(string $command): bool
    {
        return $command === 'run';
    }

    private function configure(): void
    {
        Dependencies::configure($this->container);
    }
}
