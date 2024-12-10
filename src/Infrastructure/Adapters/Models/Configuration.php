<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class Configuration
{
    public function __construct(
        public int $id,
        public int $projectId,
        public int $idTurn,
        public int $day,
        public int $count,
    ) { }
}
