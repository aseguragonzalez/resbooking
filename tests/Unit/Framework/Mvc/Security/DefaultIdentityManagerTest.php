<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Security;

use Framework\Mvc\Security\Challenge;
use Framework\Mvc\Security\ChallengeNotificator;
use Framework\Mvc\Security\ChallengesExpirationTime;
use Framework\Mvc\Security\DefaultIdentityManager;
use Framework\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Mvc\Security\Domain\Entities\SignInSession;
use Framework\Mvc\Security\Domain\Entities\SignUpChallenge;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Framework\Mvc\Security\Domain\Exceptions\InvalidCredentialsException;
use Framework\Mvc\Security\Domain\Exceptions\ResetPasswordChallengeException;
use Framework\Mvc\Security\Domain\Exceptions\SessionExpiredException;
use Framework\Mvc\Security\Domain\Exceptions\SignUpChallengeException;
use Framework\Mvc\Security\Domain\Exceptions\UserIsNotFoundException;
use Framework\Mvc\Security\Identity;
use Framework\Mvc\Security\IdentityStore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DefaultIdentityManagerTest extends TestCase
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
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');
        $this->store->expects($this->never())->method('getSignInSessionByToken');

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
        $challenge->expects($this->once())->method('isExpired')->willReturn(false);
        $session = SignInSession::build($challenge, $identity);
        $this->store->expects($this->once())->method('getSignInSessionByToken')->willReturn($session);
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');

        $actualIdentity = $this->manager->getIdentity('token');

        $this->assertSame($identity, $actualIdentity);
    }

    public function testSignUpCreatesUserAndChallenge(): void
    {
        $this->store->expects($this->once())->method('existsUserIdentityByUsername')->willReturn(false);
        $this->store->expects($this->once())->method('saveUserIdentity');
        $this->store->expects($this->once())->method('saveSignUpChallenge');
        $this->notificator->expects($this->once())->method('sendSignUpChallenge');

        $this->manager->signUp('user@domain.com', 'pass', ['role']);
    }

    public function testSignUpDoesNothingIfUserExists(): void
    {
        $this->store->expects($this->once())->method('existsUserIdentityByUsername')->willReturn(true);
        $this->store->expects($this->never())->method('saveUserIdentity');
        $this->store->expects($this->never())->method('saveSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');

        $this->manager->signUp('user@domain.com', 'pass', ['role']);
    }

    public function testActivateUserIdentityActivatesUser(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $challenge = SignUpChallenge::build('token', (new \DateTimeImmutable())->modify('+1 day'), $user);
        $activatedUser = $user->activate();
        $this->store->expects($this->once())->method('getSignUpChallengeByToken')->willReturn($challenge);
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');
        $this->store->expects($this->once())->method('saveUserIdentity')->with($activatedUser);

        $this->manager->activateUserIdentity('token');
    }

    public function testActivateUserIdentityDoesNothingIfChallengeNotFound(): void
    {
        $this->store->expects($this->once())->method('getSignUpChallengeByToken')->willReturn(null);
        $this->store->expects($this->never())->method('saveUserIdentity');
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');
        $this->expectException(SignUpChallengeException::class);

        $this->manager->activateUserIdentity('token');
    }

    public function testActivateUserIdentityDoesNothingIfChallengeExpired(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $challenge = SignUpChallenge::build('token', (new \DateTimeImmutable())->modify('-1 day'), $user);
        $this->store->expects($this->once())->method('getSignUpChallengeByToken')->willReturn($challenge);
        $this->store->expects($this->once())->method('deleteSignUpChallengeByToken')->with('token');
        $this->store->expects($this->never())->method('saveUserIdentity');
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');
        $this->expectException(SignUpChallengeException::class);

        $this->manager->activateUserIdentity('token');
    }

    public function testSignInThrowsIfUserNotFound(): void
    {
        $this->store->expects($this->once())->method('getUserIdentityByUsername')->willReturn(null);
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');
        $this->expectException(InvalidCredentialsException::class);

        $this->manager->signIn('user', 'pass', false);
    }

    public function testSignInThrowsIfPasswordInvalid(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $this->store->expects($this->once())->method('getUserIdentityByUsername')->willReturn($user);
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');
        $this->expectException(InvalidCredentialsException::class);

        $this->manager->signIn('user', 'wrongpass', false);
    }

    public function testSignInCreatesSessionAndReturnsChallenge(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $activatedUser = $user->activate();
        $this->store->expects($this->once())->method('getUserIdentityByUsername')->willReturn($activatedUser);
        $this->store->expects($this->once())->method('saveSignInSession');
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');

        $result = $this->manager->signIn('user@domain.com', 'pass', false);

        $this->assertInstanceOf(\Framework\Mvc\Security\Challenge::class, $result);
        $this->assertNotEmpty($result->getToken());
    }

    public function testSignInWithRememberMeCreatesSessionAndReturnsChallenge(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $activatedUser = $user->activate();
        $this->store->expects($this->once())->method('getUserIdentityByUsername')->willReturn($activatedUser);
        $this->store->expects($this->once())->method('saveSignInSession');
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');

        $result = $this->manager->signIn('user@domain.com', 'pass', true);

        $this->assertInstanceOf(\Framework\Mvc\Security\Challenge::class, $result);
        $this->assertNotEmpty($result->getToken());
        $this->assertGreaterThan(
            (new \DateTimeImmutable())->modify("+{$this->expirationTime->signIn} minutes"),
            $result->getExpiresAt()
        );
    }

    public function testRefreshSignInSessionThrowsSessionExpiredIfSessionNotFound(): void
    {
        $this->store->expects($this->once())->method('getSignInSessionByToken')->willReturn(null);
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');
        $this->expectException(SessionExpiredException::class);

        $this->manager->refreshSignInSession('token');
    }

    public function testRefreshSignInSessionThrowsSessionExpiredIfChallengeExpired(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $challenge = $this->createMock(Challenge::class);
        $challenge->expects($this->once())->method('isExpired')->willReturn(true);
        $session = SignInSession::build($challenge, $user);
        $this->store->expects($this->once())->method('getSignInSessionByToken')->willReturn($session);
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');
        $this->expectException(SessionExpiredException::class);

        $this->manager->refreshSignInSession('token');
    }

    public function testRefreshSignInSessionUpdatesSession(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $challengeUpdated = $this->createMock(Challenge::class);
        $challengeUpdated->expects($this->never())->method('isExpired');
        $challenge = $this->createMock(Challenge::class);
        $challenge->expects($this->once())->method('refreshUntil')->willReturn($challengeUpdated);
        $challenge->expects($this->once())->method('isExpired')->willReturn(false);
        $session = SignInSession::build($challenge, $user);
        $sessionUpdated = SignInSession::build($challengeUpdated, $user);
        $this->store->expects($this->once())->method('getSignInSessionByToken')->willReturn($session);
        $this->store->expects($this->once())->method('saveSignInSession')->with($sessionUpdated);
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');

        $result = $this->manager->refreshSignInSession('token');

        $this->assertInstanceOf(Challenge::class, $result);
    }

    public function testModifyUserIdentityPasswordUpdatesPassword(): void
    {
        $challenge = $this->createMock(Challenge::class);
        $challenge->expects($this->once())->method('isExpired')->willReturn(false);
        $user = UserIdentity::new('user@domain.com', ['role'], 'old');
        $activatedUser = $user->activate();
        $session = SignInSession::build($challenge, $user);
        $this->store->expects($this->once())->method('getSignInSessionByToken')->willReturn($session);
        $this->store->expects($this->once())->method('getUserIdentityByUsername')->willReturn($activatedUser);
        $this->store->expects($this->once())->method('saveUserIdentity');
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');

        $this->manager->modifyUserIdentityPassword('token', 'old', 'new');
    }

    public function testModifyUserIdentityPasswordThrowsIfUserNotFound(): void
    {
        $challenge = $this->createMock(Challenge::class);
        $user = UserIdentity::new('user@domain.com', ['role'], 'old');
        $session = SignInSession::build($challenge, $user);
        $challenge->expects($this->once())->method('isExpired')->willReturn(false);
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');
        $this->store->expects($this->once())->method('getUserIdentityByUsername')->willReturn(null);
        $this->store->expects($this->once())->method('getSignInSessionByToken')->willReturn($session);
        $this->expectException(UserIsNotFoundException::class);

        $this->manager->modifyUserIdentityPassword('token', 'old', 'new');
    }

    public function testSignOutDeletesSession(): void
    {
        $this->store->expects($this->once())->method('deleteSignInSessionByToken')->with('token');
        $this->notificator->expects($this->never())->method('sendSignUpChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');

        $this->manager->signOut('token');
    }

    public function testResetPasswordChallengeDoesNothingIfUserNotFound(): void
    {
        $this->store->expects($this->once())->method('getUserIdentityByUsername')->willReturn(null);
        $this->store->expects($this->never())->method('saveResetPasswordChallenge');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');

        $this->manager->resetPasswordChallenge('user');
    }

    public function testResetPasswordChallengeCreatesChallengeAndNotifies(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $this->store->expects($this->once())->method('getUserIdentityByUsername')->willReturn($user);
        $this->store->expects($this->once())->method('saveResetPasswordChallenge');
        $this->notificator->expects($this->once())->method('sendResetPasswordChallenge');

        $this->manager->resetPasswordChallenge('user');
    }

    public function testResetPasswordFromTokenDoesNothingIfUserNotFound(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $challenge = ResetPasswordChallenge::build('token', (new \DateTimeImmutable())->modify('+1 hour'), $user);
        $this->store->expects($this->once())->method('getResetPasswordChallengeByToken')->willReturn($challenge);
        $this->store->expects($this->once())->method('getUserIdentityByUsername')->willReturn(null);
        $this->store->expects($this->never())->method('saveUserIdentity');
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');

        $this->manager->resetPasswordFromToken('token', 'newpass');
    }

    public function testResetPasswordFromTokenUpdatesPassword(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $activatedUser = $user->activate();
        $challenge = ResetPasswordChallenge::build('token', (new \DateTimeImmutable())->modify('+1 hour'), $user);
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');
        $this->store->expects($this->once())->method('getResetPasswordChallengeByToken')->willReturn($challenge);
        $this->store->expects($this->once())->method('getUserIdentityByUsername')->willReturn($activatedUser);
        $this->store->expects($this->once())->method('saveUserIdentity');

        $this->manager->resetPasswordFromToken('token', 'newpass');
    }

    public function testResetPasswordFromTokenDoNothingIfChallengeNotFound(): void
    {
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');
        $this->store->expects($this->once())->method('getResetPasswordChallengeByToken')->willReturn(null);
        $this->store->expects($this->never())->method('saveUserIdentity');

        $this->manager->resetPasswordFromToken('token', 'newpass');
    }

    public function testResetPasswordFromTokenThrowsIfChallengeExpired(): void
    {
        $user = UserIdentity::new('user@domain.com', ['role'], 'pass');
        $challenge = ResetPasswordChallenge::build('token', (new \DateTimeImmutable())->modify('-1 hour'), $user);
        $this->notificator->expects($this->never())->method('sendResetPasswordChallenge');
        $this->store->expects($this->once())->method('getResetPasswordChallengeByToken')->willReturn($challenge);
        $this->store->expects($this->once())->method('deleteResetPasswordChallengeByToken')->with('token');
        $this->expectException(ResetPasswordChallengeException::class);

        $this->manager->resetPasswordFromToken('token', 'newpass');
    }
}
