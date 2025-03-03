<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\Events;

use App\Domain\Shared\{Email, Password, Role};
use App\Domain\Users\Entities\User;
use App\Domain\Users\Events\UserCreated;
use App\Domain\Users\ValueObjects\Credential;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class UserCreatedTest extends TestCase
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
        $roles = [Role::User, Role::Admin];
        $password = Password::new();
        $event = UserCreated::new(username: $username, roles: $roles, password: $password);

        $this->assertNotEmpty($event->getId());
        $this->assertSame('UserCreated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($username, $payload['username']);
        $this->assertSame($roles, $payload['roles']);
        $this->assertSame($password, $payload['password']);
    }

    public function testBuildStoredEvent(): void
    {
        $username = $this->user->username->getValue();
        $roles = [Role::User, Role::Admin];
        $password = Password::new();
        $event = UserCreated::build(
            username: $username,
            roles: $roles,
            password: $password,
            id: $this->faker->uuid
        );

        $this->assertNotEmpty($event->getId());
        $this->assertSame('UserCreated', $event->getType());
        $this->assertSame('1.0', $event->getVersion());
        $payload = $event->getPayload();
        $this->assertSame($username, $payload['username']);
        $this->assertSame($roles, $payload['roles']);
        $this->assertSame($password, $payload['password']);
    }
}
