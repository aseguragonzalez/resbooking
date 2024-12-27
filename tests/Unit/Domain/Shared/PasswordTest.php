<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Shared\Password;

final class PasswordTest extends TestCase
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

    public function testPasswordShouldCreateInstance(): void
    {
        $expected = $this->faker->password(8);

        $password = new Password($expected);

        $this->assertSame($expected, $password->getValue());
    }

    public function testPasswordShouldFailWhenValueInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Password($this->faker->password(7));
    }

    public function testPasswordShouldBeCastedToString(): void
    {
        $expected = $this->faker->password;

        $password = new Password($expected);

        $this->assertSame($expected, (string) $password);
    }

    public function testPasswordShouldTrueWhenComparedWithSameValues(): void
    {
        $password = new Password($this->faker->password);

        $this->assertTrue($password->equals(new Password($password->getValue())));
    }

    public function testPasswordShouldFalseWhenComparedWithDifferentValues(): void
    {
        $password = new Password($this->faker->password);

        $this->assertFalse($password->equals(new Password($this->faker->password)));
    }
}
