<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Events;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\Entities\User;
use App\Domain\Projects\ValueObjects\Credential;
use App\Domain\Projects\Events\UserRemoved;
use App\Domain\Shared\{Email, Password};

final class UserRemovedTest extends TestCase
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
        $projectId = $this->faker->uuid;
        $user = User::build(
            username: new Email($this->faker->email),
            credential: Credential::new(
                new Password($this->faker->password(Password::MIN_LENGTH))
            )
        );

        $event = UserRemoved::new(projectId: $projectId, user: $user);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('UserRemoved', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($projectId, $payload['projectId']);
        $this->assertEquals($user, $payload['user']);
    }

    public function testBuildShouldInstanciateStoredEvent(): void
    {
        $projectId = $this->faker->uuid;
        $user = User::build(
            username: new Email($this->faker->email),
            credential: Credential::new(
                new Password($this->faker->password(Password::MIN_LENGTH))
            )
        );

        $event = UserRemoved::build(projectId: $projectId, user: $user, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('UserRemoved', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($projectId, $payload['projectId']);
        $this->assertEquals($user, $payload['user']);
    }
}
