<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\Events;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Users\Entities\User;
use App\Domain\Users\Events\UserDisabled;
use App\Domain\Users\ValueObjects\Credential;
use App\Domain\Shared\{Email, Password};

final class UserDisabledTest extends TestCase
{
    private $faker = null;
    private $user = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $credential = Credential::new(
            password: new Password($this->faker->password(Password::MIN_LENGTH))
        );
        $this->user = User::build(
            username: new Email($this->faker->email),
            credential: $credential,
        );
    }

    protected function tearDown(): void
    {
        $this->faker = null;
        $this->user = null;
    }

    public function testNewShouldCreateNewEvent(): void
    {
        $username = $this->user->username->getValue();
        $event = UserDisabled::new(username: $username, user: $this->user);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('UserDisabled', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($username, $payload['username']);
        $this->assertEquals($this->user, $payload['user']);
    }

    public function testBuildShouldCreateStoredEvent(): void
    {
        $username = $this->user->username->getValue();
        $event = UserDisabled::build(username: $username, user: $this->user, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertEquals('UserDisabled', $event->getType());
        $this->assertEquals('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertEquals($username, $payload['username']);
        $this->assertEquals($this->user, $payload['user']);
    }
}
