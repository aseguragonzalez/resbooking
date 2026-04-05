<?php

declare(strict_types=1);

namespace Framework\Web;

/**
 * Joins an application base directory with a relative path segment for filesystem layout
 * (views, i18n assets, etc.).
 */
final class AppFilesystemPath
{
    public static function join(string $basePath, string $relativePath): string
    {
        $base = rtrim($basePath, '/\\');
        $relativePath = str_replace('\\', '/', $relativePath);
        $relativePath = ltrim($relativePath, '/');

        return $base === '' ? $relativePath : $base . '/' . $relativePath;
    }
}
