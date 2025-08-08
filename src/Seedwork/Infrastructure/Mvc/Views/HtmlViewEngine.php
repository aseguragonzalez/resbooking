<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Views;

use Seedwork\Infrastructure\Mvc\Actions\Responses\View;

final class HtmlViewEngine implements ViewEngine
{
    public function __construct(private readonly string $basePath)
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
        // get direct properties
        $tagsToReplace = $view->data == null
            ? [] : $this->replaceObjectProperty(propertyName: "", model: $view->data, template: $template);
        // get branches
        $branchesToReplace = $view->data == null
            ? [] : $this->replaceBranches(model: $view->data, template: $template);

        // Replace branches first:
        $templateWithBranches = str_replace(
            array_keys($branchesToReplace),
            array_values($branchesToReplace),
            $template
        );
        $body = str_replace(array_keys($tagsToReplace), array_values($tagsToReplace), $templateWithBranches);
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

    /**
     * @return array<string, string>
     */
    private function replaceObjectProperty(string $propertyName, object $model, string $template): array
    {
        $prefix = $propertyName == "" ? "" : "{$propertyName}->";
        $tagsToReplace = [];
        $values = get_object_vars($model);
        foreach ($values as $property => $value) {
            $replacePropertyKey = "{{{$prefix}{$property}}}";
            $propertyName = "{$prefix}{$property}";

            if (is_array($value) || (is_object($value) && !$this->isDatetime($value))) {
                $subTagsToReplace = match (true) {
                    is_array($value) => $this->replaceArrayProperty($propertyName, $value, $template),
                    is_object($value) => $this->replaceObjectProperty($propertyName, $value, $template),
                };
                $tagsToReplace = array_merge($tagsToReplace, $subTagsToReplace);
                continue;
            }

            $tagsToReplace[$replacePropertyKey] = match (true) {
                $value instanceof \DateTimeImmutable => $value->format(\DateTime::ISO8601_EXPANDED),
                $value instanceof \DateTime => $value->format(\DateTime::ISO8601_EXPANDED),
                is_bool($value) => $value ? 'true' : 'false',
                is_numeric($value) => "{$value}",
                is_string($value) => $value,
                default => "",
            };
        }
        return $tagsToReplace;
    }

    /**
     * @param mixed $object
     */
    private function isDatetime($object): bool
    {
        return $object instanceof \DateTimeImmutable || $object instanceof \DateTime;
    }

    /**
     * @param array<mixed, mixed> $model
     * @return array<string, string>
     */
    private function replaceArrayProperty(string $propertyName, array $model, string $template): array
    {
        $tagsToReplace = [];
        preg_match_all(
            "/\{\{#for (.*?) in {$propertyName}:\}\}(.*?)\{\{#endfor {$propertyName}:\}\}/s",
            $template,
            $matches,
            PREG_SET_ORDER
        );
        foreach ($matches as $match) {
            $loopVariable = $match[1];
            $blockContent = $match[2];
            $content = '';
            /** @var object|string $item */
            foreach ($model as $item) {
                if (is_string($item)) {
                    $content .= str_replace("{{{$loopVariable}}}", (string)$item, $blockContent);
                    continue;
                }
                $propertiesToReplace = $this->replaceObjectProperty(
                    propertyName: $loopVariable,
                    model: $item,
                    template: $blockContent
                );
                $content .= str_replace(
                    array_keys($propertiesToReplace),
                    array_values($propertiesToReplace),
                    $blockContent
                );
            }
            $tagsToReplace[$match[0]] = $content;
        }
        return $tagsToReplace;
    }

    /**
     * @return array<string, string>
     */
    private function replaceBranches(object $model, string $template): array
    {
        // TODO: allow nested if statements
        $expressionsToReplace = [];
        preg_match_all(
            "/\{\{#if (.*?):\}\}(.*?)\{\{#endif \\1:\}\}/s",
            $template,
            $matches,
            PREG_SET_ORDER
        );
        foreach ($matches as $match) {
            $expression = $match[1];
            $blockContent = $match[2];
            $expressionsToReplace[$match[0]] = $this->checkExpression($expression, $model) ? $blockContent : '';
        }
        return $expressionsToReplace;
    }

    private function checkExpression(string $expression, object $model): bool
    {
        $parts = explode('->', trim(str_replace(['!', '()'], ['', ''], $expression)));
        $path = $model;
        foreach ($parts as $next) {
            // @phpstan-ignore-next-line
            if (property_exists($path, $next)) {
                $path = $path->$next;
            // @phpstan-ignore-next-line
            } elseif (method_exists($path, $next)) {
                $path = $path->$next();
            } elseif (preg_match('/^(\w+)\[(.*?)\]$/', $next, $arrayMatches)) {
                // NOTE: check if next is an key-value dict (array)
                $property = $arrayMatches[1];
                $key = $arrayMatches[2];
                $cleanKey = str_replace(['"', "'"], '', $key);
                if (
                    // @phpstan-ignore-next-line
                    property_exists($path, $property)
                    && is_array($path->$property)
                    && array_key_exists($cleanKey, $path->$property)
                ) {
                    $path = $path->$property[$cleanKey];
                }
            } else {
                $path = null;
                break;
            }
        }
        return str_starts_with($expression, '!') ? !(bool)$path : (bool)$path;
    }
}
