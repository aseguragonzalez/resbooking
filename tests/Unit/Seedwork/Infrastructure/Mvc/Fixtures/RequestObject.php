<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Fixtures;

use Tuupola\Ksuid;
use Tuupola\KsuidFactory;

final class RequestObject
{
    /**
     * @param array<int> $items
     * @param array<Ksuid> $ksuidArray
     * @param array<InnerTypeObject> $customClassType
     */
    public function __construct(
        public readonly int $id = 0,
        public readonly float $amount = 0.0,
        public readonly string $name = '',
        public readonly string $uuid = '',
        private readonly ?string $ksuid = null,
        public readonly ?\DateTime $date = null,
        public readonly ?\DateTimeImmutable $dateImmutable = null,
        public readonly bool $active = false,
        public readonly ?InnerTypeObject $innerTypeObject = null,
        public readonly array $items = [],
        public readonly array $ksuidArray = [],
        public readonly array $customClassType = [],
    ) {
    }

    public function getKsuid(): ?Ksuid
    {
        return is_null($this->ksuid) ? null : KsuidFactory::fromString($this->ksuid);
    }
}
