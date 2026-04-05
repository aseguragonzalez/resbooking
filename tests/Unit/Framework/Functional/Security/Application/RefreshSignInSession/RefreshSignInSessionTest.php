<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Module\Security\Application\RefreshSignInSession;

use Framework\Module\Security\Application\RefreshSignInSession\RefreshSignInSessionCommand;
use Framework\Module\Security\Application\RefreshSignInSession\RefreshSignInSessionHandler;
use Framework\Module\Security\Challenge;
use Framework\Module\Security\ChallengesExpirationTime;
use Framework\Module\Security\Domain\Entities\SignInSession;
use Framework\Module\Security\Domain\Entities\UserIdentity;
use Framework\Module\Security\Domain\Repositories\SignInSessionRepository;
use PHPUnit\Framework\TestCase;

final class RefreshSignInSessionTest extends TestCase
{
    public function testExecuteRefreshesSessionAndReturnsChallenge(): void
    {
        $user = UserIdentity::new('user@example.com', ['admin'], 'pass');
        $challenge = $this->createStub(Challenge::class);
        $challenge->method('isExpired')->willReturn(false);
        $challenge->method('refreshUntil')->willReturn($challenge);
        $session = SignInSession::build($challenge, $user);

        $signInSessionRepository = $this->createMock(SignInSessionRepository::class);
        $signInSessionRepository->method('getByToken')->willReturn($session);
        $signInSessionRepository->expects($this->once())->method('save');

        $handler = new RefreshSignInSessionHandler(
            $signInSessionRepository,
            new ChallengesExpirationTime(10, 5, 20, 15, 30)
        );

        $result = $handler->execute(new RefreshSignInSessionCommand('token'));

        $this->assertInstanceOf(Challenge::class, $result);
    }
}
