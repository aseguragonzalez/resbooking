<?php

declare(strict_types=1);

namespace Framework\Migrations\Domain\Services;

use Framework\Migrations\Domain\Entities\Migration;

interface MigrationExecutor
{
    public function execute(Migration $migration): void;
}
