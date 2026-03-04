<?php

declare(strict_types=1);

namespace Framework\Mvc\Migrations\Domain\Services;

use Framework\Mvc\Migrations\Domain\ValueObjects\SchemaSnapshot;

interface SchemaComparator
{
    public function compare(SchemaSnapshot $initial, SchemaSnapshot $final): SchemaComparisonResult;
}
