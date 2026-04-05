<?php

declare(strict_types=1);

namespace Framework\Apps\Migrations\Domain\Services;

use Framework\Apps\Migrations\Domain\Entities\Migration;

interface TestMigrationExecutor
{
    public function execute(Migration $migration): void;
}
