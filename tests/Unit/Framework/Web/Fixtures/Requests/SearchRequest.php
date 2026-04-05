<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Fixtures\Requests;

final class SearchRequest
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email = null,
    ) {
    }
}
