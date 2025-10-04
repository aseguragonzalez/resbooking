<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Security\Models;

use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\UserIdentity;

class ResetPasswordChallengeTest extends TestCase
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
