<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Security\Application\RequestResetPassword;

use Framework\Mvc\Security\Application\RequestResetPassword\RequestResetPasswordCommand;
use Framework\Mvc\Security\Application\RequestResetPassword\RequestResetPasswordHandler;
use Framework\Mvc\Security\Domain\Services\ChallengeNotificator;
use Framework\Mvc\Security\ChallengesExpirationTime;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Framework\Mvc\Security\Domain\Repositories\ResetPasswordChallengeRepository;
use Framework\Mvc\Security\Domain\Repositories\UserIdentityRepository;
use PHPUnit\Framework\TestCase;

final class RequestResetPasswordTest extends TestCase
{
    public function testExecuteCreatesChallengeAndNotifiesWhenUserExists(): void
    {
        $user = UserIdentity::new('user@example.com', ['admin'], 'pass');
        $userIdentityRepository = $this->createStub(UserIdentityRepository::class);
        $userIdentityRepository->method('getByUsername')->willReturn($user);

        $resetPasswordChallengeRepository = $this->createMock(ResetPasswordChallengeRepository::class);
        $resetPasswordChallengeRepository->expects($this->once())->method('save');

        $notificator = $this->createMock(ChallengeNotificator::class);
        $notificator->expects($this->once())->method('sendResetPasswordChallenge');

        $handler = new RequestResetPasswordHandler(
            $userIdentityRepository,
            $resetPasswordChallengeRepository,
            $notificator,
            new ChallengesExpirationTime(10, 5, 20, 15, 30)
        );

        $handler->execute(new RequestResetPasswordCommand('user@example.com'));
    }
}
