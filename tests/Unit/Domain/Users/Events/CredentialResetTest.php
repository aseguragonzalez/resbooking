<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\Events;

use App\Domain\Shared\Password;
use App\Domain\Users\Events\CredentialReset;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class CredentialResetTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testNewShouldCreateNewEvent(): void
    {
        $username = $this->faker->email;
        $password = new Password($this->faker->password(Password::MIN_LENGTH));

        $event = CredentialReset::new(username: $username, password: $password);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('CredentialReset', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($username, $payload['username']);
        $this->assertSame($password, $payload['password']);
    }

    public function testBuildShouldCreateStoredEvent(): void
    {
        $username = $this->faker->email;
        $password = new Password($this->faker->password(Password::MIN_LENGTH));

        $event = CredentialReset::build(
            username: $username,
            password: $password,
            id: $this->faker->uuid
        );

        $this->assertNotEmpty($event->getId());
        $this->assertSame('CredentialReset', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($username, $payload['username']);
        $this->assertSame($password, $payload['password']);
    }
}
