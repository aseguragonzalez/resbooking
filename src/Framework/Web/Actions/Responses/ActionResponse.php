<?php

declare(strict_types=1);

namespace Framework\Actions\Responses;

use Framework\Responses\Headers\Header;
use Framework\Responses\StatusCode;

abstract class ActionResponse
{
    /**
     * @param array<Header> $headers
     * @param array<string, mixed>|object $data
     */
    public function __construct(
        public readonly array|object $data,
        public readonly array $headers,
        public readonly StatusCode $statusCode,
    ) {
    }
}
