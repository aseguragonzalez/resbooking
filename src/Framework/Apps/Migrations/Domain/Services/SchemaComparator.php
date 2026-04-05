<?php

declare(strict_types=1);

namespace Framework\Apps\Migrations\Domain\Services;

use Framework\Apps\Migrations\Domain\ValueObjects\SchemaSnapshot;

interface SchemaComparator
{
    public function compare(SchemaSnapshot $initial, SchemaSnapshot $final): SchemaComparisonResult;
}
