<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class Block
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $idTurn,
        public string $date,
        public bool $block,
        public int $year,
        public int $week,
    ) { }
}
