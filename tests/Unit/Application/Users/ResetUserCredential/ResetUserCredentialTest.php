<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Users\ResetUserCredential;

use App\Application\Users\ResetUserCredential\{ResetUserCredential, ResetUserCredentialRequest};
use App\Domain\Shared\{Email, Password};
use App\Domain\Users\Entities\User;
use App\Domain\Users\UserRepository;
use App\Domain\Users\ValueObjects\Credential;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ResetUserCredentialTest extends TestCase
{
    private Faker $faker;
    private MockObject&UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->userRepository = $this->createMock(UserRepository::class);
    }

    protected function tearDown(): void
    {
    }

    public function testAssingNewRandomPassword(): void
    {
        $password = new Password($this->faker->password(Password::MIN_LENGTH));
        $user = User::build(
            username: new Email($this->faker->email),
            credential: Credential::new(password: $password)
        );
        $this->userRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($user);
        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);
        $request = new ResetUserCredentialRequest(username: $user->username->getValue());
        $useCase = new ResetUserCredential(userRepository: $this->userRepository);

        $useCase->execute($request);

        $this->assertFalse($user->getCredential()->check($password));
    }
}
