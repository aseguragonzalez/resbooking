<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

final class View extends Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(private string $path, object $data, array $headers, StatusCode $statusCode)
    {
        parent::__construct(headers: $headers, statusCode: $statusCode, data: $data);
    }

    public function getBody(): string
    {
        $template = file_get_contents($this->path);
        if (!$template) {
            throw new \RuntimeException("Template not found: {$this->path}");
        }
        $tags_to_replace = $this->replaceObjectProperty(
            propertyName: "",
            model: $this->data,
            template: $template
        );
        $body = str_replace(array_keys($tags_to_replace), array_values($tags_to_replace), $template);
        // clean empty lines
        return preg_replace("/^\s*\n/m", '', $body) ?? "";
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
}
