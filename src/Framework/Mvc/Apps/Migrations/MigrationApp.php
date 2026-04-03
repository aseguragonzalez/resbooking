<?php

declare(strict_types=1);

namespace Framework\Mvc\Migrations;

use DI\Container;
use Framework\Mvc\Application;
use Framework\Mvc\Migrations\Application\RunMigrations;
use Framework\Mvc\Migrations\Application\TestMigration;
use Framework\Mvc\Migrations\Application\TestMigrationCommand;
use Psr\Log\LoggerInterface;

final class MigrationApp extends Application
{
    public function __construct(Container $container, string $basePath)
    {
        parent::__construct($container, $basePath);
    }

    /**
     * Run the application with the given arguments.
     * @param int|null $argc The number of arguments passed to the application. Default is null.
     * @param array<string> $argv The arguments to pass to the application. Default is an empty array.
     * @return int The exit code of the application.
     */
    public function run(?int $argc = null, array $argv = []): int
    {
        try {
            $arguments = $this->parseArguments($argv);

            if ($arguments['command'] === 'test') {
                /** @var string $migrationName */
                $migrationName = $arguments['args'];
                /** @var TestMigration $testMigration */
                $testMigration = $this->container->get(TestMigration::class);
                $testMigration->execute(new TestMigrationCommand(
                    migrationName: $migrationName,
                    basePath: $this->basePath,
                ));
            } elseif ($arguments['command'] === 'run') {
                /** @var RunMigrations $runMigrations */
                $runMigrations = $this->container->get(RunMigrations::class);
                $runMigrations->execute(basePath: $this->basePath);
            }

            return 0;
        } catch (\Exception $e) {
            /** @var LoggerInterface $logger */
            $logger = $this->container->get(LoggerInterface::class);
            $logger->error('Error running migrations: {error}', ['error' => $e->getMessage()]);

            return 1;
        }
    }

    /**
     * Parse the arguments and return the command and the migration name.
     * @param array<string> $argv The arguments to pass to the application. Default is an empty array.
     * @return array<string, string> The command and the arguments.
     */
    private function parseArguments(array $argv = []): array
    {
        if (empty($argv)) {
            return [
                'command' => 'run',
                'args' => '',
            ];
        }

        if (count($argv) === 1) {
            $migrationName = str_replace('--test=', '', $argv[0] ?? '');

            return [
                'command' => 'test',
                'args' => $migrationName,
            ];
        }

        throw new \InvalidArgumentException('Invalid command');
    }
}
