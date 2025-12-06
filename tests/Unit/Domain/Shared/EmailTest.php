<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use Domain\Shared\Email;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
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
        $expected = $this->faker->email;

        $email = new Email($expected);

        $this->assertSame($expected, $email->value);
    }

    public function testCreateInstanceFailWhenValueInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Email($this->faker->word);
    }

    public function testCastingToString(): void
    {
        $expected = $this->faker->email;

        $email = new Email($expected);

        $this->assertSame($expected, (string) $email);
    }

    public function testEquals(): void
    {
        $email = new Email($this->faker->email);

        $this->assertTrue($email->equals(new Email($email->value)));
    }

    public function testEqualsIsFalseWhenComparedWithDifferentValues(): void
    {
        $email = new Email($this->faker->email);

        $this->assertFalse($email->equals(new Email($this->faker->email)));
    }

    public function testCreateInstanceFailWhenValueIsEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Email('');
    }

    /**
     * @return array<array{string}>
     */
    public static function invalidEmailProvider(): array
    {
        return [
            ['no-at-sign'],
            ['@nodomain.com'],
            ['nodomain@'],
            ['spaces in@email.com'],
            ['invalid@'],
            ['@invalid'],
            ['missing.domain@'],
        ];
    }

    #[DataProvider('invalidEmailProvider')]
    public function testCreateInstanceFailWhenValueIsInvalidFormat(string $invalidEmail): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');

        new Email($invalidEmail);
    }
}
