<?php

declare(strict_types=1);

namespace Framework\Mvc\Migrations\Domain\Services;

use Framework\Mvc\Migrations\Domain\Entities\Script;

interface RollbackExecutor
{
    /**
     * @param array<Script> $scripts
     */
    public function rollback(array $scripts): void;
}
