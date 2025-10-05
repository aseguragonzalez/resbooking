<?php

declare(strict_types=1);

namespace Tests\Unit\Seedwork\Infrastructure\Mvc\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Seedwork\Infrastructure\Mvc\Security\Challenge;
use Seedwork\Infrastructure\Mvc\Security\ChallengeNotificator;
use Seedwork\Infrastructure\Mvc\Security\ChallengesExpirationTime;
use Seedwork\Infrastructure\Mvc\Security\DefaultIdentityManager;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignUpChallenge;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\SignInSession;
use Seedwork\Infrastructure\Mvc\Security\Domain\Entities\UserIdentity;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\InvalidCredentialsException;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\ResetPasswordChallengeException;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\SessionExpiredException;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\SignUpChallengeException;
use Seedwork\Infrastructure\Mvc\Security\Domain\Exceptions\UserIsNotFoundException;
use Seedwork\Infrastructure\Mvc\Security\Identity;
use Seedwork\Infrastructure\Mvc\Security\IdentityStore;

class DefaultIdentityManagerTest extends TestCase
{
    private MockObject&IdentityStore $store;
    private MockObject&ChallengeNotificator $notificator;
    private ChallengesExpirationTime $expirationTime;
    private DefaultIdentityManager $manager;

    protected function setUp(): void
    {
        $this->store = $this->createMock(IdentityStore::class);
        $this->notificator = $this->createMock(ChallengeNotificator::class);
        $this->expirationTime = new ChallengesExpirationTime(
            signUp: 10,
            signIn: 5,
            signInWithRememberMe: 20,
            refresh: 15,
            resetPasswordChallenge: 30
        );
        $this->manager = new DefaultIdentityManager($this->notificator, $this->expirationTime, $this->store);
    }

    #[DataProvider('tokenProvider')]
    public function testGetIdentityReturnsAnonymousForEmptyTokenParametrized(?string $token): void
    {
        $identity = $this->manager->getIdentity($token);
        $this->assertInstanceOf(Identity::class, $identity);
        $this->assertFalse($identity->isAuthenticated());
        $this->assertEquals('anonymous', $identity->username());
    }

    /**
     * @return array<int, list<string|null>>
     */
    public static function tokenProvider(): array
    {
        return [
            [''],
            [null],
            ['   '],
            ["\n"],
            ["\t"],
        ];
    }

    public function testGetIdentityReturnsSessionIdentity(): void
    {
        $identity = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $challenge = $this->createMock(Challenge::class);
        $challenge->method('isExpired')->willReturn(false);
        $challenge->method('getToken')->willReturn('token');
        $challenge->method('refreshUntil')->willReturn($challenge);
        $session = SignInSession::build($challenge, $identity);
        $this->store->method('getSignInSessionByToken')->willReturn($session);

        $actualIdentity = $this->manager->getIdentity('token');

        $this->assertSame($identity, $actualIdentity);
    }

    public function testSignUpCreatesUserAndChallenge(): void
    {
        $this->store->method('existsUserIdentityByUsername')->willReturn(false);
        $this->store->expects($this->once())->method('saveUserIdentity');
        $this->store->expects($this->once())->method('saveSignUpChallenge');
        $this->notificator->expects($this->once())->method('sendSignUpChallenge');
        $this->manager->signUp('user@domain.com', 'pass', ['role']);
    }

    public function testSignUpDoesNothingIfUserExists(): void
    {
        $this->store->method('existsUserIdentityByUsername')->willReturn(true);
        $this->store->expects($this->never())->method('saveUserIdentity');
        $this->manager->signUp('user@domain.com', 'pass', ['role']);
    }

    public function testActivateUserIdentityActivatesUser(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $challenge = SignUpChallenge::build('token', (new \DateTimeImmutable())->modify('+1 day'), $user);
        $activatedUser = $user->activate();
        $this->store->method('getSignUpChallengeByToken')->willReturn($challenge);
        $this->store->method('getUserIdentityByUsername')->willReturn($user);
        $this->store->expects($this->once())->method('saveUserIdentity')->with($activatedUser);

        $this->manager->activateUserIdentity('token');
    }

