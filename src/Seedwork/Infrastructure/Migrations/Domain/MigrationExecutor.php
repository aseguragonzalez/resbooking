<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain;

use Seedwork\Infrastructure\Migrations\Domain\Migration;

interface MigrationExecutor
{
    public function execute(Migration $migration): void;
}
