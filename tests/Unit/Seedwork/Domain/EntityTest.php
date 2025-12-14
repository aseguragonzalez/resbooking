<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain;

use PHPUnit\Framework\TestCase;
use Tests\Unit\Seedwork\Domain\Fixtures\DummyEntity;

class EntityTest extends TestCase
{
    public function testEqualsReturnsTrueForSameId(): void
    {
        $entity1 = new DummyEntity('id-1');
        $entity2 = new DummyEntity('id-1');
        $this->assertTrue($entity1->equals($entity2));
    }

    public function testEqualsReturnsFalseForDifferentId(): void
    {
        $entity1 = new DummyEntity('id-1');
        $entity2 = new DummyEntity('id-2');
        $this->assertFalse($entity1->equals($entity2));
    }

    public function testGetIdReturnsId(): void
    {
        $entity = new DummyEntity('my-id');
        $this->assertSame('my-id', $entity->id);
    }
}
