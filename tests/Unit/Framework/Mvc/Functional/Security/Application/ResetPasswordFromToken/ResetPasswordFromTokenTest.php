<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Security\Application\ResetPasswordFromToken;

use Framework\Mvc\Security\Application\ResetPasswordFromToken\ResetPasswordFromTokenCommand;
use Framework\Mvc\Security\Application\ResetPasswordFromToken\ResetPasswordFromTokenHandler;
use Framework\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Framework\Mvc\Security\Domain\Repositories\ResetPasswordChallengeRepository;
use Framework\Mvc\Security\Domain\Repositories\UserIdentityRepository;
use PHPUnit\Framework\TestCase;

final class ResetPasswordFromTokenTest extends TestCase
{
    public function testExecuteUpdatesPasswordWhenChallengeValid(): void
    {
        $user = UserIdentity::new('user@example.com', ['admin'], 'old')->activate();
        $challenge = ResetPasswordChallenge::build(
            'token',
            (new \DateTimeImmutable())->modify('+1 hour'),
            $user
        );

        $resetPasswordChallengeRepository = $this->createStub(ResetPasswordChallengeRepository::class);
        $resetPasswordChallengeRepository->method('getByToken')->willReturn($challenge);

        $userIdentityRepository = $this->createMock(UserIdentityRepository::class);
        $userIdentityRepository->method('getByUsername')->willReturn($user);
        $userIdentityRepository->expects($this->once())->method('save');

        $handler = new ResetPasswordFromTokenHandler($resetPasswordChallengeRepository, $userIdentityRepository);

        $handler->execute(new ResetPasswordFromTokenCommand('token', 'newpass'));
    }
}
