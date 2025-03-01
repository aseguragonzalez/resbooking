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
        return $this->replaceObjectProperty(propertyName: "", template: $template, data: $this->data);
    }

    private function replaceObjectProperty(string $propertyName, string $template, object $data): string
    {
        $prefix = $propertyName == "" ? "" : "{$propertyName}->";
        /* @var array $keys */
        $keys = [];
        /* @var array $values */
        $values = [];
        foreach ((array)$data as $property => $value) {
            $replace_property_key = "{{{$prefix}{$property}}}";
            $keys[] = $replace_property_key;
            if ($value instanceof \DateTimeImmutable) {
                $formated_date = $value->format(\DateTime::ISO8601_EXPANDED);
                $values[] = $formated_date;
                # $template = str_replace($replace_property_key, $formated_date, $template);
                continue;
            }
            if (is_bool($value)) {
                $formated_value = $value ? 'true' : 'false';
                $values[] = $formated_value;
                # $template = str_replace($replace_property_key, $formated_value, $template);
                continue;
            }
            if (is_numeric($value)) {
                $values[] = "{$value}";
                # $template = str_replace($replace_property_key, "{$value}", $template);
                continue;
            }

            if (is_string($value)) {
                $values[] = $value;
                # $template = str_replace($replace_property_key, $value, $template);
                continue;
            }
            # $template = str_replace($replace_property_key, "{$value}", $template);
        }
        return str_replace($keys, $values, $template);
        # return $template;
    }
}
