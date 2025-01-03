<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Entities;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\Entities\Place;
use App\Domain\Shared\Capacity;
use App\Seedwork\Domain\Exceptions\ValueException;

final class PlaceTest extends TestCase
{
    private $faker = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    public function testNewShouldCreateNewInstance(): void
    {
        $id = $this->faker->uuid;
        $capacity = new Capacity(100);
        $name = $this->faker->name;

        $place = Place::new(id: $id, capacity: $capacity, name: $name);

        $this->assertInstanceOf(Place::class, $place);
        $this->assertEquals($id, $place->getId());
        $this->assertEquals($capacity, $place->capacity);
        $this->assertEquals($name, $place->name);
    }

    public function testStoredShouldCreateNewInstance(): void
    {
        $id = $this->faker->uuid;
        $capacity = new Capacity(100);
        $name = $this->faker->name;

        $place = Place::stored(id: $id, capacity: $capacity, name: $name);

        $this->assertInstanceOf(Place::class, $place);
        $this->assertEquals($id, $place->getId());
        $this->assertEquals($capacity, $place->capacity);
        $this->assertEquals($name, $place->name);
    }

    public function testConstructorShouldFailWhenNameIsInvalid(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Name is required');

        Place::new(
            id: $this->faker->uuid,
            capacity: new Capacity($this->faker->numberBetween(1, 100)),
            name: ''
        );
    }
}
