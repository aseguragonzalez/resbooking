<?php

declare(strict_types=1);

namespace Framework\Mvc\Views;

use Framework\Files\FileManager;
use Framework\Mvc\LanguageSettings;
use Framework\Mvc\Requests\RequestContext;
use Framework\Mvc\Requests\RequestContextKeys;

final class I18nReplacer extends ContentReplacerBase
{
    public function __construct(
        private readonly LanguageSettings $settings,
        private readonly FileManager $fileManager,
        BranchesReplacer $nextReplacer
    ) {
        parent::__construct($nextReplacer);
    }

    protected function customReplace(?object $model, string $template, RequestContext $context): string
    {
        $language = $context->get(RequestContextKeys::Language->value);
        $file = "{$this->settings->i18nPath}/{$language}.json";
        $languageKeyValueJson = $this->fileManager->readKeyValueJson($file);
        $keys = array_map(fn ($key) => "{{{$key}}}", array_keys($languageKeyValueJson));
        return str_replace($keys, array_values($languageKeyValueJson), $template);
    }
}
