<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Accounts\Requests;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\ConfirmResetPasswordRequest;

final class ConfirmResetPasswordRequestTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testConstructorSetsProperties(): void
    {
        $token = $this->faker->uuid;
        $password = $this->faker->password(8, 16) . 'A1!';
        $request = new ConfirmResetPasswordRequest($token, $password);
        $this->assertSame($token, $request->token);
        $this->assertSame($password, $request->newPassword);
    }

    public function testValidateReturnsErrorForEmptyToken(): void
    {
        $request = new ConfirmResetPasswordRequest('', $this->faker->password(8, 16) . 'A1!');
        $errors = $request->validate();
        $this->assertArrayHasKey('token', $errors);
        $this->assertSame('{{accounts.reset-password.form.token.error.required}}', $errors['token']);
    }

    public function testValidateReturnsErrorForEmptyNewPassword(): void
    {
        $request = new ConfirmResetPasswordRequest($this->faker->uuid, '');
        $errors = $request->validate();
        $this->assertArrayHasKey('newPassword', $errors);
        $this->assertSame('{{accounts.reset-password.form.new-password.error.required}}', $errors['newPassword']);
    }

    public function testValidateReturnsErrorForWeakNewPassword(): void
    {
        $weakPassword = 'abc12345'; // No special char
        $request = new ConfirmResetPasswordRequest($this->faker->uuid, $weakPassword);
        $errors = $request->validate();
        $this->assertArrayHasKey('newPassword', $errors);
        $this->assertSame('{{accounts.reset-password.form.new-password.error.weak}}', $errors['newPassword']);
    }

    public function testValidateReturnsNoErrorsForValidInput(): void
    {
        $token = $this->faker->uuid;
        $password = 'Abcdef1!';
        $request = new ConfirmResetPasswordRequest($token, $password);
        $errors = $request->validate();
        $this->assertEmpty($errors);
    }
}
