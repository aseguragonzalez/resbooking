<?php

declare(strict_types=1);

namespace Framework\Mvc\Migrations\Domain\Services;

use Framework\Mvc\Migrations\Domain\Entities\Migration;

interface MigrationExecutor
{
    public function execute(Migration $migration): void;
}
