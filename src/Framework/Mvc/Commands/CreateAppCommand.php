<?php

declare(strict_types=1);

namespace Framework\Mvc\Commands;

final class CreateAppCommand implements Command
{
    public function __construct(
        private readonly ConsoleOutput $output,
        private readonly StubGenerator $stubGenerator,
    ) {
    }

    public function getName(): string
    {
        return 'create-app';
    }

    public function getDescription(): string
    {
        return 'Create a new MVC application scaffold';
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

        $parsed = $this->parseArgs($args);

        if ($parsed === null) {
            return 1;
        }

        /** @var string $path */
        $path = $parsed['path'];
        /** @var string $name */
        $name = $parsed['name'];
        /** @var string $namespace */
        $namespace = $parsed['namespace'];

        $resolvedPath = $this->resolvePath($path);

        if (is_dir($resolvedPath) && count(scandir($resolvedPath) ?: []) > 2) {
            $this->output->error("Directory already exists and is not empty: {$resolvedPath}");
            return 1;
        }

        $projectRoot = $this->findProjectRoot($resolvedPath);
        if ($projectRoot === null) {
            $this->output->error('Could not find project root (no composer.json found in parent directories).');
            return 1;
        }

        $autoloadPath = $this->computeAutoloadPath($resolvedPath, $projectRoot);
        $replacements = $this->buildReplacements($name, $namespace, $autoloadPath);

        $this->output->info("Creating MVC app '{$name}' at {$resolvedPath}");

        $this->createDirectories($resolvedPath);
        $this->createFiles($resolvedPath, $name, $replacements);

        $this->output->success("App '{$name}' created successfully at {$resolvedPath}");
        return 0;
    }

    /**
     * @param array<string> $args
     * @return array<string, string>|null
     */
    private function parseArgs(array $args): ?array
    {
        $path = null;
        $name = null;
        $namespace = null;

        foreach ($args as $arg) {
            if (str_starts_with($arg, '--name=')) {
                $name = substr($arg, 7);
            } elseif (str_starts_with($arg, '--namespace=')) {
                $namespace = substr($arg, 12);
            } elseif (!str_starts_with($arg, '-')) {
                $path = $arg;
            }
        }

        if ($path === null || $path === '') {
            $this->output->error('Missing required argument: <path>');
            $this->showHelp();
            return null;
        }

        if ($name === null || $name === '') {
            $this->output->error('Missing required option: --name=<AppName>');
            $this->showHelp();
            return null;
        }

        if ($namespace === null || $namespace === '') {
            $this->output->error('Missing required option: --namespace=<Namespace>');
            $this->showHelp();
            return null;
        }

        return ['path' => $path, 'name' => $name, 'namespace' => $namespace];
    }

    /**
     * @param array<string, string> $replacements
     */
    private function createFiles(string $basePath, string $name, array $replacements): void
    {
        $files = [
            'index.php' => $this->stubGenerator->generate('index.stub', $replacements),
            '.htaccess' => $this->stubGenerator->generate('htaccess.stub', $replacements),
            "{$name}App.php" => $this->stubGenerator->generate('app.stub', $replacements),
            "{$name}Settings.php" => $this->stubGenerator->generate('app-settings.stub', $replacements),
            'Controllers/RouterBuilder.php' => $this->stubGenerator->generate('router-builder.stub', $replacements),
            'Controllers/HomeController.php' => $this->stubGenerator->generate('home-controller.stub', $replacements),
            'Views/layout.html' => $this->stubGenerator->generate('layout.html.stub', $replacements),
            'Views/Home/index.html' => $this->stubGenerator->generate('home-index.html.stub', $replacements),
            'Views/Shared/401.html' => $this->stubGenerator->generate('error-401.html.stub', $replacements),
            'Views/Shared/403.html' => $this->stubGenerator->generate('error-403.html.stub', $replacements),
            'Views/Shared/404.html' => $this->stubGenerator->generate('error-404.html.stub', $replacements),
            'Views/Shared/500.html' => $this->stubGenerator->generate('error-500.html.stub', $replacements),
            'assets/i18n/en.json' => $this->stubGenerator->generate('i18n-en.json.stub', $replacements),
        ];

        foreach ($files as $relativePath => $content) {
            $filePath = $basePath . '/' . $relativePath;
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($filePath, $content);
            $this->output->line("  Created {$relativePath}");
        }
    }

    private function createDirectories(string $basePath): void
    {
        $directories = [
            'Controllers',
            'Views/Home',
            'Views/Shared',
            'Models',
            'assets/i18n',
            'assets/scripts',
            'assets/styles',
        ];

        foreach ($directories as $dir) {
            $dirPath = $basePath . '/' . $dir;
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0755, true);
            }
        }
    }

    /**
     * @return array<string, string>
     */
    private function buildReplacements(string $name, string $namespace, string $autoloadPath): array
    {
        return [
            'name' => $name,
            'namespace' => $namespace,
            'autoloadPath' => $autoloadPath,
            'nameKebab' => $this->toKebabCase($name),
            'nameSnake' => $this->toSnakeCase($name),
            'envPrefix' => strtoupper($this->toSnakeCase($name)),
            'appTitle' => $name,
            'year' => date('Y'),
        ];
    }

    private function resolvePath(string $path): string
    {
        if (str_starts_with($path, '/') || str_contains($path, '://')) {
            return $path;
        }

        return getcwd() . '/' . $path;
    }

    private function findProjectRoot(string $fromPath): ?string
    {
        $dir = is_dir($fromPath) ? $fromPath : dirname($fromPath);

        // Walk up from the target path's parent, since the target may not exist yet
        while (!is_dir($dir)) {
            $dir = dirname($dir);
        }

        $current = $dir;
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

        $relativePath = $this->getRelativePath($targetDir, $projectRoot);

        return $relativePath . '/vendor/autoload.php';
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

        $parts = array_merge(array_fill(0, $upCount, '..'), $downParts);

        return implode('/', $parts);
    }

    private function toKebabCase(string $name): string
    {
        $result = preg_replace('/([a-z])([A-Z])/', '$1-$2', $name);
        return strtolower($result ?? $name);
    }

    private function toSnakeCase(string $name): string
    {
        $result = preg_replace('/([a-z])([A-Z])/', '$1_$2', $name);
        return strtolower($result ?? $name);
    }

    private function showHelp(): void
    {
        $this->output->line('Usage: mvc create-app <path> --name=<AppName> --namespace=<Namespace>');
        $this->output->line();
        $this->output->line('Arguments:');
        $this->output->line('  <path>                  Target directory for the new app');
        $this->output->line();
        $this->output->line('Options:');
        $this->output->line('  --name=<AppName>        Class name prefix (e.g. Dashboard)');
        $this->output->line('  --namespace=<Namespace> PSR-4 namespace (e.g. App\\Ports\\Dashboard)');
    }
}
