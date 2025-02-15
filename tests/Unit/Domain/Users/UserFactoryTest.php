<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users;

use App\Domain\Shared\{Email, Password, Role};
use App\Domain\Users\UserFactory;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\TestCase;

final class UserFactoryTest extends TestCase
{
    private Faker $faker;
    private Email $email;
    private Password $password;
    private UserFactory $userFactory;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->email = new Email(value: $this->faker->email);
        $this->password = new Password(value: $this->faker->password(Password::MIN_LENGTH));
        $this->userFactory = new UserFactory();
    }

    protected function tearDown(): void
    {
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
