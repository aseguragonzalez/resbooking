<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\Password;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class PasswordTest extends TestCase
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
        $expected = $this->faker->password(Password::MIN_LENGTH);

        $password = new Password($expected);

        $this->assertSame($expected, $password->getValue());
    }

    public function testCreateInstanceFailWhenValueInvalid(): void
    {
        $invalidPassword = substr($this->faker->password, 0, Password::MIN_LENGTH - 1);
        $this->expectException(\InvalidArgumentException::class);

        new Password(value: $invalidPassword);
    }

    public function testCastingToString(): void
    {
        $expected = $this->faker->password(Password::MIN_LENGTH);

        $password = new Password($expected);

        $this->assertSame($expected, (string) $password);
    }

    public function testEquals(): void
    {
        $password = new Password($this->faker->password(Password::MIN_LENGTH));

        $this->assertTrue($password->equals(new Password($password->getValue())));
    }

    public function testEqualsIsFalseWhenComparedWithDifferentValues(): void
    {
        $password = new Password($this->faker->password(Password::MIN_LENGTH));

        $this->assertFalse(
            $password->equals(new Password($this->faker->password(Password::MIN_LENGTH)))
        );
    }

    public function testCreateNewPassword(): void
    {
        $password = Password::new();

        $this->assertGreaterThanOrEqual(Password::MIN_LENGTH, strlen($password->getValue()));
    }
}
