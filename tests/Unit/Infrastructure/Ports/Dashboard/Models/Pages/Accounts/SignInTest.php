<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Accounts\Pages;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\SignIn;

final class SignInTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testNewReturnsDefaultInstance(): void
    {
        $model = SignIn::new();
        $this->assertEquals((object)[], $model->errors);
        $this->assertEquals([], $model->errorSummary);
        $this->assertSame('{{accounts.signin.title}}', $model->pageTitle);
    }

    public function testWithErrorsSetsErrors(): void
    {
        $errors = [
            'username' => $this->faker->sentence,
            'password' => $this->faker->sentence,
        ];
        $model = SignIn::withErrors($errors);
        $this->assertEquals((object)$errors, $model->errors);
        $this->assertCount(2, $model->errorSummary);
        $this->assertSame('username', $model->errorSummary[0]->field);
        $this->assertSame($errors['username'], $model->errorSummary[0]->message);
        $this->assertSame('password', $model->errorSummary[1]->field);
        $this->assertSame($errors['password'], $model->errorSummary[1]->message);
    }
}
