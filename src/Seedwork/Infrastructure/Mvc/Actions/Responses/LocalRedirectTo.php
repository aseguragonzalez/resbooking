<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Actions\Responses;

use Seedwork\Infrastructure\Mvc\Responses\Headers\Header;
use Seedwork\Infrastructure\Mvc\Responses\StatusCode;

final class LocalRedirectTo extends ActionResponse
{
    /**
     * @param array<Header> $headers
     */
    public function __construct(
        public readonly string $action,
        public readonly string $controller,
        public readonly ?object $args = null,
        array $headers = [],
    ) {
        parent::__construct(data: $args ?? new \stdClass(), headers: $headers, statusCode: StatusCode::Found);
    }
}
