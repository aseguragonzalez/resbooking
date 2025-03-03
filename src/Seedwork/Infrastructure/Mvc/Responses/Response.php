<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses;

abstract class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public readonly object $data,
        public readonly array $headers,
        public readonly StatusCode $statusCode,
    ) {
    }
}
