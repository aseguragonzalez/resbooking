<?php

declare(strict_types=1);

namespace Framework\Mvc\Views;

use Framework\Mvc\Requests\RequestContext;

abstract class ContentReplacerBase implements ContentReplacer
{
    protected function __construct(private ?ContentReplacer $nextReplacer = null)
    {
    }

    public function setNext(ContentReplacer $replacer): void
    {
        $this->nextReplacer = $replacer;
    }

    public function replace(?object $model, string $template, RequestContext $context): string
    {
        if ($this->nextReplacer === null) {
            return $this->customReplace($model, $template, $context);
        }
        $replacedTemplate = $this->nextReplacer->replace($model, $template, $context);
        return $this->customReplace($model, $replacedTemplate, $context);
    }

    abstract protected function customReplace(?object $model, string $template, RequestContext $context): string;
}
