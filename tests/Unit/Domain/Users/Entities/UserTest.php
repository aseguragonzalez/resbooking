<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\Entities;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Users\Entities\User;
use App\Domain\Shared\Exceptions\{RoleAlreadyExist, RoleDoesNotExist};
use App\Domain\Users\ValueObjects\Credential;
use App\Domain\Shared\{Email, Password, Role};

final class UserTest extends TestCase
{
    private $faker = null;
    private ?Password $password = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->password = new Password(value: $this->faker->password(Password::MIN_LENGTH));
    }

    protected function tearDown(): void
    {
        $this->faker = null;
    }

    public function testConstructorShouldCreateInstance(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);

        $user = User::build($email, $credential);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($email, $user->username);
        $this->assertEquals($credential, $user->getCredential());
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
    }

    public function testUnlockShouldUnlockTheUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential, locked: true);

        $user->unlock();

        $this->assertFalse($user->isLocked());
    }

    public function testDisableShouldDisableTheUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential);

        $user->disable();

        $this->assertFalse($user->isAvailable());
    }

    public function testEnableShouldEnableTheUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential, available: false);

        $user->enable();

        $this->assertTrue($user->isAvailable());
    }

    public function testHasRoleShouldBeTrueWhenUserContainsTheRole(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $role = Role::ADMIN;
        $user = User::build($email, $credential, roles: [$role]);

        $this->assertTrue($user->hasRole($role));
    }

    public function testHasRoleShouldBeFalseWhenUserDoesNotContainesTheRole(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = User::build($email, $credential);

        $this->assertFalse($user->hasRole(Role::ADMIN));
    }

    public function testGetRolesShouldReturnAllRolesFromUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $roles = [Role::ADMIN, Role::USER];
        $user = User::build($email, $credential, roles: $roles);

        $this->assertEquals($roles, $user->getRoles());
    }

    public function testAddRoleShouldSetANewRoleToUser(): void
    {
        $role = Role::ADMIN;
        $user = User::build(
            username: new Email($this->faker->email),
            credential: Credential::new($this->password)
        );

        $user->addRole($role);

        $this->assertTrue($user->hasRole($role));
    }

    public function testAddRoleShouldFailWhenRoleAlreadyExist(): void
    {
        $role = Role::ADMIN;
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
        $role = Role::ADMIN;
        $user = User::build(
            username: new Email($this->faker->email),
            credential: Credential::new($this->password),
            roles: [$role]
        );

        $user->removeRole($role);

        $this->assertFalse($user->hasRole($role));
    }

    public function testRemoveRoleShouldFailWhenRoleDoesNotExist(): void
    {
        $role = Role::ADMIN;
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
        $newCredential = Credential::new($this->password);

        $user->changeCredential($newCredential);

        $this->assertEquals($newCredential, $user->getCredential());
    }

    public function testCreateNewAdminShouldReturnAnAdminUser(): void
    {
        $user = User::createNewAdmin(username: new Email($this->faker->email), password: $this->password);

        $this->assertTrue($user->hasRole(Role::ADMIN));
    }

    public function testCreateNewUserShouldReturnAnNewUser(): void
    {
        $user = User::createNewUser(username: new Email($this->faker->email), password: $this->password);

        $this->assertTrue($user->hasRole(Role::USER));
    }
}
