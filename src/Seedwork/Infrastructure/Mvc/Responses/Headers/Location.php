<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses\Headers;

final class Location extends Header
{
    public function __construct(string $value)
    {
        parent::__construct('Location', $value);
    }

    public static function new(string $url): Location
    {
        if (!filter_var($url, FILTER_VALIDATE_URL) || !in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'])) {
            throw new \InvalidArgumentException("Invalid URL provided for Location header: $url");
        }
        return new Location($url);
    }
}