    public function testActivateUserIdentityDoesNothingIfChallengeNotFound(): void
    {
        $this->store->method('getSignUpChallengeByToken')->willReturn(null);
        $this->store->expects($this->never())->method('saveUserIdentity');

        $this->expectException(SignUpChallengeException::class);
        $this->manager->activateUserIdentity('token');
    }

    public function testActivateUserIdentityDoesNothingIfChallengeExpired(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $challenge = SignUpChallenge::build('token', (new \DateTimeImmutable())->modify('-1 day'), $user);
        $this->store->method('getSignUpChallengeByToken')->willReturn($challenge);
        $this->store->expects($this->once())->method('deleteSignUpChallengeByToken')->with('token');
        $this->store->expects($this->never())->method('saveUserIdentity');

        $this->expectException(SignUpChallengeException::class);
        $this->manager->activateUserIdentity('token');
    }

    public function testSignInThrowsIfUserNotFound(): void
    {
        $this->store->method('getUserIdentityByUsername')->willReturn(null);
        $this->expectException(InvalidCredentialsException::class);
        $this->manager->signIn('user', 'pass', false);
    }

    public function testSignInThrowsIfPasswordInvalid(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $this->store->method('getUserIdentityByUsername')->willReturn($user);
        $this->expectException(InvalidCredentialsException::class);
        $this->manager->signIn('user', 'wrongpass', false);
    }

    public function testSignInCreatesSessionAndReturnsChallenge(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $activatedUser = $user->activate();
        $this->store->method('getUserIdentityByUsername')->willReturn($activatedUser);
        $this->store->expects($this->once())->method('saveSignInSession');
        $result = $this->manager->signIn('user@domain.com', 'pass', false);

        $this->assertInstanceOf(\Seedwork\Infrastructure\Mvc\Security\Challenge::class, $result);
        $this->assertNotEmpty($result->getToken());
    }

    public function testSignInWithRememberMeCreatesSessionAndReturnsChallenge(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $activatedUser = $user->activate();
        $this->store->method('getUserIdentityByUsername')->willReturn($activatedUser);
        $this->store->expects($this->once())->method('saveSignInSession');
        $result = $this->manager->signIn('user@domain.com', 'pass', true);

        $this->assertInstanceOf(\Seedwork\Infrastructure\Mvc\Security\Challenge::class, $result);
        $this->assertNotEmpty($result->getToken());
        $this->assertGreaterThan(
            (new \DateTimeImmutable())->modify("+{$this->expirationTime->signIn} minutes"),
            $result->getExpiresAt()
        );
    }

    public function testRefreshSignInSessionThrowsSessionExpiredIfSessionNotFound(): void
    {
        $this->store->method('getSignInSessionByToken')->willReturn(null);
        $this->expectException(SessionExpiredException::class);
        $this->manager->refreshSignInSession('token');
    }

    public function testRefreshSignInSessionThrowsSessionExpiredIfChallengeExpired(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $challenge = $this->createMock(Challenge::class);
        $challenge->method('isExpired')->willReturn(true);
        $session = SignInSession::build($challenge, $user);
        $this->store->method('getSignInSessionByToken')->willReturn($session);
        $this->expectException(SessionExpiredException::class);
        $this->manager->refreshSignInSession('token');
    }

    public function testRefreshSignInSessionUpdatesSession(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $challenge = $this->createMock(Challenge::class);
        $challengeUpdated = $this->createMock(Challenge::class);
        $challenge->method('refreshUntil')->willReturn($challengeUpdated);
        $challenge->method('isExpired')->willReturn(false);
        $challengeUpdated->method('isExpired')->willReturn(false);
        $session = SignInSession::build($challenge, $user);
        $sessionUpdated = SignInSession::build($challengeUpdated, $user);
        $this->store->method('getSignInSessionByToken')->willReturn($session);
        $this->store->expects($this->once())->method('saveSignInSession')->with($sessionUpdated);

        $result = $this->manager->refreshSignInSession('token');

        $this->assertInstanceOf(Challenge::class, $result);
    }

