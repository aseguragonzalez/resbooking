<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Settings\Requests;

use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Settings\Requests\UpdateSettingsRequest;
use PHPUnit\Framework\TestCase;

final class UpdateSettingsRequestTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testConstructorSetsProperties(): void
    {
        $email = $this->faker->email;
        $name = $this->faker->company;
        $maxNumberOfDiners = $this->faker->numberBetween(1, 20);
        $minNumberOfDiners = $this->faker->numberBetween(1, $maxNumberOfDiners);
        $numberOfTables = $this->faker->numberBetween(1, 50);
        $phone = $this->faker->phoneNumber;
        $hasReminders = 'on';

        $request = new UpdateSettingsRequest(
            email: $email,
            name: $name,
            maxNumberOfDiners: $maxNumberOfDiners,
            minNumberOfDiners: $minNumberOfDiners,
            numberOfTables: $numberOfTables,
            phone: $phone,
            hasReminders: $hasReminders
        );

        $this->assertSame($email, $request->email);
        $this->assertSame($name, $request->name);
        $this->assertSame($maxNumberOfDiners, $request->maxNumberOfDiners);
        $this->assertSame($minNumberOfDiners, $request->minNumberOfDiners);
        $this->assertSame($numberOfTables, $request->numberOfTables);
        $this->assertSame($phone, $request->phone);
        $this->assertSame($hasReminders, $request->hasReminders);
    }

    public function testConstructorSetsDefaultHasReminders(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $this->assertSame('off', $request->hasReminders);
    }

    public function testHasRemindersCheckedReturnsTrueWhenOn(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber,
            hasReminders: 'on'
        );

        $this->assertTrue($request->hasRemindersChecked());
    }

    public function testHasRemindersCheckedReturnsFalseWhenOff(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber,
            hasReminders: 'off'
        );

        $this->assertFalse($request->hasRemindersChecked());
    }

    public function testValidateReturnsErrorForEmptyEmail(): void
    {
        $request = new UpdateSettingsRequest(
            email: '',
            name: $this->faker->company,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $errors = $request->validate();

        $this->assertArrayHasKey('email', $errors);
        $this->assertSame('{{restaurants.settings.form.email.error.required}}', $errors['email']);
    }

    public function testValidateReturnsErrorForInvalidEmail(): void
    {
        $request = new UpdateSettingsRequest(
            email: 'not-an-email',
            name: $this->faker->company,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $errors = $request->validate();

        $this->assertArrayHasKey('email', $errors);
        $this->assertSame('{{restaurants.settings.form.email.error.invalid}}', $errors['email']);
    }

    public function testValidateReturnsErrorForEmptyName(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: '',
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $errors = $request->validate();

        $this->assertArrayHasKey('name', $errors);
        $this->assertSame('{{restaurants.settings.form.name.error.required}}', $errors['name']);
    }

    public function testValidateReturnsErrorForEmptyPhone(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: 5,
            phone: ''
        );

        $errors = $request->validate();

        $this->assertArrayHasKey('phone', $errors);
        $this->assertSame('{{restaurants.settings.form.phone.error.required}}', $errors['phone']);
    }

    public function testValidateReturnsErrorForZeroMinNumberOfDiners(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 0,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $errors = $request->validate();

        $this->assertArrayHasKey('minNumberOfDiners', $errors);
        $this->assertSame(
            '{{restaurants.settings.form.min-number-of-diners.error.required}}',
            $errors['minNumberOfDiners']
        );
    }

    public function testValidateReturnsErrorForNegativeMinNumberOfDiners(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: 10,
            minNumberOfDiners: -1,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $errors = $request->validate();

        $this->assertArrayHasKey('minNumberOfDiners', $errors);
        $this->assertSame(
            '{{restaurants.settings.form.min-number-of-diners.error.negative}}',
            $errors['minNumberOfDiners']
        );
    }

    public function testValidateReturnsErrorForZeroMaxNumberOfDiners(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: 0,
            minNumberOfDiners: 1,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $errors = $request->validate();

        $this->assertArrayHasKey('maxNumberOfDiners', $errors);
        $this->assertSame(
            '{{restaurants.settings.form.max-number-of-diners.error.required}}',
            $errors['maxNumberOfDiners']
        );
    }

    public function testValidateReturnsErrorForNegativeMaxNumberOfDiners(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: -1,
            minNumberOfDiners: 1,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $errors = $request->validate();

        $this->assertArrayHasKey('maxNumberOfDiners', $errors);
        $this->assertSame(
            '{{restaurants.settings.form.max-number-of-diners.error.negative}}',
            $errors['maxNumberOfDiners']
        );
    }

    public function testValidateReturnsErrorForZeroNumberOfTables(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: 0,
            phone: $this->faker->phoneNumber
        );

        $errors = $request->validate();

        $this->assertArrayHasKey('numberOfTables', $errors);
        $this->assertSame('{{restaurants.settings.form.number-of-tables.error.required}}', $errors['numberOfTables']);
    }

    public function testValidateReturnsErrorForNegativeNumberOfTables(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: -1,
            phone: $this->faker->phoneNumber
        );

        $errors = $request->validate();

        $this->assertArrayHasKey('numberOfTables', $errors);
        $this->assertSame('{{restaurants.settings.form.number-of-tables.error.negative}}', $errors['numberOfTables']);
    }

    public function testValidateReturnsErrorWhenMinNumberOfDinersGreaterThanMax(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: 5,
            minNumberOfDiners: 10,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $errors = $request->validate();

        $this->assertArrayHasKey('minNumberOfDiners', $errors);
        $this->assertSame(
            '{{restaurants.settings.form.min-number-of-diners.error.greater-than-max}}',
            $errors['minNumberOfDiners']
        );
    }

    public function testValidateReturnsNoErrorsForValidInput(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: 10,
            minNumberOfDiners: 1,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $errors = $request->validate();

        $this->assertEmpty($errors);
    }

    public function testValidateDoesNotCheckMinMaxRelationshipWhenMinHasError(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: 5,
            minNumberOfDiners: -1,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $errors = $request->validate();

        $this->assertArrayHasKey('minNumberOfDiners', $errors);
        $this->assertArrayNotHasKey('maxNumberOfDiners', $errors);
        $this->assertCount(1, $errors);
    }

    public function testValidateDoesNotCheckMinMaxRelationshipWhenMaxHasError(): void
    {
        $request = new UpdateSettingsRequest(
            email: $this->faker->email,
            name: $this->faker->company,
            maxNumberOfDiners: -1,
            minNumberOfDiners: 5,
            numberOfTables: 5,
            phone: $this->faker->phoneNumber
        );

        $errors = $request->validate();

        $this->assertArrayHasKey('maxNumberOfDiners', $errors);
        $this->assertArrayNotHasKey('minNumberOfDiners', $errors);
        $this->assertCount(1, $errors);
    }
}
