<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class Service {

    public function __construct(
        public int $id,
        public string $name,
        public string $path,
        public string $platform,
        public string $description,
        public bool $active,
    ) { }

}
