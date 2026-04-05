<?php

declare(strict_types=1);

namespace Framework\Web\Views;

use Framework\Web\Actions\Responses\View;
use Framework\Web\UiAssetsSettings;
use Framework\Web\Requests\RequestContext;

final class HtmlViewEngine implements ViewEngine
{
    /** @var array<string, string> */
    private static array $templateCache = [];

    public function __construct(
        private readonly string $viewsRoot,
        private readonly ContentReplacer $contentReplacer,
        private readonly ?UiAssetsSettings $uiAssetsSettings = null,
    ) {
    }

    public function render(View $view, RequestContext $context): string
    {
        $viewPath = "{$this->viewsRoot}/{$view->viewPath}.html";
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Template not found: {$viewPath}");
        }
        if (!array_key_exists($viewPath, self::$templateCache)) {
            $templateFile = file_get_contents($viewPath);
            if ($templateFile === false) {
                throw new \RuntimeException("Could not read template: {$viewPath}");
            }
            self::$templateCache[$viewPath] = $templateFile;
        }
        $templateFile = self::$templateCache[$viewPath];

        // add current identity to the model
        $currentIdentity = $context->getIdentity();
        $contextModel = [
            'user' => (object)[
                'username' => $currentIdentity->username(),
                'isAuthenticated' => $currentIdentity->isAuthenticated(),
            ]
        ];

        if ($this->uiAssetsSettings !== null) {
            $contextModel = array_merge($contextModel, [
                'jsAssetsPathUrl' => $this->uiAssetsSettings->jsAssetsPathUrl,
                'mainJsBundler' => $this->uiAssetsSettings->mainJsBundler,
                'cssAssetsPathUrl' => $this->uiAssetsSettings->cssAssetsPathUrl,
                'mainCssBundler' => $this->uiAssetsSettings->mainCssBundler,
            ]);
        }
        $viewData = $view->data;
        /** @var array<string, mixed> $model */
        $model = array_merge(is_array($viewData) ? $viewData : (array) $viewData, $contextModel);

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
            $layoutPath = "{$this->viewsRoot}/{$layoutFilename}.html";
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
