<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Controllers;

use Seedwork\Infrastructure\Mvc\Responses\{Response, StatusCode};

final class LocalRedirectTo extends Response
{
    public function __construct(
        public readonly string $controller,
        public readonly string $action,
        public readonly ?object $args = null,
        array $headers = [],
    ) {
        parent::__construct(data: $args ?? new \stdClass(), headers: $headers, statusCode: StatusCode::Found);
    }
}
