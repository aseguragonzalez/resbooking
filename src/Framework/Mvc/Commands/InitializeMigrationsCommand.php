<?php

declare(strict_types=1);

namespace Framework\Mvc\Commands;

use Framework\Mvc\Config\MvcConfig;

final class InitializeMigrationsCommand implements Command
{
    public function __construct(
        private readonly ConsoleOutput $output,
        private readonly StubGenerator $stubGenerator,
    ) {
    }

    public function getName(): string
    {
        return 'initialize-migrations';
    }

    public function getDescription(): string
    {
        return 'Add the Migrations feature to an existing MVC app';
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

        $appPath = $this->parsePath($args);
        $resolvedPath = $this->resolvePath($appPath);

        if (!$this->isAppDirectory($resolvedPath)) {
            $this->output->error("Not a valid app directory (index.php not found): {$resolvedPath}");
            return 1;
        }

        $migrationsDir = $resolvedPath . '/Migrations';
        if (is_dir($migrationsDir)) {
            $this->output->error("Migrations directory already exists: {$migrationsDir}");
            return 1;
        }

        $projectRoot = $this->findProjectRoot($resolvedPath);
        if ($projectRoot === null) {
            $this->output->error('Could not find project root (no composer.json found in parent directories).');
            return 1;
        }

        $autoloadPath = $this->computeAutoloadPath($migrationsDir, $projectRoot);

        $this->output->info("Initializing Migrations in {$resolvedPath}");

        mkdir($migrationsDir, 0755, true);
        mkdir($migrationsDir . '/migrations', 0755, true);

        $this->updateMvcConfig($resolvedPath, [
            'migrationsFolderPath' => './Migrations',
        ]);

        $content = $this->stubGenerator->generate('migrations-index.stub', [
            'autoloadPath' => $autoloadPath,
        ]);
        file_put_contents($migrationsDir . '/index.php', $content);
        $this->output->line('  Created Migrations/index.php');
        $this->output->line('  Created Migrations/migrations/');

        $this->output->success('Migrations feature initialized successfully.');
        return 0;
    }

    /**
     * @param array<string, string> $changes
     */
    private function updateMvcConfig(string $appPath, array $changes): void
    {
        $configPath = rtrim($appPath, '/') . '/' . MvcConfig::CONFIG_FILENAME;

        $config = MvcConfig::defaults();
        $data = [
            'jsAssetsPath' => $config->jsAssetsPath,
            'mainJsBundler' => $config->mainJsBundler,
            'cssAssetsPath' => $config->cssAssetsPath,
            'mainCssBundler' => $config->mainCssBundler,
            'i18nPath' => $config->i18nPath,
            'migrationsFolderPath' => $config->migrationsFolderPath,
            'backgroundTasksFolderPath' => $config->backgroundTasksFolderPath,
        ];

        if (is_file($configPath)) {
            $content = file_get_contents($configPath);
            if ($content !== false) {
                try {
                    /** @var array<string, mixed> $decoded */
                    $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
                    foreach ($data as $key => $defaultValue) {
                        if (array_key_exists($key, $decoded) && is_string($decoded[$key])) {
                            $data[$key] = $decoded[$key];
                        }
                    }
                } catch (\JsonException) {
                    // Keep defaults when config is invalid.
                }
            }
        }

        foreach ($changes as $key => $value) {
            $data[$key] = $value;
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new \RuntimeException('Failed to encode mvc.config.json');
        }
        file_put_contents($configPath, $json . PHP_EOL);
    }

    /**
     * @param array<string> $args
     */
    private function parsePath(array $args): string
    {
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--path=')) {
                return substr($arg, 7);
            }
        }

        return '.';
    }

    private function resolvePath(string $path): string
    {
        if (str_starts_with($path, '/') || str_contains($path, '://')) {
            return rtrim($path, '/');
        }

        return rtrim(getcwd() . '/' . $path, '/');
    }

    private function isAppDirectory(string $path): bool
    {
        return is_dir($path) && file_exists($path . '/index.php');
    }

    private function findProjectRoot(string $fromPath): ?string
    {
        $current = $fromPath;
        while ($current !== dirname($current)) {
            if (file_exists($current . '/composer.json')) {
                return $current;
            }
            $current = dirname($current);
        }

        return null;
    }

    private function computeAutoloadPath(string $targetDir, string $projectRoot): string
    {
        $targetDir = rtrim($targetDir, '/');
        $projectRoot = rtrim($projectRoot, '/');

        return $this->getRelativePath($targetDir, $projectRoot) . '/vendor/autoload.php';
    }

    private function getRelativePath(string $from, string $to): string
    {
        $fromParts = explode('/', $from);
        $toParts = explode('/', $to);

        $commonLength = 0;
        $maxLength = min(count($fromParts), count($toParts));
        for ($i = 0; $i < $maxLength; $i++) {
            if ($fromParts[$i] !== $toParts[$i]) {
                break;
            }
            $commonLength++;
        }

        $upCount = count($fromParts) - $commonLength;
        $downParts = array_slice($toParts, $commonLength);

        return implode('/', array_merge(array_fill(0, $upCount, '..'), $downParts));
    }

    private function showHelp(): void
    {
        $this->output->line('Usage: mvc initialize-migrations [--path=<app-path>]');
        $this->output->line();
        $this->output->line('Options:');
        $this->output->line('  --path=<app-path>  Path to the MVC app directory (default: current directory)');
    }
}
