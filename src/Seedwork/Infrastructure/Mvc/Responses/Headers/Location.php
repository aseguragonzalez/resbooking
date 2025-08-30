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
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (
            !filter_var($url, FILTER_VALIDATE_URL)
            || !is_string($scheme)
            || !in_array($scheme, ['http', 'https'])
        ) {
            throw new \InvalidArgumentException("Invalid URL provided for Location header");
        }
        return new Location($url);
    }
}
