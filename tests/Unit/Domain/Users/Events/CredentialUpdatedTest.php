<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\Events;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Shared\Password;
use App\Domain\Users\Events\CredentialUpdated;

final class CredentialUpdatedTest extends TestCase
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

    public function testNewShouldCreateNewEvent(): void
    {
        $username = $this->faker->email;
        $password = new Password($this->faker->password(Password::MIN_LENGTH));

        $event = CredentialUpdated::new(username: $username, password: $password);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('CredentialUpdated', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($username, $payload['username']);
        $this->assertEquals($password, $payload['password']);
    }

    public function testBuildShouldCreateStoredEvent(): void
    {
        $username = $this->faker->email;
        $password = new Password($this->faker->password(Password::MIN_LENGTH));

        $event = CredentialUpdated::build(
            username: $username,
            password: $password,
            id: $this->faker->uuid
        );

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('CredentialUpdated', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($username, $payload['username']);
        $this->assertEquals($password, $payload['password']);
    }
}
