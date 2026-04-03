<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Security\Application\ModifyUserIdentityPassword;

use Framework\Mvc\Security\Application\ModifyUserIdentityPassword\ModifyUserIdentityPasswordCommand;
use Framework\Mvc\Security\Application\ModifyUserIdentityPassword\ModifyUserIdentityPasswordHandler;
use Framework\Mvc\Security\Challenge;
use Framework\Mvc\Security\Domain\Entities\SignInSession;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Framework\Mvc\Security\Domain\Repositories\SignInSessionRepository;
use Framework\Mvc\Security\Domain\Repositories\UserIdentityRepository;
use PHPUnit\Framework\TestCase;

final class ModifyUserIdentityPasswordTest extends TestCase
{
    public function testExecuteUpdatesPasswordWhenCurrentPasswordValid(): void
    {
        $user = UserIdentity::new('user@example.com', ['admin'], 'old')->activate();
        $challenge = $this->createStub(Challenge::class);
        $challenge->method('isExpired')->willReturn(false);
        $session = SignInSession::build($challenge, $user);

        $signInSessionRepository = $this->createStub(SignInSessionRepository::class);
        $signInSessionRepository->method('getByToken')->willReturn($session);

        $userIdentityRepository = $this->createMock(UserIdentityRepository::class);
        $userIdentityRepository->method('getByUsername')->willReturn($user);
        $userIdentityRepository->expects($this->once())->method('save');

        $handler = new ModifyUserIdentityPasswordHandler($signInSessionRepository, $userIdentityRepository);

        $handler->execute(new ModifyUserIdentityPasswordCommand('token', 'old', 'new'));
    }
}
