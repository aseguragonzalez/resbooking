<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\ValueObjects;

use Domain\Restaurants\ValueObjects\DiningAreaId;
use PHPUnit\Framework\TestCase;
use SeedWork\Domain\Exceptions\ValueException;

final class DiningAreaIdTest extends TestCase
{
    public function testCreateGeneratesUniqueId(): void
    {
        $id1 = DiningAreaId::create();
        $id2 = DiningAreaId::create();

        $this->assertNotSame($id1->value, $id2->value);
        $this->assertStringStartsWith('id-', $id1->value);
    }

    public function testFromString(): void
    {
        $value = 'dining-area-123';
        $id = DiningAreaId::fromString($value);

        $this->assertSame($value, $id->value);
    }

    public function testEquals(): void
    {
        $id1 = DiningAreaId::create();
        $id2 = DiningAreaId::create();
        $id3 = DiningAreaId::fromString($id1->value);

        $this->assertFalse($id1->equals($id2));
        $this->assertTrue($id1->equals($id3));
    }

    public function testToString(): void
    {
        $value = 'dining-area-456';
        $id = DiningAreaId::fromString($value);

        $this->assertSame($value, (string) $id);
    }

    public function testFromStringThrowsWhenEmpty(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Dining area id cannot be empty');

        DiningAreaId::fromString('');
    }
}
