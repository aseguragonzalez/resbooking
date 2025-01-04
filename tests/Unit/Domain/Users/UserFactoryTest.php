<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use App\Domain\Users\Entities\User;
use App\Domain\Users\UserFactory;
use App\Domain\Shared\{Email, Password, Role};

final class UserFactoryTest extends TestCase
{
    private $faker = null;
    private ?Email $email = null;
    private ?Password $password = null;
    private ?UserFactory $userFactory = null;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->email = new Email(value: $this->faker->email);
        $this->password = new Password(value: $this->faker->password(Password::MIN_LENGTH));
        $this->userFactory = new UserFactory();
    }

    protected function tearDown(): void
    {
        $this->faker = null;
        $this->email = null;
        $this->password = null;
        $this->userFactory = null;
    }

    public function testCreateNewAdminShouldReturnAnAdminUser(): void
    {
        $user = $this->userFactory->createNewAdmin(username: $this->email, password: $this->password);

        $this->assertTrue($user->hasRole(Role::ADMIN));
    }

    public function testCreateNewUserShouldReturnAnNewUser(): void
    {
        $user = $this->userFactory->createNewUser(username: $this->email, password: $this->password);

        $this->assertTrue($user->hasRole(Role::USER));
    }
}
