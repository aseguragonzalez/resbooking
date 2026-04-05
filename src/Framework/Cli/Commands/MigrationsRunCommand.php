<?php

declare(strict_types=1);

namespace Framework\Cli\Commands;

use Framework\Web\Config\MvcConfig;

final class MigrationsRunCommand implements Command
{
    use MigrationsCommandPathTrait;

    /** @var \Closure(string, list<string>): int */
    private \Closure $migrationRunner;

    /**
     * @param \Closure(string, list<string>): int|null $migrationRunner
     */
    public function __construct(
        private readonly ConsoleOutput $output,
        ?\Closure $migrationRunner = null,
    ) {
        $this->migrationRunner = $migrationRunner ?? static function (string $indexPath, array $forwardArgs): int {
            /** @var list<string> $forwardArgs */
            /** @var list<string> $cmd */
            $cmd = [PHP_BINARY, $indexPath, ...$forwardArgs];
            $descriptorSpec = [0 => STDIN, 1 => STDOUT, 2 => STDERR];
            $process = proc_open($cmd, $descriptorSpec, $pipes, null, null, ['bypass_shell' => true]);
            if (!is_resource($process)) {
                return 1;
            }

            return proc_close($process);
        };
    }

    public function getName(): string
    {
        return 'migrations:run';
    }

    public function getDescription(): string
    {
        return 'Run all pending migrations';
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

        $resolvedLeaf = $this->resolveLeafMigrationsDirFromArgs($args);

        if ($resolvedLeaf === null || !is_dir($resolvedLeaf)) {
            if ($this->hasExplicitLeafPath($args)) {
                $this->output->error(
                    'Migrations directory does not exist: ' . ($resolvedLeaf ?? '(invalid path)'),
                );
            } elseif ($this->migrationsDisabledWithoutForce($args)) {
                $resolvedApp = $this->resolveShellPath($this->parseAppPathArg($args));
                $this->output->error(
                    'Migrations are disabled in ' . MvcConfig::CONFIG_FILENAME
                    . '. Run: mvc migrations:enable --path=' . $this->displayPath($resolvedApp)
                    . ' (or pass --force to run anyway).',
                );
            } else {
                $this->output->error(
                    'Could not resolve migrations directory. Use --path=<migrations-dir> or --app-path=<app-dir> '
                    . 'where mvc.config.json enables migrations and the module exists.',
                );
            }
            $this->output->line();
            $this->showHelp();
            return 1;
        }

        $indexPath = MigrationsAppPathResolver::resolveIndexPathFromLeafDir($resolvedLeaf);
        if ($indexPath === null) {
            $this->output->error(
                'Migrations entry not found (expected index.php next to the migrations leaf). '
                . 'Run: mvc migrations:enable --path=<app-dir> --namespace=<PhpNamespace>',
            );
            $this->output->line();
            $this->showHelp();
            return 1;
        }

        /** @var list<string> $forwardArgs */
        $forwardArgs = ['--migrations-base=' . $resolvedLeaf];

        $this->output->info("Running pending migrations from: {$resolvedLeaf}");
        $this->output->info("Entrypoint: {$indexPath}");

        return ($this->migrationRunner)($indexPath, $forwardArgs);
    }

    /**
     * @param array<string> $args
     */
    private function migrationsDisabledWithoutForce(array $args): bool
    {
        if (in_array('--force', $args, true)) {
            return false;
        }

        $resolvedApp = $this->resolveShellPath($this->parseAppPathArg($args));
        $configPath = $resolvedApp . '/' . MvcConfig::CONFIG_FILENAME;
        if (!is_file($configPath)) {
            return false;
        }

        $config = MvcConfig::load($resolvedApp);

        return !$config->isMigrationsEnabled();
    }

    private function displayPath(string $absolutePath): string
    {
        $cwd = getcwd();
        if ($cwd !== false) {
            $prefix = rtrim($cwd, '/') . '/';
            if (str_starts_with($absolutePath, $prefix)) {
                return '.' . substr($absolutePath, strlen($prefix));
            }
        }

        return $absolutePath;
    }

    private function showHelp(): void
    {
        $this->output->line(
            'Usage: mvc migrations:run [--app-path=<app-dir>] [--path=<migrations-dir>] [--force]',
        );
        $this->output->line();
        $this->output->line('Options:');
        $this->output->line(
            '  --app-path=<app-dir>     MVC app root (default: current directory); uses mvc.config.json',
        );
        $this->output->line('  --path=<migrations-dir>  Override path to the leaf migrations directory');
        $this->output->line(
            '  --force                  Run even when migrationsEnabled is false (operators only)',
        );
        $this->output->line();
        $this->output->line('Runs pending migrations via the module index.php subprocess.');
    }
}
