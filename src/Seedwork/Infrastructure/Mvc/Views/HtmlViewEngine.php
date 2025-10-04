<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Views;

use Seedwork\Infrastructure\Mvc\Actions\Responses\View;
use Seedwork\Infrastructure\Mvc\Requests\RequestContext;
use Seedwork\Infrastructure\Mvc\Settings;

final class HtmlViewEngine implements ViewEngine
{
    public function __construct(private readonly Settings $settings, private readonly ContentReplacer $contentReplacer)
    {
    }

    public function render(View $view, RequestContext $context): string
    {
        $viewPath = "{$this->settings->viewPath}/{$view->viewPath}.html";
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Template not found: {$viewPath}");
        }
        $templateFile = file_get_contents($viewPath);

        // add current identity to the model
        $currentIdentity = $context->getIdentity();
        $contextModel = [
            'user' => (object)[
                'username' => $currentIdentity->username(),
                'isAuthenticated' => $currentIdentity->isAuthenticated(),
            ]
        ];
        $model = $view->data == null ? (object)$contextModel : (object)array_merge((array)$view->data, $contextModel);

        // @phpstan-ignore-next-line
        $template = $this->applyLayout($templateFile);

        $body = $this->contentReplacer->replace($model, $template, $context);
        // clean empty lines
        return preg_replace("/^\s*\n/m", "", $body) ?? "";
    }

    private function applyLayout(string $template): string
    {
        preg_match("/\{\{#layout (.*?):\}\}/", $template, $matches);
        if ($matches) {
            $layoutFilename = $matches[1];
            $layoutPath = "{$this->settings->viewPath}/{$layoutFilename}.html";
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
