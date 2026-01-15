<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain\Services;

use Seedwork\Infrastructure\Migrations\Domain\Entities\Script;

interface RollbackExecutor
{
    /**
     * @param array<Script> $scripts
     */
    public function rollback(array $scripts): void;
}
