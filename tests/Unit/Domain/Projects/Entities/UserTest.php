<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Projects\Entities;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Projects\Entities\User;
use App\Domain\Projects\ValueObjects\Credential;
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

        $user = new User($email, $credential);

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
        $user = new User($email, $credential);

        $user->lock();

        $this->assertTrue($user->isLocked());
    }

    public function testUnlockShouldUnlockTheUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = new User($email, $credential, locked: true);

        $user->unlock();

        $this->assertFalse($user->isLocked());
    }

    public function testDisableShouldDisableTheUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = new User($email, $credential);

        $user->disable();

        $this->assertFalse($user->isAvailable());
    }

    public function testEnableShouldEnableTheUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = new User($email, $credential, available: false);

        $user->enable();

        $this->assertTrue($user->isAvailable());
    }

    public function testHasRoleShouldBeTrueWhenUserContainsTheRole(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $role = Role::ADMIN;
        $user = new User($email, $credential, roles: [$role]);

        $this->assertTrue($user->hasRole($role));
    }

    public function testHasRoleShouldBeFalseWhenUserDoesNotContainesTheRole(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = new User($email, $credential);

        $this->assertFalse($user->hasRole(Role::ADMIN));
    }

    public function testGetRolesShouldReturnAllRolesFromUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $roles = [Role::ADMIN, Role::USER];
        $user = new User($email, $credential, roles: $roles);

        $this->assertEquals($roles, $user->getRoles());
    }

    public function testAddRoleShouldSetANewRoleToUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = new User($email, $credential);
        $role = Role::ADMIN;

        $user->addRole($role);

        $this->assertTrue($user->hasRole($role));
    }

    public function testRemoveRoleShouldDeleteRoleFromUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $role = Role::ADMIN;
        $user = new User($email, $credential, roles: [$role]);

        $user->removeRole($role);

        $this->assertFalse($user->hasRole($role));
    }

    public function testChangeCredentialShouldUpdateCredential(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);
        $user = new User($email, $credential);
        $newCredential = Credential::new($this->password, $this->faker->uuid);

        $user->changeCredential($newCredential);

        $this->assertEquals($newCredential, $user->getCredential());
    }

    public function testCreateNewAdminShouldReturnAnAdminUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);

        $user = User::createNewAdmin($email, $credential);

        $this->assertTrue($user->hasRole(Role::ADMIN));
    }

    public function testCreateNewUserShouldReturnAnNewUser(): void
    {
        $email = new Email($this->faker->email);
        $credential = Credential::new($this->password, $this->faker->uuid);

        $user = User::createNewUser($email, $credential);

        $this->assertTrue($user->hasRole(Role::USER));
    }
}
