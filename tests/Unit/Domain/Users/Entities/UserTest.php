<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\Entities;

use App\Domain\Shared\{Email, Password, Role};
use App\Domain\Users\Entities\User;
use App\Domain\Users\Events\{
    UserLocked,
    UserUnlocked,
    UserDisabled,
    UserEnabled,
    RoleAddedToUser,
    RoleRemovedFromUser,
    CredentialReset,
    CredentialUpdated
};
use App\Domain\Users\Exceptions\{
    RoleAlreadyExist,
    RoleDoesNotExist,
    UserAlreadyEnabled,
    UserAlreadyDisabled,
    UserAlreadyLocked,
    UserAlreadyUnlocked
};
use App\Domain\Users\ValueObjects\Credential;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    private Faker $faker;
    private Password $password;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->password = new Password(value: $this->faker->password(Password::MIN_LENGTH));
    }

    protected function tearDown(): void
    {
    }

    public function testCreateInstance(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);

        $user = User::build($email, $credential);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($email, $user->username);
        $this->assertSame($credential, $user->getCredential());
        $this->assertFalse($user->isLocked());
        $this->assertTrue($user->isAvailable());
        $this->assertEmpty($user->getRoles());
    }

    public function testLockUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential);

        $user->lock();

        $this->assertTrue($user->isLocked());
        $events = $user->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(UserLocked::class, $events[0]);
        $event = $events[0];
        $this->assertSame($user, $event->getPayload()['user']);
        $this->assertSame($user->username->getValue(), $event->getPayload()['username']);
    }

    public function testLockFailWhenUserIsAlreadyLocked(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential, locked: true);
        $this->expectException(UserAlreadyLocked::class);

        $user->lock();
    }

    public function testUnlockUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential, locked: true);

        $user->unlock();

        $this->assertFalse($user->isLocked());
        $events = $user->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(UserUnlocked::class, $events[0]);
        $event = $events[0];
        $this->assertSame($user, $event->getPayload()['user']);
        $this->assertSame($user->username->getValue(), $event->getPayload()['username']);
    }

    public function testUnlockFailWhenUserIsAlreadyUnlocked(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential);
        $this->expectException(UserAlreadyUnlocked::class);

        $user->unlock();
    }

    public function testDisableUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential);

        $user->disable();

        $this->assertFalse($user->isAvailable());
        $events = $user->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(UserDisabled::class, $events[0]);
        $event = $events[0];
        $this->assertSame($user, $event->getPayload()['user']);
        $this->assertSame($user->username->getValue(), $event->getPayload()['username']);
    }

    public function testDisableFailWhenUserIsAlreadyDisabled(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential, available: false);
        $this->expectException(UserAlreadyDisabled::class);

        $user->disable();
    }

    public function testEnableUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential, available: false);

        $user->enable();

        $this->assertTrue($user->isAvailable());
        $events = $user->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(UserEnabled::class, $events[0]);
        $event = $events[0];
        $this->assertSame($user, $event->getPayload()['user']);
        $this->assertSame($user->username->getValue(), $event->getPayload()['username']);
    }

    public function testEnableFailWhenUserIsAlreadyEnabled(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential);
        $this->expectException(UserAlreadyEnabled::class);

        $user->enable();
    }

    public function testHasRole(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $role = Role::Admin;
        $user = User::build($email, $credential, roles: [$role]);

        $this->assertTrue($user->hasRole($role));
    }

    public function testHasRoleIsFalseWhenUserDoesNotContainsTheRole(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential);

        $this->assertFalse($user->hasRole(Role::Admin));
    }

    public function testGetRoles(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $roles = [Role::Admin, Role::User];
        $user = User::build($email, $credential, roles: $roles);

        $this->assertSame($roles, $user->getRoles());
    }

    public function testAddRoleToUser(): void
    {
        $role = Role::Admin;
        $user = User::build(
            username: new Email($this->faker->email),
            credential: Credential::new($this->password)
        );

        $user->addRole($role);

        $this->assertTrue($user->hasRole($role));
        $events = $user->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(RoleAddedToUser::class, $events[0]);
        $event = $events[0];
        $this->assertSame($role, $event->getPayload()['role']);
        $this->assertSame($user->username->getValue(), $event->getPayload()['username']);
    }

    public function testAddRoleFailWhenRoleAlreadyExist(): void
    {
        $role = Role::Admin;
        $user = User::build(
            username: new Email($this->faker->email),
            credential: Credential::new($this->password),
            roles: [$role]
        );
        $this->expectException(RoleAlreadyExist::class);

        $user->addRole($role);
    }

    public function testRemoveRoleFromUser(): void
    {
        $role = Role::Admin;
        $user = User::build(
            username: new Email($this->faker->email),
            credential: Credential::new($this->password),
            roles: [$role]
        );

        $user->removeRole($role);

        $this->assertFalse($user->hasRole($role));
        $events = $user->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(RoleRemovedFromUser::class, $events[0]);
        $event = $events[0];
        $this->assertSame($role, $event->getPayload()['role']);
        $this->assertSame($user->username->getValue(), $event->getPayload()['username']);
    }

    public function testRemoveRoleFailWhenRoleDoesNotExist(): void
    {
        $role = Role::Admin;
        $user = User::build(
            username: new Email($this->faker->email),
            credential: Credential::new($this->password)
        );
        $this->expectException(RoleDoesNotExist::class);

        $user->removeRole($role);
    }

    public function testChangeCredential(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password);
        $user = User::build($email, $credential);
        $currentCredential = $user->getCredential();

        $user->changeCredential(password: $this->password);

        $this->assertNotEquals($currentCredential, $user->getCredential());
        $events = $user->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(CredentialUpdated::class, $events[0]);
        $event = $events[0];
        $this->assertSame($user->username->getValue(), $event->getPayload()['username']);
    }

    public function testResetCredentail(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password);
        $user = User::build($email, $credential);
        $currentCredential = $user->getCredential();

        $user->resetCredential();

        $this->assertNotEquals($currentCredential, $user->getCredential());
        $events = $user->getEvents();
        $this->assertSame(1, count($events));
        $this->assertInstanceOf(CredentialReset::class, $events[0]);
        $event = $events[0];
        $this->assertNotEquals($this->password, $event->getPayload()['password']);
        $this->assertSame($user->username->getValue(), $event->getPayload()['username']);
    }
}
