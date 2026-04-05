<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Module\Security\Application\SignUp;

use Framework\Module\Security\Application\SignUp\SignUpCommand;
use Framework\Module\Security\Application\SignUp\SignUpHandler;
use Framework\Module\Security\Domain\Services\ChallengeNotificator;
use Framework\Module\Security\ChallengesExpirationTime;
use Framework\Module\Security\Domain\Repositories\SignUpChallengeRepository;
use Framework\Module\Security\Domain\Repositories\UserIdentityRepository;
use PHPUnit\Framework\TestCase;

final class SignUpTest extends TestCase
{
    public function testExecuteCreatesUserAndSendsChallengeWhenUserDoesNotExist(): void
    {
        $userIdentityRepository = $this->createStub(UserIdentityRepository::class);
        $userIdentityRepository->method('existsByUsername')->willReturn(false);

        $signUpChallengeRepository = $this->createMock(SignUpChallengeRepository::class);
        $signUpChallengeRepository->expects($this->once())->method('save');

        $notificator = $this->createMock(ChallengeNotificator::class);
        $notificator->expects($this->once())->method('sendSignUpChallenge');

        $handler = new SignUpHandler(
            $userIdentityRepository,
            $signUpChallengeRepository,
            $notificator,
            new ChallengesExpirationTime(10, 5, 20, 15, 30)
        );

        $handler->execute(new SignUpCommand('user@example.com', 'password', ['admin']));
    }
}
