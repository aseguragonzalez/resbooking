<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Actions\Responses;

use Seedwork\Infrastructure\Mvc\Responses\Headers\Header;
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;

abstract class ActionResponse
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
