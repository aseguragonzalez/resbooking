<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Entities;

use App\Domain\Projects\Entities\Place;
use App\Domain\Shared\Capacity;
use Seedwork\Domain\Exceptions\ValueException;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class PlaceTest extends TestCase
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

        $place = Place::new(id: $id, capacity: $capacity, name: $name);

        $this->assertInstanceOf(Place::class, $place);
        $this->assertSame($id, $place->getId());
        $this->assertSame($capacity, $place->capacity);
        $this->assertSame($name, $place->name);
    }

    public function testBuildCreatedInstance(): void
    {
        $id = $this->faker->uuid;
        $capacity = new Capacity(100);
        $name = $this->faker->name;

        $place = Place::build(id: $id, capacity: $capacity, name: $name);

        $this->assertInstanceOf(Place::class, $place);
        $this->assertSame($id, $place->getId());
        $this->assertSame($capacity, $place->capacity);
        $this->assertSame($name, $place->name);
    }

    public function testCreateInstanceFailWhenNameIsInvalid(): void
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
