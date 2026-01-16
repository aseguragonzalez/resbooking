<?php

declare(strict_types=1);

namespace Framework\Migrations;

use DI\Container;
use Framework\Migrations\Application\RunMigrations;
use Framework\Migrations\Application\TestMigration;
use Framework\Migrations\Application\TestMigrationCommand;
use Psr\Log\LoggerInterface;

final readonly class MigrationApp
{
    public function __construct(
        private Container $container,
        private string $basePath,
    ) {
    }

    public function run(string $command, ?string $testMigrationName): void
    {
        $this->configure();

        /** @var LoggerInterface $logger */
        $logger = $this->container->get(LoggerInterface::class);

        $exitCode = 0;

        try {
            $this->handleOperation($command, $testMigrationName);
        } catch (\Exception $e) {
            $logger->error('Error running migrations: {error}', ['error' => $e->getMessage()]);
            $exitCode = 1;
        } finally {
            exit($exitCode);
        }
    }

    private function configure(): void
    {
        Dependencies::configure($this->container);
    }

    private function handleOperation(string $command, ?string $testMigrationName): void
    {
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
}
