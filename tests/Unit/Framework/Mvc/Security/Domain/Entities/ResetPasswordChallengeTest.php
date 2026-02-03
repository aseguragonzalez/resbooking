<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Security\Domain\Entities;

use PHPUnit\Framework\TestCase;
use Framework\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;

final class ResetPasswordChallengeTest extends TestCase
{
    private function makeUserIdentity(): UserIdentity
    {
        $reflection = new \ReflectionClass(UserIdentity::class);
        return $reflection->newInstanceWithoutConstructor();
    }

    public function testNewGeneratesValidTokenAndExpirationAndUserIdentity(): void
    {
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $userIdentity = $this->makeUserIdentity();
        $challenge = ResetPasswordChallenge::new($expiresAt, $userIdentity);
        $this->assertInstanceOf(ResetPasswordChallenge::class, $challenge);
        $this->assertNotEmpty($challenge->getToken());
        $this->assertEquals($expiresAt, $challenge->expiresAt);
        $this->assertSame($userIdentity, $challenge->userIdentity);
        $this->assertFalse($challenge->isExpired());
    }

    public function testBuildCreatesChallengeWithGivenTokenExpirationAndUserIdentity(): void
    {
        $token = 'testtoken';
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $userIdentity = $this->makeUserIdentity();
        $challenge = ResetPasswordChallenge::build($token, $expiresAt, $userIdentity);
        $this->assertInstanceOf(ResetPasswordChallenge::class, $challenge);
        $this->assertEquals($token, $challenge->getToken());
        $this->assertEquals($expiresAt, $challenge->expiresAt);
        $this->assertSame($userIdentity, $challenge->userIdentity);
    }

    public function testIsExpiredReturnsTrueIfExpired(): void
    {
        $expiresAt = new \DateTimeImmutable('-1 hour');
        $userIdentity = $this->makeUserIdentity();
        $challenge = ResetPasswordChallenge::new($expiresAt, $userIdentity);
        $this->assertTrue($challenge->isExpired());
    }

    public function testRefreshUntilReturnsNewChallengeWithUpdatedExpiration(): void
    {
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $userIdentity = $this->makeUserIdentity();
        $challenge = ResetPasswordChallenge::new($expiresAt, $userIdentity);
        $newExpiresAt = new \DateTimeImmutable('+2 hours');
        $refreshed = $challenge->refreshUntil($newExpiresAt);
        $this->assertInstanceOf(ResetPasswordChallenge::class, $refreshed);
        $this->assertEquals($challenge->getToken(), $refreshed->getToken());
        $this->assertEquals($newExpiresAt, $refreshed->expiresAt);
    }
}
