<?php

declare(strict_types=1);

namespace Framework\Migrations\Domain\Services;

use Framework\Migrations\Domain\Entities\Script;

interface RollbackExecutor
{
    /**
     * @param array<Script> $scripts
     */
    public function rollback(array $scripts): void;
}
