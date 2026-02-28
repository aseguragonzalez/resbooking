<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\ValueObjects;

use Domain\Restaurants\ValueObjects\RestaurantId;
use PHPUnit\Framework\TestCase;
use SeedWork\Domain\Exceptions\ValueException;

final class RestaurantIdTest extends TestCase
{
    public function testCreateGeneratesUniqueId(): void
    {
        $id1 = RestaurantId::create();
        $id2 = RestaurantId::create();

        $this->assertNotSame($id1->value, $id2->value);
        $this->assertStringStartsWith('id-', $id1->value);
    }

    public function testFromString(): void
    {
        $value = 'restaurant-123';
        $id = RestaurantId::fromString($value);

        $this->assertSame($value, $id->value);
    }

    public function testEquals(): void
    {
        $id1 = RestaurantId::create();
        $id2 = RestaurantId::create();
        $id3 = RestaurantId::fromString($id1->value);

        $this->assertFalse($id1->equals($id2));
        $this->assertTrue($id1->equals($id3));
    }

    public function testToString(): void
    {
        $value = 'restaurant-456';
        $id = RestaurantId::fromString($value);

        $this->assertSame($value, (string) $id);
    }

    public function testFromStringThrowsWhenEmpty(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Restaurant id cannot be empty');

        RestaurantId::fromString('');
    }
}
