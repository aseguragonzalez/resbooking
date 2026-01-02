<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses\Headers;

final class ContentLanguage extends Header
{
    public function __construct(
        private bool $english = false,
        private bool $french = false,
        private bool $spanish = false,
        private bool $portuguese = false,
        private bool $german = false,
        private bool $italian = false,
        private bool $dutch = false,
        private bool $russian = false,
    ) {
        parent::__construct('Content-Language', $this->buildValue());
    }

    public static function createFromCurrentLanguage(string $language): ContentLanguage
    {
        if (!in_array($language, ['en', 'fr', 'es', 'pt', 'de', 'it', 'nl', 'ru'], true)) {
            throw new \InvalidArgumentException("Invalid language: $language");
        }

        return new self(
            english: $language === 'en',
            french: $language === 'fr',
            spanish: $language === 'es',
            portuguese: $language === 'pt',
            german: $language === 'de',
            italian: $language === 'it',
            dutch: $language === 'nl',
            russian: $language === 'ru',
        );
    }

    private function buildValue(): string
    {
        $languages = [];

        if ($this->english) {
            $languages[] = 'en';
        }

        if ($this->french) {
            $languages[] = 'fr';
        }

        if ($this->spanish) {
            $languages[] = 'es';
        }

        if ($this->portuguese) {
            $languages[] = 'pt';
        }

        if ($this->german) {
            $languages[] = 'de';
        }

        if ($this->italian) {
            $languages[] = 'it';
        }

        if ($this->dutch) {
            $languages[] = 'nl';
        }

        if ($this->russian) {
            $languages[] = 'ru';
        }

        return implode(', ', $languages);
    }
}
