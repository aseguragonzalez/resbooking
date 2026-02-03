<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Security\Application\GetIdentity;

use Framework\Mvc\Security\Application\GetIdentity\GetIdentityCommand;
use Framework\Mvc\Security\Application\GetIdentity\GetIdentityHandler;
use Framework\Mvc\Security\Domain\Repositories\SignInSessionRepository;
use PHPUnit\Framework\TestCase;

final class GetIdentityTest extends TestCase
{
    public function testExecuteReturnsAnonymousForNullToken(): void
    {
        $handler = new GetIdentityHandler($this->createStub(SignInSessionRepository::class));

        $identity = $handler->execute(new GetIdentityCommand(null));

        $this->assertFalse($identity->isAuthenticated());
        $this->assertEquals('anonymous', $identity->username());
    }
}
