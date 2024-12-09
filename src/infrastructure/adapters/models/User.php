<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class User {

    public function __construct(
        public int $id,
        public string $username,
        public string $password,
        public bool $active,
    ) { }

}
