<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Requests;

final class EditRequest
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email
    ) {
    }
}
