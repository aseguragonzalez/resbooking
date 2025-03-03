<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

final class RedirectToResponse extends Response
{
    public function __construct(
        public readonly string $url,
        public readonly ?object $args = null,
        array $headers = [],
    ) {
        $data = $args ?? new \stdClass();
        // normalize url
        $normalizedUrl = strtolower(filter_var($url, FILTER_SANITIZE_URL) ? $url : '');
        if (!preg_match('/^https?:\/\//', $normalizedUrl) && !str_starts_with($normalizedUrl, '/')) {
            $normalizedUrl = '/' . $normalizedUrl;
        }
        // create queryString from arguments
        $queryString = http_build_query(get_object_vars($data));
        $updatedHeaders = array_merge($headers, ['Location' => "{$normalizedUrl}?{$queryString}"]);
        parent::__construct(data: $data, headers: $updatedHeaders, statusCode: StatusCode::Found);
    }
}
