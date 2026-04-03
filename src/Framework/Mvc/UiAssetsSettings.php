<?php

declare(strict_types=1);

namespace Framework\Mvc;

use Framework\Mvc\Config\MvcConfig;

final readonly class UiAssetsSettings
{
    public function __construct(
        public string $jsAssetsPathUrl,
        public string $mainJsBundler,
        public string $cssAssetsPathUrl,
        public string $mainCssBundler,
    ) {
    }

    public static function fromConfig(MvcConfig $config): self
    {
        return new self(
            jsAssetsPathUrl: self::toUrlBasePath($config->jsAssetsPath),
            mainJsBundler: self::normalizeBundlerFileName($config->mainJsBundler),
            cssAssetsPathUrl: self::toUrlBasePath($config->cssAssetsPath),
            mainCssBundler: self::normalizeBundlerFileName($config->mainCssBundler),
        );
    }

    /**
     * @return string URL base path without trailing slash, e.g. `/assets/scripts`
     */
    private static function toUrlBasePath(string $relativePath): string
    {
        $relativePath = trim($relativePath);

        if ($relativePath === '') {
            return '/';
        }

        if (str_starts_with($relativePath, './')) {
            $relativePath = substr($relativePath, 2);
        }

        $relativePath = ltrim($relativePath, '/');
        $relativePath = rtrim($relativePath, '/');

        return '/' . $relativePath;
    }

    private static function normalizeBundlerFileName(string $fileName): string
    {
        $fileName = trim($fileName);
        $fileName = ltrim($fileName, '/');
        return $fileName;
    }
}
