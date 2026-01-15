<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain\Services;

use Seedwork\Infrastructure\Migrations\Domain\Entities\Migration;

interface MigrationExecutor
{
    public function execute(Migration $migration): void;
}
