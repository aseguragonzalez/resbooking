<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain;

use Seedwork\Infrastructure\Migrations\Domain\Script;

interface RollbackExecutor
{
    /**
     * @param array<Script> $scripts
     */
    public function rollback(array $scripts): void;
}
