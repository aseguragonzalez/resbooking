<?php

declare(strict_types=1);

namespace Framework\Mvc\Commands;

final class MigrationsCreateCommand implements Command
{
    public function __construct(
        private readonly ConsoleOutput $output,
    ) {
    }

    public function getName(): string
    {
        return 'migrations:create';
    }

    public function getDescription(): string
    {
        return 'Create a new migration with timestamped folder and SQL stubs';
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

        $resolvedPath = $this->resolvePath($migrationsPath);

        if (!is_dir($resolvedPath)) {
            $this->output->error("Migrations directory does not exist: {$resolvedPath}");
            return 1;
        }

        $timestamp = date('YmdHis');
        $migrationDir = $resolvedPath . '/' . $timestamp;

        if (!mkdir($migrationDir, 0755, true)) {
            $this->output->error("Failed to create migration directory: {$migrationDir}");
            return 1;
        }

        $migrationFile = $migrationDir . '/0001_migration.sql';
        $rollbackFile = $migrationDir . '/0001_migration.rollback.sql';

        file_put_contents($migrationFile, "-- Migration file\n");
        file_put_contents($rollbackFile, "-- Rollback file\n");

        $this->output->success("Migration created successfully!");
        $this->output->line("  Directory: {$migrationDir}");
        $this->output->line("  Migration file: {$migrationFile}");
        $this->output->line("  Rollback file: {$rollbackFile}");

        return 0;
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

    private function resolvePath(string $path): string
    {
        if (str_starts_with($path, '/') || str_contains($path, '://')) {
            return rtrim($path, '/');
        }

        return rtrim(getcwd() . '/' . $path, '/');
    }

    private function showHelp(): void
    {
        $this->output->line('Usage: mvc migrations:create --path=<migrations-dir>');
        $this->output->line();
        $this->output->line('Options:');
        $this->output->line('  --path=<migrations-dir>  Path to the migrations directory (required)');
        $this->output->line();
        $this->output->line('Creates a new timestamped migration folder with empty SQL stubs.');
    }
}
