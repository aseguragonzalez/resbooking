<?php

declare(strict_types=1);

namespace Framework\Mvc\Config;

use RuntimeException;

final readonly class MvcConfig
{
    public const CONFIG_FILENAME = 'mvc.config.json';

    public function __construct(
        public string $jsAssetsPath,
        public string $mainJsBundler,
        public string $cssAssetsPath,
        public string $mainCssBundler,
        public string $i18nPath,
        public string $migrationsFolderPath,
        public string $backgroundTasksFolderPath,
    ) {
    }

    public static function defaults(): self
    {
        return new self(
            jsAssetsPath: './assets/scripts',
            mainJsBundler: 'main.min.js',
            cssAssetsPath: './assets/styles',
            mainCssBundler: 'main.min.css',
            i18nPath: './assets/i18n',
            migrationsFolderPath: '',
            backgroundTasksFolderPath: '',
        );
    }

    public static function load(string $basePath, string $configFilename = self::CONFIG_FILENAME): self
    {
        $configPath = rtrim($basePath, '/') . '/' . ltrim($configFilename, '/');
        if (!is_file($configPath)) {
            return self::defaults();
        }

        $content = file_get_contents($configPath);
        if ($content === false) {
            throw new RuntimeException("Failed to read config: {$configPath}");
        }

        try {
            /** @var array<string, mixed> $data */
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new RuntimeException("Failed to decode JSON config: {$configPath}: {$e->getMessage()}");
        }

        return self::fromArray($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function fromArray(array $data): self
    {
        $defaults = self::defaults();

        return new self(
            jsAssetsPath: self::getString($data, 'jsAssetsPath', $defaults->jsAssetsPath),
            mainJsBundler: self::getString($data, 'mainJsBundler', $defaults->mainJsBundler),
            cssAssetsPath: self::getString($data, 'cssAssetsPath', $defaults->cssAssetsPath),
            mainCssBundler: self::getString($data, 'mainCssBundler', $defaults->mainCssBundler),
            i18nPath: self::getString($data, 'i18nPath', $defaults->i18nPath),
            migrationsFolderPath: self::getString($data, 'migrationsFolderPath', $defaults->migrationsFolderPath),
            backgroundTasksFolderPath: self::getString(
                data:$data,
                key: 'backgroundTasksFolderPath',
                default: $defaults->backgroundTasksFolderPath
            ),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function getString(array $data, string $key, string $default): string
    {
        $value = $data[$key] ?? $default;
        if (!is_string($value)) {
            return $default;
        }
        return $value;
    }

    /**
     * Normalizes a config value like `./assets/i18n` into `assets/i18n/` for the LanguageSettings constructor.
     */
    public function normalizedI18nAssetsPathForLanguageSettings(): string
    {
        $normalized = self::normalizeRelativePath($this->i18nPath);
        if ($normalized === '') {
            return 'assets/i18n/';
        }

        return rtrim($normalized, '/') . '/';
    }

    public function normalizedMigrationsFolderPath(): string
    {
        return self::normalizeRelativePath($this->migrationsFolderPath);
    }

    public function normalizedBackgroundTasksFolderPath(): string
    {
        return self::normalizeRelativePath($this->backgroundTasksFolderPath);
    }

    /**
     * @return string path without leading `./` or `/`, without trailing `/`.
     */
    private static function normalizeRelativePath(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        if (str_starts_with($path, './')) {
            $path = substr($path, 2);
        }
        $path = ltrim($path, '/');
        return rtrim($path, '/');
    }
}
