<?php

declare(strict_types=1);

namespace Framework\Mvc\Actions\Responses;

use Framework\Mvc\Responses\Headers\ContentType;
use Framework\Mvc\Responses\Headers\Header;
use Framework\Mvc\Responses\Headers\Location;
use Framework\Mvc\Responses\StatusCode;

final class RedirectTo extends ActionResponse
{
    /**
     * @param array<Header> $headers
     */
    private function __construct(public readonly string $url, array $headers = [])
    {
        parent::__construct(data: new \stdClass(), headers: $headers, statusCode: StatusCode::Found);
    }

    /**
     * @param string $url The URL to redirect to ({scheme}//{host}/{path})
     * @param array<string, mixed>|null $args
     * @param array<Header> $headers
     */
    public static function create(string $url, ?array $args = [], array $headers = []): self
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException("Invalid URL: $url");
        }
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new \InvalidArgumentException("Invalid URL: $url");
        }
        $urlBlocks = [strtolower($url)];
        if (!empty($args)) {
            $urlBlocks[] = http_build_query($args);
        }
        $urlWithQueryParams = implode('?', $urlBlocks);
        $newHeaders = [Location::toUrl($urlWithQueryParams)];
        if (empty(array_filter($headers, fn (Header $header) => $header instanceof ContentType === true))) {
            $newHeaders[] = ContentType::html();
        }
        return new self($urlWithQueryParams, array_merge($headers, $newHeaders));
    }
}
