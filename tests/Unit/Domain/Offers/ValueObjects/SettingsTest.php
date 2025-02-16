<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Offers\ValueObjects;

use App\Domain\Offers\Exceptions\InvalidDateRange;
use App\Domain\Offers\ValueObjects\Settings;
use App\Domain\Shared\{Capacity, DayOfWeek, Turn};
use App\Seedwork\Domain\Exceptions\ValueException;
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

    public function testConstructorShouldCreateInstance(): void
    {
        $description = $this->faker->text;
        $title = $this->faker->sentence;
        $termsAndConditions = $this->faker->text;
        $startDate = new \DateTimeImmutable();
        $endDate = $startDate->add(new \DateInterval('P10D'));
        $settings = new Settings(
            description: $description,
            title: $title,
            termsAndConditions: $termsAndConditions,
            startDate: $startDate,
            endDate: $endDate,
        );

        $this->assertInstanceOf(Settings::class, $settings);
        $this->assertEquals($description, $settings->description);
        $this->assertEquals($title, $settings->title);
        $this->assertEquals($termsAndConditions, $settings->termsAndConditions);
        $this->assertEquals($startDate, $settings->startDate);
        $this->assertEquals($endDate, $settings->endDate);
    }

    public function testConstructorShouldFailWhenTitleIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        new Settings(
            description: $this->faker->text,
            title: '',
            termsAndConditions: $this->faker->text,
            startDate: new \DateTimeImmutable(),
            endDate: new \DateTimeImmutable(),
        );
    }

    public function testConstructorShouldFailWhenDescriptionIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        new Settings(
            description: '',
            title: $this->faker->sentence,
            termsAndConditions: $this->faker->text,
            startDate: new \DateTimeImmutable(),
            endDate: new \DateTimeImmutable(),
        );
    }

    public function testConstructorShouldFailWhenTermsAndConditionsIsInvalid(): void
    {
        $this->expectException(ValueException::class);

        new Settings(
            description: $this->faker->text,
            title: $this->faker->sentence,
            termsAndConditions: '',
            startDate: new \DateTimeImmutable(),
            endDate: new \DateTimeImmutable(),
        );
    }

    public function testConstructorShouldFailWhenDateRangeIsInvalid(): void
    {
        $startDate = new \DateTimeImmutable();
        $endDate = $startDate->sub(new \DateInterval('P1D'));
        $this->expectException(InvalidDateRange::class);

        new Settings(
            description: $this->faker->text,
            title: $this->faker->sentence,
            termsAndConditions: $this->faker->text,
            startDate: $startDate,
            endDate: $endDate,
        );
    }
}
