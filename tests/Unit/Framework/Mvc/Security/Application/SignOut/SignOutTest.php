<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Security\Application\SignOut;

use Framework\Mvc\Security\Application\SignOut\SignOutCommand;
use Framework\Mvc\Security\Application\SignOut\SignOutHandler;
use Framework\Mvc\Security\Domain\Repositories\SignInSessionRepository;
use PHPUnit\Framework\TestCase;

final class SignOutTest extends TestCase
{
    public function testExecuteDeletesSessionByToken(): void
    {
        $signInSessionRepository = $this->createMock(SignInSessionRepository::class);
        $signInSessionRepository->expects($this->once())
            ->method('deleteByToken')
            ->with('the-token');

        $handler = new SignOutHandler($signInSessionRepository);

        $handler->execute(new SignOutCommand('the-token'));
    }
}
