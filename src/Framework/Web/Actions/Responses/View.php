<?php

declare(strict_types=1);

namespace Framework\Actions\Responses;

use Framework\Responses\Headers\Header;
use Framework\Responses\Headers\ContentType;
use Framework\Responses\StatusCode;

final class View extends ActionResponse
{
    /**
     * @param array<Header> $headers
     * @param array<string, mixed>|object|null $data
     */
    public function __construct(
        public readonly string $viewPath,
        array|object|null $data = null,
        array $headers = [],
        StatusCode $statusCode = StatusCode::Ok
    ) {
        $headers = array_merge($headers, [ContentType::html()]);
        parent::__construct(headers: $headers, statusCode: $statusCode, data: $data ?? new \stdClass());
    }
}
