<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\ValueObjects;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\ValueObjects\Settings;
use App\Seedwork\Domain\Exceptions\ValueException;
use App\Domain\Shared\{Capacity, Email, Phone};

final class SettingsTest extends TestCase
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

    public function testConstructorShouldCreateInstance(): void
    {
        $maxNumberOfDiners = new Capacity($this->faker->numberBetween(1, 100));
        $minNumberOfDiners = new Capacity($this->faker->numberBetween(1, 100));
        $hasRemainders = $this->faker->boolean;
        $name = $this->faker->name;
        $email = new Email($this->faker->email);
        $phone = new Phone($this->faker->phoneNumber);

        $settings = new Settings(
            email: $email,
            hasRemainders: $hasRemainders,
            name: $name,
            maxNumberOfDiners: $maxNumberOfDiners,
            minNumberOfDiners: $minNumberOfDiners,
            phone: $phone
        );

        $this->assertInstanceOf(Settings::class, $settings);
        $this->assertEquals($maxNumberOfDiners, $settings->maxNumberOfDiners);
        $this->assertEquals($minNumberOfDiners, $settings->minNumberOfDiners);
        $this->assertEquals($hasRemainders, $settings->hasRemainders);
        $this->assertEquals($name, $settings->name);
        $this->assertEquals($email, $settings->email);
        $this->assertEquals($phone, $settings->phone);
    }

    public function testConstructorShouldFailWhenNameIsEmpty(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Name is required');

        new Settings(
            email: new Email($this->faker->email),
            hasRemainders: $this->faker->boolean,
            name: '',
            maxNumberOfDiners: new Capacity($this->faker->numberBetween(1, 100)),
            minNumberOfDiners: new Capacity($this->faker->numberBetween(1, 100)),
            phone: new Phone($this->faker->phoneNumber)
        );
    }
}
