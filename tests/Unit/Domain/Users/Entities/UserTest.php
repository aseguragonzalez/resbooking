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

    public function testConstructorShouldCreateInstance(): void
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

    public function testLockShouldLockTheUser(): void
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

    public function testLockShouldFailWhenUserIsAlreadyLocked(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential, locked: true);
        $this->expectException(UserAlreadyLocked::class);

        $user->lock();
    }

    public function testUnlockShouldUnlockTheUser(): void
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

    public function testUnlockShouldFailWhenUserIsAlreadyUnlocked(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential);
        $this->expectException(UserAlreadyUnlocked::class);

        $user->unlock();
    }

    public function testDisableShouldDisableTheUser(): void
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

    public function testDisableShouldFailWhenUserIsAlreadyDisabled(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential, available: false);
        $this->expectException(UserAlreadyDisabled::class);

        $user->disable();
    }

    public function testEnableShouldEnableTheUser(): void
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

    public function testEnableShouldFailWhenUserIsAlreadyEnabled(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential);
        $this->expectException(UserAlreadyEnabled::class);

        $user->enable();
    }

    public function testHasRoleShouldBeTrueWhenUserContainsTheRole(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $role = Role::Admin;
        $user = User::build($email, $credential, roles: [$role]);

        $this->assertTrue($user->hasRole($role));
    }

    public function testHasRoleShouldBeFalseWhenUserDoesNotContainesTheRole(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential);

        $this->assertFalse($user->hasRole(Role::Admin));
    }

    public function testGetRolesShouldReturnAllRolesFromUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $roles = [Role::Admin, Role::User];
        $user = User::build($email, $credential, roles: $roles);

        $this->assertSame($roles, $user->getRoles());
    }

    public function testAddRoleShouldSetANewRoleToUser(): void
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

    public function testAddRoleShouldFailWhenRoleAlreadyExist(): void
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

    public function testRemoveRoleShouldDeleteRoleFromUser(): void
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

    public function testRemoveRoleShouldFailWhenRoleDoesNotExist(): void
    {
        $role = Role::Admin;
        $user = User::build(
            username: new Email($this->faker->email),
            credential: Credential::new($this->password)
        );
        $this->expectException(RoleDoesNotExist::class);

        $user->removeRole($role);
    }

    public function testChangeCredentialShouldUpdateCredential(): void
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

    public function testResetCredentailShouldSetRandomCredential(): void
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
