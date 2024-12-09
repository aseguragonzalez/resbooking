<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters\Models;

readonly class ProjectUser
{
    public function __construct(
        public int $id,
        public int $idProject,
        public int $idUser,
    ) { }
}
