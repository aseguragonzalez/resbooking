<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures\Actions;

final class EmbeddedObject
{
    /**
     * @param array<bool> $boolItems
     * @param array<\DateTime> $dateTimeItems
     * @param array<\DateTimeImmutable> $dateTimeImmutableItems
     * @param array<float> $floatItems
     * @param array<int> $intItems
     * @param array<string> $stringItems
     */
    public function __construct(
        public readonly array $boolItems = [],
        public readonly array $dateTimeItems = [],
        public readonly array $dateTimeImmutableItems = [],
        public readonly array $floatItems = [],
        public readonly array $intItems = [],
        public readonly array $stringItems = [],
    ) {
    }
}
