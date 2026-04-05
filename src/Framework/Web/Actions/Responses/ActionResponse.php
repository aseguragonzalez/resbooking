<?php

declare(strict_types=1);

namespace Framework\Web\Actions\Responses;

use Framework\Web\Responses\Headers\Header;
use Framework\Web\Responses\StatusCode;

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
