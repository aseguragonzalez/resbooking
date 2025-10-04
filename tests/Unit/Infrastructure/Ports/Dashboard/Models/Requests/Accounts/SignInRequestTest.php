<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Accounts\Requests;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Accounts\Requests\SignInRequest;

final class SignInRequestTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testConstructorSetsProperties(): void
    {
        $username = $this->faker->email;
        $password = $this->faker->password;
        $rememberMe = 'on';

        $request = new SignInRequest($username, $password, $rememberMe);

        $this->assertSame($username, $request->username);
        $this->assertSame($password, $request->password);
        $this->assertSame($rememberMe, $request->rememberMe);
    }

    public function testKeepMeSignedInReturnsTrueWhenOn(): void
    {
        $request = new SignInRequest($this->faker->email, $this->faker->password, 'on');
        $this->assertTrue($request->keepMeSignedIn());
    }

    public function testKeepMeSignedInReturnsFalseWhenOff(): void
    {
        $request = new SignInRequest($this->faker->email, $this->faker->password, 'off');
        $this->assertFalse($request->keepMeSignedIn());
    }

    public function testKeepMeSignedInReturnsFalseForOtherValues(): void
    {
        $request = new SignInRequest($this->faker->email, $this->faker->password, $this->faker->word);
        $this->assertFalse($request->keepMeSignedIn());
    }

    public function testValidateReturnsErrorForEmptyUsername(): void
    {
        $request = new SignInRequest('', $this->faker->password);
        $errors = $request->validate();
        $this->assertArrayHasKey('username', $errors);
        $this->assertSame('{{accounts.signin.form.username.error.required}}', $errors['username']);
    }

    public function testValidateReturnsErrorForInvalidEmail(): void
    {
        $request = new SignInRequest('not-an-email', $this->faker->password);
        $errors = $request->validate();
        $this->assertArrayHasKey('username', $errors);
        $this->assertSame('{{accounts.signin.form.username.error.invalid_email}}', $errors['username']);
    }

    public function testValidateReturnsErrorForEmptyPassword(): void
    {
        $request = new SignInRequest($this->faker->email, '');
        $errors = $request->validate();
        $this->assertArrayHasKey('password', $errors);
        $this->assertSame('{{accounts.signin.form.password.error.required}}', $errors['password']);
    }

    public function testValidateReturnsMultipleErrorsForEmptyUsernameAndPassword(): void
    {
        $request = new SignInRequest('', '');
        $errors = $request->validate();
        $this->assertArrayHasKey('username', $errors);
        $this->assertArrayHasKey('password', $errors);
    }

    public function testValidateReturnsNoErrorsForValidInput(): void
    {
        $request = new SignInRequest($this->faker->email, $this->faker->password);
        $errors = $request->validate();
        $this->assertEmpty($errors);
    }
}
