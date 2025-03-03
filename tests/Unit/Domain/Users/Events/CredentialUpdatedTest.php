<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\Events;

use App\Domain\Users\Events\CredentialUpdated;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class CredentialUpdatedTest extends TestCase
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

        $event = CredentialUpdated::new(username: $username);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('CredentialUpdated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($username, $payload['username']);
    }

    public function testBuildShouldCreateStoredEvent(): void
    {
        $username = $this->faker->email;

        $event = CredentialUpdated::build(username: $username, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('CredentialUpdated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($username, $payload['username']);
    }
}
