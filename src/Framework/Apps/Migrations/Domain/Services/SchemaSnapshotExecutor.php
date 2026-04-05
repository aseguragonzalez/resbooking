<?php

declare(strict_types=1);

namespace Framework\Migrations\Domain\Services;

use Framework\Migrations\Domain\ValueObjects\SchemaSnapshot;

interface SchemaSnapshotExecutor
{
    public function capture(): SchemaSnapshot;
}
