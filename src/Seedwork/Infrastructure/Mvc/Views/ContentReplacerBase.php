<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Views;

abstract class ContentReplacerBase implements ContentReplacer
{
    protected function __construct(private ?ContentReplacer $nextReplacer = null)
    {
    }

    public function setNext(ContentReplacer $replacer): void
    {
        $this->nextReplacer = $replacer;
    }

    public function replace(object $model, string $template): string
    {
        if ($this->nextReplacer === null) {
            return $this->customReplace($model, $template);
        }
        $replacedTemplate = $this->nextReplacer->replace($model, $template);
        return $this->customReplace($model, $replacedTemplate);
    }

    abstract protected function customReplace(object $model, string $template): string;
}
