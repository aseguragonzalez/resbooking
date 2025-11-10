<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Actions\Responses;

use Seedwork\Infrastructure\Mvc\Responses\Headers\Header;
use Seedwork\Infrastructure\Mvc\Responses\Headers\ContentType;
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;

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
