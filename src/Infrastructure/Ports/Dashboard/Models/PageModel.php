<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models;

abstract readonly class PageModel
{
    protected function __construct(
        public string $pageTitle,
    ) {
    }
}
