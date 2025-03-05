<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses;

use Seedwork\Infrastructure\Mvc\Responses\Headers\Header;

abstract class Response
{
    /**
     * @param array<Header> $headers
     */
    public function __construct(
        public readonly object $data,
        public readonly array $headers,
        public readonly StatusCode $statusCode,
    ) {
    }
}
