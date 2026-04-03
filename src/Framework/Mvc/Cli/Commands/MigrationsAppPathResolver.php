<?php

declare(strict_types=1);

namespace Framework\Mvc\Commands;

use Framework\Mvc\Config\MvcConfig;

/**
 * Resolves the migrations module index.php from the MVC app root or from an explicit leaf directory.
 */
final class MigrationsAppPathResolver
{
    /**
     * Absolute path to index.php under the configured migrations module, or null when missing.
     */
    public static function resolveIndexPath(string $appPath): ?string
    {
        $appPath = rtrim($appPath, '/');
        $configPath = $appPath . '/' . MvcConfig::CONFIG_FILENAME;
        if (!is_file($configPath)) {
            return null;
        }

        $config = MvcConfig::load($appPath);
        $moduleRelative = $config->effectiveMigrationsModuleRelativePath();
        $path = $appPath . '/' . $moduleRelative . '/index.php';

        return is_file($path) ? $path : null;
    }

    /**
     * Resolves index.php next to a leaf migrations directory (nested …/migrations)
     * or flat module layout (leaf is module root).
     */
    public static function resolveIndexPathFromLeafDir(string $leafDir): ?string
    {
        $leafDir = rtrim($leafDir, '/\\');
        $base = basename($leafDir);
        if ($base === 'migrations') {
            $candidate = dirname($leafDir) . '/index.php';

            return is_file($candidate) ? $candidate : null;
        }

        $candidate = $leafDir . '/index.php';

        return is_file($candidate) ? $candidate : null;
    }
}
