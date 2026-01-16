<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Fixtures\Actions;

final class RequestObject
{
    /**
     * @param array<bool> $boolItems
     * @param array<\DateTime> $dateTimeItems
     * @param array<\DateTimeImmutable> $dateTimeImmutableItems
     * @param array<float> $floatItems
     * @param array<int> $intItems
     * @param array<string> $stringItems
     * @param array<InnerTypeObject> $customClassType
     */
    public function __construct(
        public readonly int $id = 0,
        public readonly float $amount = 0.0,
        public readonly string $name = '',
        public readonly string $uuid = '',
        public readonly ?\DateTime $date = null,
        public readonly ?\DateTimeImmutable $dateImmutable = null,
        public readonly bool $active = false,
        public readonly ?InnerTypeObject $innerTypeObject = null,
        public readonly array $boolItems = [],
        public readonly array $dateTimeItems = [],
        public readonly array $dateTimeImmutableItems = [],
        public readonly array $floatItems = [],
        public readonly array $intItems = [],
        public readonly array $stringItems = [],
        public readonly array $customClassType = [],
        public readonly ?EmbeddedObject $embeddedObject = null,
    ) {
    }
}
