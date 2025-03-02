<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

final class ViewEngine
{
    public function __construct(private readonly string $basePath)
    {
    }

    public function render(ViewResponse $view): string
    {
        $viewPath = "{$this->basePath}/{$view->name}.html";
        $templateFile = file_get_contents($viewPath);
        if (!$templateFile) {
            throw new \RuntimeException("Template not found: {$viewPath}");
        }
        // use layout if defined
        $template = $this->applyLayout($templateFile);
        // get direct properties
        $tags_to_replace = $view->data == null
            ? [] : $this->replaceObjectProperty(propertyName: "", model: $view->data, template: $template);
        // get branches
        $branches_to_replace = $view->data == null
            ? [] : $this->replaceBranches(model: $view->data, template: $template);
        $replacements = array_merge($tags_to_replace, $branches_to_replace);
        $body = str_replace(array_keys($replacements), array_values($replacements), $template);
        // clean empty lines
        return preg_replace("/^\s*\n/m", "", $body) ?? "";
    }

    private function applyLayout(string $template): string
    {
        preg_match("/\{\{#layout (.*?):\}\}/", $template, $matches);
        if ($matches) {
            $layoutFilename = $matches[1];
            $layout = file_get_contents("{$this->basePath}/{$layoutFilename}.html");
            if (!$layout) {
                throw new \RuntimeException("Layout not found: {$layoutFilename}");
            }
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
        /* @var array<string, string> $tags_to_replace */
        $tags_to_replace = [];
        $values = get_object_vars($model);
        foreach ($values as $property => $value) {
            # TODO: try to use match pattern using gettype($value)
            $replace_property_key = "{{{$prefix}{$property}}}";
            if ($value instanceof \DateTimeImmutable or $value instanceof \DateTime) {
                $tags_to_replace[$replace_property_key] = $value->format(\DateTime::ISO8601_EXPANDED);
                continue;
            }

            if (is_array($value)) {
                $tags_to_replace = array_merge(
                    $tags_to_replace,
                    $this->replaceArrayProperty(
                        propertyName: "{$prefix}{$property}",
                        model: $value,
                        template: $template
                    )
                );
                continue;
            }

            if (is_object($value)) {
                $tags_to_replace = array_merge(
                    $tags_to_replace,
                    $this->replaceObjectProperty(
                        propertyName: "{$prefix}{$property}",
                        model: $value,
                        template: $template
                    )
                );
                continue;
            }

            if (is_bool($value)) {
                $tags_to_replace[$replace_property_key] = $value ? 'true' : 'false';
                continue;
            }

            if (is_numeric($value)) {
                # TODO: format number
                $tags_to_replace[$replace_property_key] = "{$value}";
                continue;
            }

            if (is_string($value)) {
                $tags_to_replace[$replace_property_key] = $value;
                continue;
            }
        }
        return $tags_to_replace;
    }

    /**
     * @param array<mixed, mixed> $model
     * @return array<string, string>
     */
    private function replaceArrayProperty(string $propertyName, array $model, string $template): array
    {
        $tags_to_replace = [];
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
            foreach ($model as $item) {
                if (!is_object($item)) {
                    continue;
                }

                $properties_to_replace = $this->replaceObjectProperty(
                    propertyName: $loopVariable,
                    model: $item,
                    template: $blockContent
                );
                $content .= str_replace(
                    array_keys($properties_to_replace),
                    array_values($properties_to_replace),
                    $blockContent
                );
            }
            $tags_to_replace[$match[0]] = $content;
        }
        return $tags_to_replace;
    }

    /**
     * @return array<string, string>
     */
    private function replaceBranches(object $model, string $template): array
    {
        $expressions_to_replace = [];
        preg_match_all(
            "/\{\{#if (.*?):\}\}(.*?)\{\{#endif:\}\}/s",
            $template,
            $matches,
            PREG_SET_ORDER
        );
        foreach ($matches as $match) {
            $expression = $match[1];
            $blockContent = $match[2];
            $expressions_to_replace[$match[0]] = $this->checkExpression($expression, $model) ? $blockContent : '';
        }
        return $expressions_to_replace;
    }

    private function checkExpression(string $expression, object $model): bool
    {
        $expression_path = trim(str_replace(['!', '()'], ['', ''], $expression));
        $parts = explode('->', $expression_path);
        $value = $model;
        foreach ($parts as $part) {
            if (!is_object($value)) {
                return false;
            }
            if (method_exists($value, $part)) {
                $value = $value->$part();
            } elseif (property_exists($value, $part)) {
                $value = $value->$part;
            } else {
                return false;
            }
        }
        if (str_starts_with($expression, '!')) {
            return !(bool)$value;
        }
        return (bool) $value;
    }
}
