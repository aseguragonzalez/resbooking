<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Restaurants\Entities;

use Domain\Restaurants\Entities\DiningArea;
use Domain\Shared\Capacity;
use Seedwork\Domain\Exceptions\ValueException;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class DiningAreaTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testCreateNewInstance(): void
    {
        $id = $this->faker->uuid;
        $capacity = new Capacity(100);
        $name = $this->faker->name;

        $diningArea = DiningArea::new(id: $id, capacity: $capacity, name: $name);

        $this->assertInstanceOf(DiningArea::class, $diningArea);
        $this->assertSame($id, $diningArea->getId());
        $this->assertSame($capacity, $diningArea->capacity);
        $this->assertSame($name, $diningArea->name);
    }

    public function testBuildCreatedInstance(): void
    {
        $id = $this->faker->uuid;
        $capacity = new Capacity(100);
        $name = $this->faker->name;

        $diningArea = DiningArea::build(id: $id, capacity: $capacity, name: $name);

        $this->assertInstanceOf(DiningArea::class, $diningArea);
        $this->assertSame($id, $diningArea->getId());
        $this->assertSame($capacity, $diningArea->capacity);
        $this->assertSame($name, $diningArea->name);
    }

    public function testCreateInstanceFailWhenNameIsInvalid(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Name is required');

        DiningArea::new(
            id: $this->faker->uuid,
            capacity: new Capacity($this->faker->numberBetween(1, 100)),
            name: ''
        );
    }
}
