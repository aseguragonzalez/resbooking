<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Migrations\Domain\Services;

use Seedwork\Infrastructure\Migrations\Domain\ValueObjects\SchemaSnapshot;

interface SchemaSnapshotExecutor
{
    public function capture(): SchemaSnapshot;
}
