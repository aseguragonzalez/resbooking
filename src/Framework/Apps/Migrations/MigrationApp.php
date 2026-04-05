<?php

declare(strict_types=1);

namespace Framework\Apps\Migrations;

use Framework\Apps\Application;
use Psr\Container\ContainerInterface;
use Framework\Apps\Migrations\Application\RunMigrations;
use Framework\Apps\Migrations\Application\TestMigration;
use Framework\Apps\Migrations\Application\TestMigrationCommand;
use Psr\Log\LoggerInterface;

final class MigrationApp extends Application
{
    public function __construct(ContainerInterface $container, string $basePath)
    {
        parent::__construct($container, $basePath);
    }

    /**
     * Removes `--migrations-base=<path>` from argv (used when the CLI subprocess forwards the SQL leaf directory).
     *
     * @param array<string> $argv
     * @return array{0: string|null, 1: array<string>}
     */
    public static function stripMigrationsBaseFromArgv(array $argv): array
    {
        $override = null;
        $out = [];
        foreach ($argv as $arg) {
            if (str_starts_with($arg, '--migrations-base=')) {
                $override = substr($arg, strlen('--migrations-base='));
                continue;
            }
            $out[] = $arg;
        }

        return [$override !== '' ? $override : null, $out];
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
            [$migrationsBaseOverride, $cleanArgv] = self::stripMigrationsBaseFromArgv($argv);
            $effectiveBasePath = $migrationsBaseOverride ?? $this->basePath;

            $arguments = $this->parseArguments($cleanArgv);

            if ($arguments['command'] === 'test') {
                /** @var string $migrationName */
                $migrationName = $arguments['args'];
                /** @var TestMigration $testMigration */
                $testMigration = $this->container->get(TestMigration::class);
                $testMigration->execute(new TestMigrationCommand(
                    migrationName: $migrationName,
                    basePath: $effectiveBasePath,
                ));
            } elseif ($arguments['command'] === 'run') {
                /** @var RunMigrations $runMigrations */
                $runMigrations = $this->container->get(RunMigrations::class);
                $runMigrations->execute(basePath: $effectiveBasePath);
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
