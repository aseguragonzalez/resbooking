<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projects\AddUser;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;

final class AddUserRequestTest extends TestCase
{
    private $faker = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    public function testAddUserShouldFailWhenUsernameIsInvalid(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
