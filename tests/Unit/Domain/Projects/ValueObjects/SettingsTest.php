<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\ValueObjects;

use App\Domain\Projects\ValueObjects\Settings;
use App\Domain\Shared\{Capacity, Email, Phone};
use Seedwork\Domain\Exceptions\ValueException;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class SettingsTest extends TestCase
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
        $maxNumberOfDiners = new Capacity(100);
        $minNumberOfDiners = new Capacity(1);
        $numberOfTables = new Capacity(25);
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
            numberOfTables: $numberOfTables,
            phone: $phone
        );

        $this->assertInstanceOf(Settings::class, $settings);
        $this->assertSame($maxNumberOfDiners, $settings->maxNumberOfDiners);
        $this->assertSame($minNumberOfDiners, $settings->minNumberOfDiners);
        $this->assertSame($hasRemainders, $settings->hasRemainders);
        $this->assertSame($name, $settings->name);
        $this->assertSame($email, $settings->email);
        $this->assertSame($phone, $settings->phone);
        $this->assertSame($numberOfTables, $settings->numberOfTables);
    }

    public function testCreateInstanceFailWhenNameIsEmpty(): void
    {
        $this->expectException(ValueException::class);
        $this->expectExceptionMessage('Name is required');

        new Settings(
            email: new Email($this->faker->email),
            hasRemainders: $this->faker->boolean,
            name: '',
            maxNumberOfDiners: new Capacity(100),
            minNumberOfDiners: new Capacity(1),
            numberOfTables: new Capacity(25),
            phone: new Phone($this->faker->phoneNumber)
        );
    }

    public function testCreateInstanceFailWhenMinMaxAreInvalid(): void
    {
        $this->expectException(ValueException::class);

        new Settings(
            email: new Email($this->faker->email),
            hasRemainders: $this->faker->boolean,
            name: $this->faker->name,
            maxNumberOfDiners: new Capacity(1),
            minNumberOfDiners: new Capacity(2),
            numberOfTables: new Capacity($this->faker->numberBetween(1, 100)),
            phone: new Phone($this->faker->phoneNumber)
        );
    }
}
