<?php

declare(strict_types=1);

namespace Framework\Apps\Migrations\Domain\Services;

use Framework\Apps\Migrations\Domain\Entities\Migration;

interface MigrationExecutor
{
    public function execute(Migration $migration): void;
}
