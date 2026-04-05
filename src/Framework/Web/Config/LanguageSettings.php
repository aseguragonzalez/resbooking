<?php

declare(strict_types=1);

namespace Framework\Web\Config;

use Framework\Web\AppFilesystemPath;

/**
 * Localization cookie, allowed languages, and filesystem path to i18n JSON assets.
 */
final readonly class LanguageSettings
{
    public string $i18nPath;

    /**
     * @param array<string> $languages
     */
    public function __construct(
        string $basePath,
        string $assetsPath = 'assets/i18n/',
        public array $languages = ['en'],
        public string $cookieName = 'lang',
        public string $defaultValue = 'en',
        public string $setUrl = '/set-language',
    ) {
        $this->i18nPath = AppFilesystemPath::join($basePath, $assetsPath);
    }
}
