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
        // TODO: Validate URL
        return new Location($url);
    }
}
