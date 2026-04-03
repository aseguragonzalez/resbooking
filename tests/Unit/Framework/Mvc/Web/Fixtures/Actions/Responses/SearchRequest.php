<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Fixtures\Actions\Responses;

final class SearchRequest
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
    ) {
    }
}
