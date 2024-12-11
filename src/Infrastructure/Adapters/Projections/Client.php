<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Projections;

readonly class Client
{
    public function __construct(
        public int $id,
        public int $projectId,
        public string $name,
        public string $email,
        public string $phone,
        public ?string $createDate,
        public ?string $updateDate,
        public bool $state,
        public bool $vip,
        public string $comments,
        public int $total,
        public int $estado_0,
        public int $estado_1,
        public int $estado_2,
        public int $estado_3,
        public int $estado_4,
        public int $estado_5,
        public int $estado_6,
        public int $estado_7,
        public string $ultimaFecha,
        public bool $advertising,
    ) { }
}
