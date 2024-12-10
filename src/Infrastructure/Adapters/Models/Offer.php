<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class Offer
{
    public function __construct(
        public int $id,
        public int $projectId,
        public string $title,
        public string $description,
        public string $terms,
        public string $start,
        public string $end,
        public bool $active,
        public ?string $createDate,
        public ?string $updateDate,
        public bool $web,
    ) { }
}
