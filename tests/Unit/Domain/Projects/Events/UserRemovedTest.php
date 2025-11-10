<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Events;

use Domain\Projects\Events\UserRemoved;
use Domain\Projects\ValueObjects\User;
use Domain\Shared\Email;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class UserRemovedTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
    }

    protected function tearDown(): void
    {
    }

    public function testCreateNewEvent(): void
    {
        $projectId = $this->faker->uuid;
        $user = new User(username: new Email($this->faker->email));

        $event = UserRemoved::new(projectId: $projectId, user: $user);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('UserRemoved', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($projectId, $payload['projectId']);
        $this->assertSame($user, $payload['user']);
    }

    public function testBuildShouldInstanciateStoredEvent(): void
    {
        $projectId = $this->faker->uuid;
        $user = new User(username: new Email($this->faker->email));

        $event = UserRemoved::build(projectId: $projectId, user: $user, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('UserRemoved', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($projectId, $payload['projectId']);
        $this->assertSame($user, $payload['user']);
    }
}
