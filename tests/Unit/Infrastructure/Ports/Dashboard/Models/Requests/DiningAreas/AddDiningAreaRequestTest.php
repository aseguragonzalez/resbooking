<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests;

use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\DiningAreas\Requests\AddDiningAreaRequest;
use PHPUnit\Framework\TestCase;

final class AddDiningAreaRequestTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testConstructorSetsProperties(): void
    {
        $name = $this->faker->name;
        $capacity = $this->faker->numberBetween(1, 50);

        $request = new AddDiningAreaRequest(name: $name, capacity: $capacity);

        $this->assertSame($name, $request->name);
        $this->assertSame($capacity, $request->capacity);
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $request = new AddDiningAreaRequest();

        $this->assertSame('', $request->name);
        $this->assertSame(1, $request->capacity);
    }

    public function testValidateReturnsErrorForEmptyName(): void
    {
        $request = new AddDiningAreaRequest(name: '', capacity: 5);
        $errors = $request->validate();

        $this->assertArrayHasKey('name', $errors);
        $this->assertSame('{{dining-areas.form.name.error.required}}', $errors['name']);
    }

    public function testValidateReturnsErrorForZeroCapacity(): void
    {
        $request = new AddDiningAreaRequest(name: $this->faker->name, capacity: 0);
        $errors = $request->validate();

        $this->assertArrayHasKey('capacity', $errors);
        $this->assertSame('{{dining-areas.form.capacity.error.min}}', $errors['capacity']);
    }

    public function testValidateReturnsErrorForNegativeCapacity(): void
    {
        $request = new AddDiningAreaRequest(name: $this->faker->name, capacity: -1);
        $errors = $request->validate();

        $this->assertArrayHasKey('capacity', $errors);
        $this->assertSame('{{dining-areas.form.capacity.error.min}}', $errors['capacity']);
    }

    public function testValidateReturnsMultipleErrorsForEmptyNameAndZeroCapacity(): void
    {
        $request = new AddDiningAreaRequest(name: '', capacity: 0);
        $errors = $request->validate();

        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('capacity', $errors);
    }

    public function testValidateReturnsNoErrorsForValidInput(): void
    {
        $request = new AddDiningAreaRequest(
            name: $this->faker->name,
            capacity: $this->faker->numberBetween(1, 50)
        );
        $errors = $request->validate();

        $this->assertEmpty($errors);
    }
}
