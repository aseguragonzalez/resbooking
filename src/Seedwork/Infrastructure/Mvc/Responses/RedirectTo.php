<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses;

use Seedwork\Infrastructure\Mvc\Responses\Headers\{ContentType, Header, Location};

final class RedirectTo extends Response
{
    /**
     * @param array<Header> $headers
     */
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

        $customHeaders = [Location::new(url: "{$normalizedUrl}?{$queryString}")];
        if (empty(array_filter($headers, fn (Header $header) => $header instanceof ContentType === true))) {
            $customHeaders[] = ContentType::html();
        }

        parent::__construct($data, headers: array_merge($headers, $customHeaders), statusCode: StatusCode::Found);
    }
}
