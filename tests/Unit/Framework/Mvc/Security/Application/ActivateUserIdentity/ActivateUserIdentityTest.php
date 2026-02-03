<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Security\Application\ActivateUserIdentity;

use Framework\Mvc\Security\Application\ActivateUserIdentity\ActivateUserIdentityCommand;
use Framework\Mvc\Security\Application\ActivateUserIdentity\ActivateUserIdentityHandler;
use Framework\Mvc\Security\Domain\Entities\SignUpChallenge;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Framework\Mvc\Security\Domain\Repositories\SignUpChallengeRepository;
use Framework\Mvc\Security\Domain\Repositories\UserIdentityRepository;
use PHPUnit\Framework\TestCase;

final class ActivateUserIdentityTest extends TestCase
{
    public function testExecuteActivatesUserAndDeletesChallenge(): void
    {
        $user = UserIdentity::new('user@example.com', ['admin'], 'pass');
        $challenge = SignUpChallenge::build('token', (new \DateTimeImmutable())->modify('+1 day'), $user);

        $signUpChallengeRepository = $this->createMock(SignUpChallengeRepository::class);
        $signUpChallengeRepository->method('getByToken')->willReturn($challenge);
        $signUpChallengeRepository->expects($this->once())->method('deleteByToken')->with('token');

        $userIdentityRepository = $this->createMock(UserIdentityRepository::class);
        $userIdentityRepository->expects($this->once())->method('save');

        $handler = new ActivateUserIdentityHandler($signUpChallengeRepository, $userIdentityRepository);

        $handler->execute(new ActivateUserIdentityCommand('token'));
    }
}
