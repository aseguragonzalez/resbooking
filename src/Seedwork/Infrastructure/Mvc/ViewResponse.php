<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

final class ViewResponse extends Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public readonly string $viewPath,
        ?object $data = null,
        array $headers = [],
        StatusCode $statusCode = StatusCode::Ok
    ) {
        parent::__construct(headers: $headers, statusCode: $statusCode, data: $data ?? new \stdClass());
    }
}
