<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Ports\Dashboard\Models\Accounts\Pages;

use PHPUnit\Framework\TestCase;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use Infrastructure\Ports\Dashboard\Models\Accounts\Pages\ResetPassword;

final class ResetPasswordTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    public function testNewReturnsDefaultInstance(): void
    {
        $model = ResetPassword::new();
        $this->assertFalse($model->hasSucceeded);
        $this->assertEquals((object)[], $model->errors);
        $this->assertEquals([], $model->errorSummary);
        $this->assertSame('{{accounts.reset-password.title}}', $model->pageTitle);
    }

    public function testWithErrorsSetsErrors(): void
    {
        $errors = [
            'username' => $this->faker->sentence,
            'other' => $this->faker->sentence,
        ];
        $model = ResetPassword::withErrors($errors);
        $this->assertFalse($model->hasSucceeded);
        $this->assertEquals((object)$errors, $model->errors);
        $this->assertCount(2, $model->errorSummary);
        $this->assertSame('username', $model->errorSummary[0]->field);
        $this->assertSame($errors['username'], $model->errorSummary[0]->message);
        $this->assertSame('other', $model->errorSummary[1]->field);
        $this->assertSame($errors['other'], $model->errorSummary[1]->message);
    }

    public function testSucceededSetsHasSucceededTrue(): void
    {
        $model = ResetPassword::succeeded();
        $this->assertTrue($model->hasSucceeded);
        $this->assertEquals((object)[], $model->errors);
        $this->assertEquals([], $model->errorSummary);
    }
}