    public function testModifyUserIdentityPasswordUpdatesPassword(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'old');
        $activatedUser = $user->activate();
        $session = SignInSession::build($this->createMock(Challenge::class), $user);
        $this->store->method('getSignInSessionByToken')->willReturn($session);
        $this->store->method('getUserIdentityByUsername')->willReturn($activatedUser);
        $this->store->expects($this->once())->method('saveUserIdentity');
        $this->manager->modifyUserIdentityPassword('token', 'old', 'new');
    }

    public function testModifyUserIdentityPasswordThrowsIfUserNotFound(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'old');
        $session = SignInSession::build($this->createMock(Challenge::class), $user);
        $this->store->method('getSignInSessionByToken')->willReturn($session);
        $this->store->method('getUserIdentityByUsername')->willReturn(null);
        $this->expectException(UserIsNotFoundException::class);
        $this->manager->modifyUserIdentityPassword('token', 'old', 'new');
    }

    public function testSignOutDeletesSession(): void
    {
        $this->store->expects($this->once())->method('deleteSignInSessionByToken')->with('token');
        $this->manager->signOut('token');
    }

    public function testResetPasswordChallengeDoesNothingIfUserNotFound(): void
    {
        $this->store->method('getUserIdentityByUsername')->willReturn(null);
        $this->store->expects($this->never())->method('saveResetPasswordChallenge');
        $this->manager->resetPasswordChallenge('user');
    }

    public function testResetPasswordChallengeCreatesChallengeAndNotifies(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $this->store->method('getUserIdentityByUsername')->willReturn($user);
        $this->store->expects($this->once())->method('saveResetPasswordChallenge');
        $this->notificator->expects($this->once())->method('sendResetPasswordChallenge');
        $this->manager->resetPasswordChallenge('user');
    }

    public function testResetPasswordFromTokenDoesNothingIfUserNotFound(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $challenge = ResetPasswordChallenge::build('token', (new \DateTimeImmutable())->modify('+1 hour'), $user);
        $this->store->method('getResetPasswordChallengeByToken')->willReturn($challenge);
        $this->store->method('getUserIdentityByUsername')->willReturn(null);
        $this->store->expects($this->never())->method('saveUserIdentity');
        $this->manager->resetPasswordFromToken('token', 'newpass');
    }

    public function testResetPasswordFromTokenUpdatesPassword(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $activatedUser = $user->activate();
        $challenge = ResetPasswordChallenge::build('token', (new \DateTimeImmutable())->modify('+1 hour'), $user);
        $this->store->method('getResetPasswordChallengeByToken')->willReturn($challenge);
        $this->store->method('getUserIdentityByUsername')->willReturn($activatedUser);
        $this->store->expects($this->once())->method('saveUserIdentity');
        $this->manager->resetPasswordFromToken('token', 'newpass');
    }

    public function testResetPasswordFromTokenDoNothingIfChallengeNotFound(): void
    {
        $this->store->method('getResetPasswordChallengeByToken')->willReturn(null);
        $this->store->expects($this->never())->method('saveUserIdentity');
        $this->manager->resetPasswordFromToken('token', 'newpass');
    }

    public function testResetPasswordFromTokenThrowsIfChallengeExpired(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $challenge = ResetPasswordChallenge::build('token', (new \DateTimeImmutable())->modify('-1 hour'), $user);
        $this->store->method('getResetPasswordChallengeByToken')->willReturn($challenge);
        $this->store->expects($this->once())->method('deleteResetPasswordChallengeByToken')->with('token');
        $this->expectException(ResetPasswordChallengeException::class);
        $this->manager->resetPasswordFromToken('token', 'newpass');
    }
}
