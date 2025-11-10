<?php

declare(strict_types=1);

namespace Infrastructure\Adapters\Repositories\Projects\Models;

final readonly class OpenCloseEvent implements \JsonSerializable
{
    public function __construct(
        public \DateTimeImmutable $date,
        public bool $isAvailable,
        public int $turnId,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'date' => $this->date->format('Y-m-d'),
            'isAvailable' => $this->isAvailable,
            'turnId' => $this->turnId,
        ];
    }
}
