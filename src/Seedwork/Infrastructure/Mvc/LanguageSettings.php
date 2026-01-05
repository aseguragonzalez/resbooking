<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

final readonly class LanguageSettings
{
    /**
     * @param array<string> $languages
     */
    public function __construct(
        public string $i18nPath,
        public array $languages = ['en'],
        public string $cookieName = 'language',
        public string $defaultValue = 'en',
        public string $setUrl = '/set-language',
    ) {
    }
}
