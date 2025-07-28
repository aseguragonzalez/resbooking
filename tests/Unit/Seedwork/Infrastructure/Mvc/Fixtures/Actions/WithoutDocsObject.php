<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Actions;

final class WithoutDocsObject
{
    // @phpstan-ignore-next-line
    public function __construct(public readonly array $items = [])
    {
    }
}
