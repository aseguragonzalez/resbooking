<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\Events;

use App\Domain\Shared\{Email, Password};
use App\Domain\Users\Entities\User;
use App\Domain\Users\Events\UserEnabled;
use App\Domain\Users\ValueObjects\Credential;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class UserEnabledTest extends TestCase
{
    private Faker $faker;
    private User $user;

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
    }

    public function testCreateNewEvent(): void
    {
        $username = $this->user->username->getValue();
        $event = UserEnabled::new(username: $username, user: $this->user);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('UserEnabled', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($username, $payload['username']);
        $this->assertSame($this->user, $payload['user']);
    }

    public function testBuildStoredEvent(): void
    {
        $username = $this->user->username->getValue();
        $event = UserEnabled::build(username: $username, user: $this->user, id: $this->faker->uuid);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('UserEnabled', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($username, $payload['username']);
        $this->assertSame($this->user, $payload['user']);
    }
}
