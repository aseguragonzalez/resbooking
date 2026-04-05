<?php

declare(strict_types=1);

namespace Framework\Cli\Commands;

use Framework\Web\Config\MvcConfig;

final class MigrationsLeafPathResolver
{
    /**
     * Resolves the absolute path to the leaf migrations directory (parent of timestamped migration folders).
     * Prefers `module/migrations/`; if absent, uses a flat `module/` layout when `module/index.php` exists
     * and timestamp-like subdirectories are present.
     *
     * Returns null when the feature is disabled, config is missing, or the directory does not exist.
     *
     * @param bool $ignoreDisabled When true, resolves even if `migrationsEnabled` is false in config.
     */
    public static function resolveLeafMigrationsDir(string $appPath, bool $ignoreDisabled = false): ?string
    {
        $appPath = rtrim($appPath, '/');
        $configPath = $appPath . '/' . MvcConfig::CONFIG_FILENAME;
        if (!is_file($configPath)) {
            return null;
        }

        $config = MvcConfig::load($appPath);
        if (!$ignoreDisabled && !$config->isMigrationsEnabled()) {
            return null;
        }

        $moduleRelative = $config->effectiveMigrationsModuleRelativePath();
        $moduleRoot = $appPath . '/' . $moduleRelative;

        $nestedLeaf = $moduleRoot . '/migrations';
        if (is_dir($nestedLeaf)) {
            return $nestedLeaf;
        }

        if (is_file($moduleRoot . '/index.php') && self::hasMigrationTimestampSubdirs($moduleRoot)) {
            return $moduleRoot;
        }

        return null;
    }

    private static function hasMigrationTimestampSubdirs(string $path): bool
    {
        if (!is_dir($path)) {
            return false;
        }

        $entries = scandir($path);
        if ($entries === false) {
            return false;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            if (is_dir($path . '/' . $entry) && preg_match('/^\d{8,}/', $entry) === 1) {
                return true;
            }
        }

        return false;
    }
}
