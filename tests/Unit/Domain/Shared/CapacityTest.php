<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use Domain\Shared\Capacity;
use Seedwork\Domain\Exceptions\ValueException;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class CapacityTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testCreateInstance(): void
    {
        $value = $this->faker->numberBetween(0, 100);

        $capacity = new Capacity($value);

        $this->assertSame($value, $capacity->value);
    }

    public function testCreateInstanceFailWhenValueIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        new Capacity($this->faker->numberBetween(-100, -1));
    }
}
