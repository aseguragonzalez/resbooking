<?php

declare(strict_types=1);

namespace Infrastructure\Ports\Dashboard\Models;

abstract class PageModel
{
    protected function __construct(
        public readonly string $pageTitle,
    ) {
    }
}
