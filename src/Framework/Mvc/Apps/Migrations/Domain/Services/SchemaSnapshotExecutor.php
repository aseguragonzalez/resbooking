<?php

declare(strict_types=1);

namespace Framework\Mvc\Migrations\Domain\Services;

use Framework\Mvc\Migrations\Domain\ValueObjects\SchemaSnapshot;

interface SchemaSnapshotExecutor
{
    public function capture(): SchemaSnapshot;
}
