<?php

declare(strict_types=1);

namespace Framework\Mvc\Commands;

use DI\Container;
use Framework\Mvc\Migrations\MigrationApp;

final class MigrationsTestCommand implements Command
{
    /** @var \Closure(string, array<string>): int */
    private \Closure $migrationRunner;

    /**
     * @param \Closure(string, array<string>): int|null $migrationRunner
     */
    public function __construct(
        private readonly ConsoleOutput $output,
        ?\Closure $migrationRunner = null,
    ) {
        $this->migrationRunner = $migrationRunner ?? static function (string $basePath, array $argv): int {
            $app = new MigrationApp(
                container: new Container(),
                basePath: $basePath,
            );
            /** @var array<string> $argv */
            return $app->run(count($argv), $argv);
        };
    }

    public function getName(): string
    {
        return 'migrations:test';
    }

    public function getDescription(): string
    {
        return 'Test a specific migration (run + rollback + schema comparison)';
    }

    /**
     * @param array<string> $args
     */
    public function execute(array $args): int
    {
        if (in_array('--help', $args, true) || in_array('-h', $args, true)) {
            $this->showHelp();
            return 0;
        }

        $migrationsPath = $this->parsePath($args);

        if ($migrationsPath === null) {
            $this->output->error('The --path option is required.');
            $this->output->line();
            $this->showHelp();
            return 1;
        }

        $migrationName = $this->parseMigrationName($args);

        if ($migrationName === null) {
            $this->output->error('The --migration option is required.');
            $this->output->line();
            $this->showHelp();
            return 1;
        }

        $resolvedPath = $this->resolvePath($migrationsPath);

        if (!is_dir($resolvedPath)) {
            $this->output->error("Migrations directory does not exist: {$resolvedPath}");
            return 1;
        }

        $this->output->info("Testing migration '{$migrationName}' from: {$resolvedPath}");

        return ($this->migrationRunner)($resolvedPath, ["--test={$migrationName}"]);
    }

    /**
     * @param array<string> $args
     */
    private function parsePath(array $args): ?string
    {
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--path=')) {
                return substr($arg, 7);
            }
        }

        return null;
    }

    /**
     * @param array<string> $args
     */
    private function parseMigrationName(array $args): ?string
    {
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--migration=')) {
                return substr($arg, 12);
            }
        }

        return null;
    }

    private function resolvePath(string $path): string
    {
        if (str_starts_with($path, '/') || str_contains($path, '://')) {
            return rtrim($path, '/');
        }

        return rtrim(getcwd() . '/' . $path, '/');
    }

    private function showHelp(): void
    {
        $this->output->line('Usage: mvc migrations:test --path=<migrations-dir> --migration=<name>');
        $this->output->line();
        $this->output->line('Options:');
        $this->output->line('  --path=<migrations-dir>  Path to the migrations directory (required)');
        $this->output->line('  --migration=<name>       Name of the migration to test (required)');
        $this->output->line();
        $this->output->line('Tests a migration by running it, then rolling back, and comparing schemas.');
    }
}
