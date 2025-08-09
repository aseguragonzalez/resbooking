<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Views;

use Seedwork\Infrastructure\Mvc\Actions\Responses\View;

final class HtmlViewEngine implements ViewEngine
{
    public function __construct(private readonly string $basePath, private readonly ContentReplacer $contentReplacer)
    {
    }

    public function render(View $view): string
    {
        $viewPath = "{$this->basePath}/{$view->viewPath}.html";
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Template not found: {$viewPath}");
        }
        $templateFile = file_get_contents($viewPath);
        // @phpstan-ignore-next-line
        $template = $this->applyLayout($templateFile);

        $body = $this->contentReplacer->replace($view->data, $template);
        // clean empty lines
        return preg_replace("/^\s*\n/m", "", $body) ?? "";
    }

    private function applyLayout(string $template): string
    {
        preg_match("/\{\{#layout (.*?):\}\}/", $template, $matches);
        if ($matches) {
            $layoutFilename = $matches[1];
            $layoutPath = "{$this->basePath}/{$layoutFilename}.html";
            if (!file_exists($layoutPath)) {
                throw new \RuntimeException("Layout not found: {$layoutFilename}");
            }
            /** @var string */
            $layout = file_get_contents($layoutPath);
            $indentation = '';
            if (preg_match('/^(\s*)\{\{content\}\}/m', $layout, $indentMatches)) {
                $indentation = $indentMatches[1];
            }
            $content = str_replace("{{#layout {$layoutFilename}:}}", "", $template);
            return str_replace("{{content}}", (string)preg_replace('/^/m', $indentation, $content), $layout);
        }
        return $template;
    }
}
