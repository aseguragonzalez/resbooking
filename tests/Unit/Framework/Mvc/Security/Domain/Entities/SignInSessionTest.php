<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Security\Domain\Entities;

use Framework\Mvc\Security\Domain\Entities\SignInChallenge;
use Framework\Mvc\Security\Domain\Entities\SignInSession;
use Framework\Mvc\Security\Identity;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
final class SignInSessionTest extends TestCase
{
    public function testNewCreatesSessionWithSignInChallengeAndIdentity(): void
    {
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $identity = $this->createMock(Identity::class);

        $session = SignInSession::new($expiresAt, $identity);

        $this->assertInstanceOf(SignInSession::class, $session);
        $this->assertInstanceOf(SignInChallenge::class, $session->challenge);
        $this->assertSame($identity, $session->identity);
    }

    public function testBuildCreatesSessionWithGivenChallengeAndIdentity(): void
    {
        $challenge = SignInChallenge::new(new \DateTimeImmutable('+1 hour'));
        $identity = $this->createMock(Identity::class);

        $session = SignInSession::build($challenge, $identity);

        $this->assertInstanceOf(SignInSession::class, $session);
        $this->assertSame($challenge, $session->challenge);
        $this->assertSame($identity, $session->identity);
    }

    public function testIsExpiredDelegatesToChallenge(): void
    {
        $challenge = SignInChallenge::new(new \DateTimeImmutable('-1 hour'));
        $identity = $this->createMock(Identity::class);

        $session = SignInSession::build($challenge, $identity);

        $this->assertTrue($session->isExpired());
    }

    public function testRefreshUntilReturnsNewSessionWithRefreshedChallenge(): void
    {
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $identity = $this->createMock(Identity::class);
        $session = SignInSession::new($expiresAt, $identity);
        $newExpiresAt = new \DateTimeImmutable('+2 hours');

        $refreshed = $session->refreshUntil($newExpiresAt);

        $this->assertInstanceOf(SignInSession::class, $refreshed);
        $this->assertNotSame($session, $refreshed);
        $this->assertEquals($newExpiresAt, $refreshed->challenge->getExpiresAt());
        $this->assertSame($identity, $refreshed->identity);
    }
}
