<?php

namespace Tests\Unit\Infrastructure\Mvc;

class BranchModel
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly bool $isBooleanProperty
    ) {
    }

    public function isBranch(): bool
    {
        return true;
    }

    public function invertBranch(): bool
    {
        return false;
    }
}
