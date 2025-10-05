<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Views;

use Seedwork\Infrastructure\Files\FileManager;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Requests\RequestContextKeys;
use Seedwork\Infrastructure\Mvc\Settings;

final class I18nReplacer extends ContentReplacerBase
{
    public function __construct(
        private readonly Settings $settings,
        private readonly FileManager $fileManager,
        ?ContentReplacer $nextReplacer = null
    ) {
        parent::__construct($nextReplacer);
    }

    protected function customReplace(?object $model, string $template, RequestContext $context): string
    {
        $language = $context->get(RequestContextKeys::LANGUAGE->value);
        $file = "{$this->settings->i18nPath}/{$language}.json";
        $languageKeyValueJson = $this->fileManager->readKeyValueJson($file);
        $keys = array_map(fn ($key) => "{{{$key}}}", array_keys($languageKeyValueJson));
        return str_replace($keys, array_values($languageKeyValueJson), $template);
    }
}
