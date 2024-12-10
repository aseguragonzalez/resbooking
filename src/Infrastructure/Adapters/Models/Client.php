<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class Client
{
    public function __construct(
        public int $id,
        public int $projectId,
        public string $name,
        public string $email,
        public string $phone,
        public string $createDate,
        public string $updateDate,
        public bool $state,
        public bool $vip,
        public string $comments,
        public bool $advertising,
    ) { }
}
