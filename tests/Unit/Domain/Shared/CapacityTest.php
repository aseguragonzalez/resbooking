<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Shared\Capacity;
use App\Seedwork\Domain\Exceptions\ValueException;

final class CapacityTest extends TestCase
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

    public function testShouldBeCreated(): void
    {
        $value = $this->faker->numberBetween(0, 100);

        $capacity = new Capacity($value);

        $this->assertSame($value, $capacity->value);
    }

    public function testShouldThrowExceptionForInvalidValue(): void
    {
        $this->expectException(ValueException::class);

        new Capacity($this->faker->numberBetween(-100, -1));
    }
}
