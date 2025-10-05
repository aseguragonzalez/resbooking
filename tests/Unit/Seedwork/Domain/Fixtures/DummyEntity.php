<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain\Fixtures;

use Seedwork\Domain\Entity;

class DummyEntity extends Entity
{
    public function __construct(string $id)
    {
        parent::__construct($id);
    }
}
