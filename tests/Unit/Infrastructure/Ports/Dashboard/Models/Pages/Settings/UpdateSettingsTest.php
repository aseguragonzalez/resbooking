<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Settings\Pages;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Settings\Pages\UpdateSettings;
use Infrastructure\Ports\Dashboard\Models\Settings\Requests\UpdateSettingsRequest;

final class UpdateSettingsTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testNewCreatesDefaultInstance(): void
    {
        $email = $this->faker->email;
        $name = $this->faker->company;
        $maxNumberOfDiners = $this->faker->numberBetween(1, 20);
        $minNumberOfDiners = $this->faker->numberBetween(1, $maxNumberOfDiners);
        $numberOfTables = $this->faker->numberBetween(1, 50);
        $phone = $this->faker->phoneNumber;

        $page = UpdateSettings::new(
            email: $email,
            hasReminders: true,
            name: $name,
            maxNumberOfDiners: $maxNumberOfDiners,
            minNumberOfDiners: $minNumberOfDiners,
            numberOfTables: $numberOfTables,
            phone: $phone
        );

        $this->assertSame('{{restaurants.settings.form.title}}', $page->pageTitle);
        $this->assertInstanceOf(UpdateSettingsRequest::class, $page->settings);
        $this->assertSame($email, $page->settings->email);
        $this->assertSame($name, $page->settings->name);
        $this->assertSame($maxNumberOfDiners, $page->settings->maxNumberOfDiners);
        $this->assertSame($minNumberOfDiners, $page->settings->minNumberOfDiners);
        $this->assertSame($numberOfTables, $page->settings->numberOfTables);
        $this->assertSame($phone, $page->settings->phone);
        $this->assertSame('on', $page->settings->hasReminders);
        $this->assertEquals((object)[], $page->errors);
        $this->assertEquals([], $page->errorSummary);
    }

    public function testNewWithHasRemindersFalse(): void
    {
        $page = UpdateSettings::new(
            email: $this->faker->email,
            hasReminders: false,
            name: $this->faker->company,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $this->assertSame('off', $page->settings->hasReminders);
    }

    public function testWithErrorsSetsErrors(): void
    {
        $errors = [
            'email' => $this->faker->sentence,
            'name' => $this->faker->sentence,
        ];
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $page = UpdateSettings::withErrors($request, $errors);

        $this->assertSame('{{restaurants.settings.form.title}}', $page->pageTitle);
        $this->assertSame($request, $page->settings);
        $this->assertEquals((object)$errors, $page->errors);
        $this->assertCount(2, $page->errorSummary);
        $this->assertSame('email', $page->errorSummary[0]->field);
        $this->assertSame('name', $page->errorSummary[1]->field);
    }

    public function testWithErrorsPreservesRequestData(): void
    {
        $request = new UpdateSettingsRequest(
            email: 'test@example.com',
            name: 'Test Restaurant',
            maxNumberOfDiners: 15,
            minNumberOfDiners: 2,
            numberOfTables: 8,
            phone: '1234567890',
            hasReminders: 'on'
        );
        $errors = ['email' => 'Invalid email'];

        $page = UpdateSettings::withErrors($request, $errors);

        $this->assertSame($request, $page->settings);
        $this->assertSame('test@example.com', $page->settings->email);
        $this->assertSame('Test Restaurant', $page->settings->name);
        $this->assertSame(15, $page->settings->maxNumberOfDiners);
        $this->assertSame(2, $page->settings->minNumberOfDiners);
        $this->assertSame(8, $page->settings->numberOfTables);
        $this->assertSame('1234567890', $page->settings->phone);
        $this->assertSame('on', $page->settings->hasReminders);
    }
}
