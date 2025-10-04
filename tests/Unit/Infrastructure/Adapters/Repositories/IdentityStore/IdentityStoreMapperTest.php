<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Adapters\Repositories\IdentityStore;

use PHPUnit\Framework\TestCase;
use Infrastructure\Adapters\Repositories\IdentityStore\IdentityStoreMapper;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignInChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignInSession;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignUpChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\UserIdentity;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\CurrentIdentity;

class IdentityStoreMapperTest extends TestCase
{
    private IdentityStoreMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new IdentityStoreMapper();
    }

    public function testMapToResetPasswordChallengeAndBack(): void
    {
        $expiresAt = (new \DateTimeImmutable('+1 hour'))->setTimezone(new \DateTimeZone('UTC'));
        $data = [
            'token' => 'token',
            'expiresAt' => $expiresAt->format('Y-m-d\TH:i:s\Z'),
            'userIdentity' => [
                'isActive' => true,
                'isBlocked' => false,
                'hash1' => 'h1',
                'hash2' => 'h2',
                'roles' => 'ROLE_USER',
                'seed' => 'seed',
                'username' => 'user@domain.com'
            ]
        ];

        $entity = $this->mapper->mapToResetPasswordChallenge($data);
        $this->assertInstanceOf(ResetPasswordChallenge::class, $entity);

        $back = $this->mapper->mapFromResetPasswordChallenge($entity);
        $this->assertEquals($data, $back);
    }

    public function testMapToSignInChallengeAndBack(): void
    {
        $expiresAt = (new \DateTimeImmutable('+1 hour'))->setTimezone(new \DateTimeZone('UTC'));
        $data = [
            'token' => 'token',
            'expiresAt' => $expiresAt->format('Y-m-d\TH:i:s\Z')
        ];

        $entity = $this->mapper->mapToSignInChallenge($data);
        $this->assertInstanceOf(SignInChallenge::class, $entity);

        $back = $this->mapper->mapFromSignInChallenge($entity);
        $this->assertEquals($data, $back);
    }

    public function testMapToSignUpChallengeAndBack(): void
    {
        $expiresAt = (new \DateTimeImmutable('+1 hour'))->setTimezone(new \DateTimeZone('UTC'));
        $data = [
            'token' => 'token',
            'expiresAt' => $expiresAt->format('Y-m-d\TH:i:s\Z'),
            'userIdentity' => [
                'isActive' => true,
                'isBlocked' => false,
                'hash1' => 'h1',
                'hash2' => 'h2',
                'roles' => 'ROLE_USER',
                'seed' => 'seed',
                'username' => 'user@domain.com'
            ]
        ];

        $entity = $this->mapper->mapToSignUpChallenge($data);
        $this->assertInstanceOf(SignUpChallenge::class, $entity);

        $back = $this->mapper->mapFromSignUpChallenge($entity);
        $this->assertEquals($data, $back);
    }

    public function testMapToCurrentIdentityAndBack(): void
    {
        $data = [
            'isAuthenticated' => true,
            'roles' => 'ROLE_USER,ROLE_ADMIN',
            'username' => 'user@domain.com'
        ];

        $entity = $this->mapper->mapToCurrentIdentity($data);
        $this->assertInstanceOf(CurrentIdentity::class, $entity);

        $back = $this->mapper->mapFromCurrentIdentity($entity);
        $this->assertEquals($data, $back);
    }

    public function testMapToUserIdentityAndBack(): void
    {
        $data = [
            'isActive' => true,
            'isBlocked' => false,
            'hash1' => 'h1',
            'hash2' => 'h2',
            'roles' => 'ROLE_USER,ROLE_ADMIN',
            'seed' => 'seed',
            'username' => 'user@domain.com'
        ];

        $entity = $this->mapper->mapToUserIdentity($data);
        $this->assertInstanceOf(UserIdentity::class, $entity);

        $back = $this->mapper->mapFromUserIdentity($entity);
        $this->assertEquals($data, $back);
    }

    public function testMapToSignInSessionAndBack(): void
    {
        $expiresAt = (new \DateTimeImmutable('+1 hour'))->setTimezone(new \DateTimeZone('UTC'));
        $data = [
            'signInChallenge' => [
                'token' => 'token',
                'expiresAt' => $expiresAt->format('Y-m-d\TH:i:s\Z')
            ],
            'identity' => [
                'isAuthenticated' => true,
                'roles' => 'ROLE_USER',
                'username' => 'user'
            ]
        ];

        $entity = $this->mapper->mapToSignInSession($data);
        $this->assertInstanceOf(SignInSession::class, $entity);

        $back = $this->mapper->mapFromSignInSession($entity);
        $this->assertEquals($data, $back);
    }
}
