<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Domain;

use PHPUnit\Framework\TestCase;
use Seedwork\Domain\EntityId;

final class EntityIdTest extends TestCase
{
    public function testFromStringReturnsInstanceWithCorrectValue(): void
    {
        $value = 'my-id-123';
        $id = EntityId::fromString($value);

        $this->assertSame($value, $id->value);
    }

    public function testNewReturnsInstanceWithNonEmptyValue(): void
    {
        $id = EntityId::new();

        $this->assertNotEmpty($id->value);
    }

    public function testEqualsReturnsTrueForSameValue(): void
    {
        $value = 'same-id';
        $id1 = EntityId::fromString($value);
        $id2 = EntityId::fromString($value);

        $this->assertTrue($id1->equals($id2));
    }

    public function testEqualsReturnsFalseForDifferentValues(): void
    {
        $id1 = EntityId::fromString('id-1');
        $id2 = EntityId::fromString('id-2');

        $this->assertFalse($id1->equals($id2));
    }

    public function testToStringReturnsStoredValue(): void
    {
        $value = 'string-value';
        $id = EntityId::fromString($value);

        $this->assertSame($value, (string) $id);
    }

    public function testTwoInstancesFromSameStringAreEqual(): void
    {
        $value = 'identical';
        $id1 = EntityId::fromString($value);
        $id2 = EntityId::fromString($value);

        $this->assertTrue($id1->equals($id2));
        $this->assertSame($id1->value, $id2->value);
    }
}
