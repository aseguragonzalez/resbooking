<?php

declare(strict_types=1);

namespace Framework\Mvc\Actions\Responses;

use Framework\Mvc\Responses\Headers\Header;
use Framework\Mvc\Responses\Headers\ContentType;
use Framework\Mvc\Responses\StatusCode;

final class View extends ActionResponse
{
    /**
     * @param array<Header> $headers
     */
    public function __construct(
        public readonly string $viewPath,
        ?object $data = null,
        array $headers = [],
        StatusCode $statusCode = StatusCode::Ok
    ) {
        $headers = array_merge($headers, [ContentType::html()]);
        parent::__construct(headers: $headers, statusCode: $statusCode, data: $data ?? new \stdClass());
    }
}
