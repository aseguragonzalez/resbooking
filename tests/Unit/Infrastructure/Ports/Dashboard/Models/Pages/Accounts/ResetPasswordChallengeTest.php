<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Accounts\Pages;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\ResetPasswordChallenge;

final class ResetPasswordChallengeTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testNewReturnsDefaultInstance(): void
    {
        $token = $this->faker->uuid;
        $model = ResetPasswordChallenge::new($token);
        $this->assertSame($token, $model->token);
        $this->assertEquals((object)[], $model->errors);
        $this->assertEquals([], $model->errorSummary);
        $this->assertSame('{{accounts.reset-password.title}}', $model->pageTitle);
    }

    public function testWithErrorsSetsErrors(): void
    {
        $token = $this->faker->uuid;
        $errors = [
            'newPassword' => $this->faker->sentence,
            'other' => $this->faker->sentence,
        ];
        $model = ResetPasswordChallenge::withErrors($errors, $token);
        $this->assertSame($token, $model->token);
        $this->assertEquals((object)$errors, $model->errors);
        $this->assertCount(2, $model->errorSummary);
        $this->assertSame('newPassword', $model->errorSummary[0]->field);
        $this->assertSame($errors['newPassword'], $model->errorSummary[0]->message);
        $this->assertSame('other', $model->errorSummary[1]->field);
        $this->assertSame($errors['other'], $model->errorSummary[1]->message);
    }
}
