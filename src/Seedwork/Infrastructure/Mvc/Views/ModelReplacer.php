<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Views;

use Seedwork\Infrastructure\Mvc\Requests\RequestContext;

final class ModelReplacer extends ContentReplacerBase
{
    public function __construct(?ContentReplacer $nextReplacer = null)
    {
        parent::__construct($nextReplacer);
    }

    protected function customReplace(?object $model, string $template, RequestContext $context): string
    {
        $tagsToReplace = $model === null ? [] : $this->replaceObjectProperty("", $model, $template);

        return str_replace(array_keys($tagsToReplace), array_values($tagsToReplace), $template);
    }

    /**
     * @return array<string, string>
     */
    private function replaceObjectProperty(string $propertyName, object $model, string $template): array
    {
        $prefix = $propertyName === "" ? "" : "{$propertyName}->";
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
}
