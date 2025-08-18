<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Views;

use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Requests\RequestContextKeys;

final class I18nReplacer extends ContentReplacerBase
{
    public function __construct(
        private readonly RequestContext $requestContext,
        private readonly string $basePath,
        ?ContentReplacer $nextReplacer = null
    ) {
        parent::__construct($nextReplacer);
    }

    protected function customReplace(object $model, string $template): string
    {
        $language = $this->requestContext->get(RequestContextKeys::LANGUAGE->value);
        $file = "{$this->basePath}/{$language}.json";
        if (!file_exists($file)) {
            throw new \RuntimeException("Language file not found: {$file}");
        }

        $languageFile = file_get_contents($file);
        if ($languageFile === false) {
            throw new \RuntimeException("Failed to read language file: {$file}");
        }

        /**
         * @var array<string, string> | null $dictionary
         */
        $dictionary = json_decode($languageFile, true);
        if ($dictionary === null) {
            throw new \RuntimeException("Failed to decode language file: " . json_last_error_msg());
        }

        $keys = array_map(fn($key) => "{{{$key}}}", array_keys($dictionary));
        return str_replace($keys, array_values($dictionary), $template);
    }
}
