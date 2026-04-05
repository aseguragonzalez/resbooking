<?php

declare(strict_types=1);

namespace Framework;

/** View and HTTP status used when rendering an error page for a mapped exception type. */
final readonly class ErrorMapping
{
    public function __construct(
        public int $statusCode,
        public string $templateName,
        public string $pageTitle
    ) {
    }
}
