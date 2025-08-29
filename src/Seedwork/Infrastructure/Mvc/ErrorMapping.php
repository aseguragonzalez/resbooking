<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc;

final class ErrorMapping
{
    public function __construct(
        public readonly int $statusCode,
        public readonly string $templateName,
        public readonly string $pageTitle
    ) {
    }
}
