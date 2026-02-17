<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain\Fixtures;

use Seedwork\Domain\Entity;
use Seedwork\Domain\EntityId;

final readonly class DummyEntity extends Entity
{
    public function __construct(EntityId $id)
    {
        parent::__construct($id);
    }
}
