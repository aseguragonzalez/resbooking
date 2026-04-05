<?php

declare(strict_types=1);

namespace Framework\Apps\Migrations\Domain\Services;

use Framework\Apps\Migrations\Domain\ValueObjects\SchemaSnapshot;

interface SchemaSnapshotExecutor
{
    public function capture(): SchemaSnapshot;
}
