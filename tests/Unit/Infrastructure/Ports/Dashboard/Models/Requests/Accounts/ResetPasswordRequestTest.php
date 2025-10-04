<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Accounts\Requests;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\ResetPasswordRequest;

final class ResetPasswordRequestTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testConstructorSetsUsername(): void
    {
        $username = $this->faker->email;
        $request = new ResetPasswordRequest($username);
        $this->assertSame($username, $request->username);
    }

    public function testValidateReturnsErrorForEmptyUsername(): void
    {
        $request = new ResetPasswordRequest('');
        $errors = $request->validate();
        $this->assertArrayHasKey('username', $errors);
        $this->assertSame('{{accounts.reset-password.form.username.error.required}}', $errors['username']);
    }

    public function testValidateReturnsErrorForInvalidEmail(): void
    {
        $request = new ResetPasswordRequest('not-an-email');
        $errors = $request->validate();
        $this->assertArrayHasKey('username', $errors);
        $this->assertSame('{{accounts.reset-password.form.username.error.invalid}}', $errors['username']);
    }

    public function testValidateReturnsNoErrorsForValidEmail(): void
    {
        $request = new ResetPasswordRequest($this->faker->email);
        $errors = $request->validate();
        $this->assertEmpty($errors);
    }
}
