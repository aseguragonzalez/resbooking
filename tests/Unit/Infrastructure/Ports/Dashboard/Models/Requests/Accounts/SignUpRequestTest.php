<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Accounts\Requests;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\SignUpRequest;

final class SignUpRequestTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testConstructorSetsProperties(): void
    {
        $username = $this->faker->email;
        $password = $this->faker->password(8, 16);
        $passwordConfirm = $password;
        $agree = 'on';

        $request = new SignUpRequest($username, $password, $passwordConfirm, $agree);

        $this->assertSame($username, $request->username);
        $this->assertSame($password, $request->password);
        $this->assertSame($passwordConfirm, $request->passwordConfirm);
        $this->assertSame($agree, $request->agree);
    }

    public function testValidateReturnsErrorForEmptyUsername(): void
    {
        $request = new SignUpRequest('', $this->faker->password(8, 16), $this->faker->password(8, 16), 'on');
        $errors = $request->validate();
        $this->assertArrayHasKey('username', $errors);
        $this->assertSame('{{accounts.signup.form.username.error.required}}', $errors['username']);
    }

    public function testValidateReturnsErrorForInvalidEmail(): void
    {
        $request = new SignUpRequest(
            'not-an-email',
            $this->faker->password(8, 16),
            $this->faker->password(8, 16),
            'on'
        );
        $errors = $request->validate();
        $this->assertArrayHasKey('username', $errors);
        $this->assertSame('{{accounts.signup.form.username.error.email}}', $errors['username']);
    }

    public function testValidateReturnsErrorForEmptyPassword(): void
    {
        $request = new SignUpRequest($this->faker->email, '', '', 'on');
        $errors = $request->validate();
        $this->assertArrayHasKey('password', $errors);
        $this->assertSame('{{accounts.signup.form.password.error.required}}', $errors['password']);
    }

    public function testValidateReturnsErrorForWeakPassword(): void
    {
        $weakPassword = 'abc12345'; // No special char
        $request = new SignUpRequest($this->faker->email, $weakPassword, $weakPassword, 'on');
        $errors = $request->validate();
        $this->assertArrayHasKey('password', $errors);
        $this->assertSame('{{accounts.signup.form.password.error.weak}}', $errors['password']);
    }

    public function testValidateReturnsErrorForPasswordMismatch(): void
    {
        $password = $this->faker->password(8, 16) . '!A1';
        $passwordConfirm = $password . 'x';
        $request = new SignUpRequest($this->faker->email, $password, $passwordConfirm, 'on');
        $errors = $request->validate();
        $this->assertArrayHasKey('passwordConfirm', $errors);
        $this->assertSame('{{accounts.signup.form.password-confirm.error.mismatch}}', $errors['passwordConfirm']);
    }

    public function testValidateReturnsErrorForNotAgree(): void
    {
        $password = 'Abcdef1!';
        $request = new SignUpRequest($this->faker->email, $password, $password, 'off');
        $errors = $request->validate();
        $this->assertArrayHasKey('agree', $errors);
        $this->assertSame('{{accounts.signup.form.agree.error.required}}', $errors['agree']);
    }

    public function testValidateReturnsNoErrorsForValidInput(): void
    {
        $username = $this->faker->email;
        $password = 'Abcdef1!';
        $request = new SignUpRequest($username, $password, $password, 'on');
        $errors = $request->validate();
        $this->assertEmpty($errors);
    }
}
