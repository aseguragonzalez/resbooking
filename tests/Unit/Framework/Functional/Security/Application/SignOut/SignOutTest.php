<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Module\Security\Application\SignOut;

use Framework\Module\Security\Application\SignOut\SignOutCommand;
use Framework\Module\Security\Application\SignOut\SignOutHandler;
use Framework\Module\Security\Domain\Repositories\SignInSessionRepository;
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
