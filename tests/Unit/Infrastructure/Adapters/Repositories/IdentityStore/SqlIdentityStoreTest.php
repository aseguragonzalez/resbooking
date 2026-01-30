<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Adapters\Repositories\IdentityStore;

use Framework\Mvc\Security\Domain\Entities\CurrentIdentity;
use Framework\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Mvc\Security\Domain\Entities\SignInChallenge;
use Framework\Mvc\Security\Domain\Entities\SignInSession;
use Framework\Mvc\Security\Domain\Entities\SignUpChallenge;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Infrastructure\Adapters\Repositories\IdentityStore\SqlIdentityStore;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SqlIdentityStoreTest extends TestCase
{
    private PDO&MockObject $pdo;
    private SqlIdentityStore $store;
    /** @var array<PDOStatement> */
    private array $prepareStatementQueue = [];

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->store = new SqlIdentityStore($this->pdo);
        $this->prepareStatementQueue = [];
    }

    public function testSaveUserIdentityInsertsNewUser(): void
    {
        $user = $this->createUserIdentity(
            username: 'test@example.com',
            hash1: 'hash1',
            hash2: 'hash2',
            seed: 'seed123',
            isActive: true,
            isBlocked: false,
            roles: ['ROLE_USER', 'ROLE_ADMIN']
        );
        $insertStmt = $this->createMock(PDOStatement::class);
        $insertStmt->expects($this->once())
            ->method('execute')
            ->with([
                'id' => 'test@example.com',
                'hash1' => 'hash1',
                'hash2' => 'hash2',
                'is_active' => 1,
                'is_blocked' => 0,
                'seed' => 'seed123',
            ]);
        $this->prepareStatementQueue[] = $insertStmt;
        $deleteRolesStmt = $this->createMock(PDOStatement::class);
        $deleteRolesStmt->expects($this->once())
            ->method('execute')
            ->with(['user_id' => 'test@example.com']);
        $this->prepareStatementQueue[] = $deleteRolesStmt;
        $insertRoleStmt = $this->createMock(PDOStatement::class);
        $expectedRoles = ['ROLE_USER', 'ROLE_ADMIN'];
        $calledRoles = [];
        $insertRoleStmt->expects($this->exactly(2))
            ->method('execute')
            ->with($this->callback(function (array $params) use (&$calledRoles, $expectedRoles): bool {
                if (
                    isset($params['user_id'])
                    && $params['user_id'] === 'test@example.com'
                    && isset($params['role'])
                    && in_array($params['role'], $expectedRoles, true)
                ) {
                    $calledRoles[] = $params['role'];
                    return true;
                }
                return false;
            }))
            ->willReturn(true);

        $this->prepareStatementQueue[] = $insertRoleStmt;

        $this->setupPrepareCallback();

        $this->store->saveUserIdentity($user);

        $this->assertCount(2, $calledRoles);
        $this->assertContains('ROLE_USER', $calledRoles);
        $this->assertContains('ROLE_ADMIN', $calledRoles);
    }

    public function testSaveUserIdentityUpdatesExistingUser(): void
    {
        $user = $this->createUserIdentity(
            username: 'test@example.com',
            hash1: 'new_hash1',
            hash2: 'new_hash2',
            seed: 'new_seed',
            isActive: false,
            isBlocked: true,
            roles: []
        );
        $insertStmt = $this->createMock(PDOStatement::class);
        $insertStmt->expects($this->once())
            ->method('execute')
            ->with([
                'id' => 'test@example.com',
                'hash1' => 'new_hash1',
                'hash2' => 'new_hash2',
                'is_active' => 0,
                'is_blocked' => 1,
                'seed' => 'new_seed',
            ]);
        $this->prepareStatementQueue[] = $insertStmt;
        $deleteRolesStmt = $this->createMock(PDOStatement::class);
        $deleteRolesStmt->expects($this->once())
            ->method('execute')
            ->with(['user_id' => 'test@example.com']);
        $this->prepareStatementQueue[] = $deleteRolesStmt;
        $this->setupPrepareCallback();

        $this->store->saveUserIdentity($user);
    }

    public function testSaveUserIdentityDeletesAndReinsertsRoles(): void
    {
        $user = $this->createUserIdentity(
            username: 'test@example.com',
            hash1: 'hash1',
            hash2: 'hash2',
            seed: 'seed',
            isActive: true,
            isBlocked: false,
            roles: ['ROLE_USER']
        );
        $insertStmt = $this->createMock(PDOStatement::class);
        $insertStmt->expects($this->once())
            ->method('execute');
        $this->prepareStatementQueue[] = $insertStmt;
        $deleteRolesStmt = $this->createMock(PDOStatement::class);
        $deleteRolesStmt->expects($this->once())
            ->method('execute')
            ->with(['user_id' => 'test@example.com']);
        $this->prepareStatementQueue[] = $deleteRolesStmt;
        $insertRoleStmt = $this->createMock(PDOStatement::class);
        $insertRoleStmt->expects($this->once())
            ->method('execute')
            ->with([
                'user_id' => 'test@example.com',
                'role' => 'ROLE_USER',
            ]);
        $this->prepareStatementQueue[] = $insertRoleStmt;

        $this->setupPrepareCallback();

        $this->store->saveUserIdentity($user);
    }

    public function testGetUserIdentityByUsernameReturnsUserWithRoles(): void
    {
        $username = 'test@example.com';
        $userStmt = $this->createMock(PDOStatement::class);
        $userStmt->expects($this->once())
            ->method('execute')
            ->with(['username' => $username]);
        $userStmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id' => $username,
                'hash1' => 'hash1',
                'hash2' => 'hash2',
                'is_active' => 1,
                'is_blocked' => 0,
                'seed' => 'seed123',
            ]);
        $this->prepareStatementQueue[] = $userStmt;
        $rolesStmt = $this->createMock(PDOStatement::class);
        $rolesStmt->expects($this->once())
            ->method('execute')
            ->with(['user_id' => $username]);
        $rolesStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                ['role' => 'ROLE_USER'],
                ['role' => 'ROLE_ADMIN'],
            ]);
        $this->prepareStatementQueue[] = $rolesStmt;

        $this->setupPrepareCallback();

        /** @var UserIdentity $result */
        $result = $this->store->getUserIdentityByUsername($username);

        $this->assertSame($username, $result->username());
        $this->assertSame('hash1', $result->hash1);
        $this->assertSame('hash2', $result->hash2);
        $this->assertSame('seed123', $result->seed);
        $this->assertSame(['ROLE_USER', 'ROLE_ADMIN'], $result->roles);
        $this->assertTrue($result->isActive);
        $this->assertFalse($result->isBlocked);
        $this->assertInstanceOf(UserIdentity::class, $result);
    }

    public function testGetUserIdentityByUsernameReturnsNullWhenNotFound(): void
    {
        $username = 'nonexistent@example.com';
        $userStmt = $this->createMock(PDOStatement::class);
        $userStmt->expects($this->once())
            ->method('execute')
            ->with(['username' => $username]);
        $userStmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(false);
        $this->prepareStatementQueue[] = $userStmt;

        $this->setupPrepareCallback();

        $result = $this->store->getUserIdentityByUsername($username);

        $this->assertNull($result);
    }

    public function testGetUserIdentityByUsernameHandlesUserWithoutRoles(): void
    {
        $username = 'test@example.com';
        $userStmt = $this->createMock(PDOStatement::class);
        $userStmt->expects($this->once())
            ->method('execute')
            ->with(['username' => $username]);
        $userStmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id' => $username,
                'hash1' => 'hash1',
                'hash2' => 'hash2',
                'is_active' => 1,
                'is_blocked' => 0,
                'seed' => 'seed123',
            ]);
        $this->prepareStatementQueue[] = $userStmt;
        $rolesStmt = $this->createMock(PDOStatement::class);
        $rolesStmt->expects($this->once())
            ->method('execute')
            ->with(['user_id' => $username]);
        $rolesStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([]);
        $this->prepareStatementQueue[] = $rolesStmt;
        $this->setupPrepareCallback();

        /** @var UserIdentity $result */
        $result = $this->store->getUserIdentityByUsername($username);

        $this->assertSame([], $result->roles);
        $this->assertInstanceOf(UserIdentity::class, $result);
    }

    public function testExistsUserIdentityByUsernameReturnsTrueWhenExists(): void
    {
        $username = 'test@example.com';
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['username' => $username]);
        $stmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(['count' => '1']);
        $this->prepareStatementQueue[] = $stmt;

        $this->setupPrepareCallback();

        $result = $this->store->existsUserIdentityByUsername($username);

        $this->assertTrue($result);
    }

    public function testExistsUserIdentityByUsernameReturnsFalseWhenNotExists(): void
    {
        $username = 'nonexistent@example.com';
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['username' => $username]);
        $stmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(['count' => '0']);
        $this->prepareStatementQueue[] = $stmt;

        $this->setupPrepareCallback();

        $result = $this->store->existsUserIdentityByUsername($username);

        $this->assertFalse($result);
    }

    public function testSaveSignInSessionSavesChallengeAndSession(): void
    {
        $token = 'session_token_123';
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $username = 'test@example.com';

        $challenge = SignInChallenge::build($token, $expiresAt);
        $identity = CurrentIdentity::build(
            isAuthenticated: true,
            roles: ['ROLE_USER'],
            username: $username
        );
        $session = SignInSession::build($challenge, $identity);
        $challengeStmt = $this->createMock(PDOStatement::class);
        $challengeStmt->expects($this->once())
            ->method('execute')
            ->with([
                'id' => $token,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            ]);
        $this->prepareStatementQueue[] = $challengeStmt;
        $sessionStmt = $this->createMock(PDOStatement::class);
        $sessionStmt->expects($this->once())
            ->method('execute')
            ->with([
                'sign_in_challenge_id' => $token,
                'user_id' => $username,
            ]);
        $this->prepareStatementQueue[] = $sessionStmt;

        $this->setupPrepareCallback();

        $this->store->saveSignInSession($session);
    }

    public function testGetSignInSessionByTokenReturnsSessionWithIdentityAndRoles(): void
    {
        $token = 'session_token_123';
        $username = 'test@example.com';
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $sessionStmt = $this->createMock(PDOStatement::class);
        $sessionStmt->expects($this->once())
            ->method('execute')
            ->with(['token' => $token]);
        $sessionStmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'challenge_id' => $token,
                'challenge_expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                'user_id' => $username,
            ]);
        $this->prepareStatementQueue[] = $sessionStmt;
        $rolesStmt = $this->createMock(PDOStatement::class);
        $rolesStmt->expects($this->once())
            ->method('execute')
            ->with(['user_id' => $username]);
        $rolesStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                ['role' => 'ROLE_USER'],
                ['role' => 'ROLE_ADMIN'],
            ]);
        $this->prepareStatementQueue[] = $rolesStmt;

        $this->setupPrepareCallback();

        /** @var SignInSession $result */
        $result = $this->store->getSignInSessionByToken($token);

        $this->assertSame($token, $result->challenge->getToken());
        $this->assertSame($username, $result->identity->username());
        $this->assertSame(['ROLE_USER', 'ROLE_ADMIN'], $result->identity->getRoles());
        $this->assertTrue($result->identity->isAuthenticated());
        $this->assertInstanceOf(SignInSession::class, $result);
        $this->assertInstanceOf(CurrentIdentity::class, $result->identity);
    }

    public function testGetSignInSessionByTokenReturnsNullWhenNotFound(): void
    {
        $token = 'nonexistent_token';
        $sessionStmt = $this->createMock(PDOStatement::class);
        $sessionStmt->expects($this->once())
            ->method('execute')
            ->with(['token' => $token]);
        $sessionStmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(false);
        $this->prepareStatementQueue[] = $sessionStmt;

        $this->setupPrepareCallback();

        $result = $this->store->getSignInSessionByToken($token);

        $this->assertNull($result);
    }

    public function testDeleteSignInSessionByTokenDeletesSession(): void
    {
        $token = 'session_token_123';
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['token' => $token]);
        $this->prepareStatementQueue[] = $stmt;

        $this->setupPrepareCallback();

        $this->store->deleteSignInSessionByToken($token);
    }

    public function testSaveSignUpChallengeSavesChallenge(): void
    {
        $token = 'signup_token_123';
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $user = $this->createUserIdentity(
            username: 'test@example.com',
            hash1: 'hash1',
            hash2: 'hash2',
            seed: 'seed',
            isActive: false,
            isBlocked: false,
            roles: []
        );
        $challenge = SignUpChallenge::build($token, $expiresAt, $user);
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([
                'id' => $token,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                'user_id' => 'test@example.com',
            ]);
        $this->prepareStatementQueue[] = $stmt;
        $this->setupPrepareCallback();

        $this->store->saveSignUpChallenge($challenge);
    }

    public function testGetSignUpChallengeByTokenReturnsChallengeWithUserIdentity(): void
    {
        $token = 'signup_token_123';
        $username = 'test@example.com';
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $challengeStmt = $this->createMock(PDOStatement::class);
        $challengeStmt->expects($this->once())
            ->method('execute')
            ->with(['token' => $token]);
        $challengeStmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id' => $token,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                'user_id' => $username,
                'hash1' => 'hash1',
                'hash2' => 'hash2',
                'is_active' => 1,
                'is_blocked' => 0,
                'seed' => 'seed123',
            ]);
        $this->prepareStatementQueue[] = $challengeStmt;
        $rolesStmt = $this->createMock(PDOStatement::class);
        $rolesStmt->expects($this->once())
            ->method('execute')
            ->with(['user_id' => $username]);
        $rolesStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                ['role' => 'ROLE_USER'],
            ]);
        $this->prepareStatementQueue[] = $rolesStmt;
        $this->setupPrepareCallback();

        /** @var SignUpChallenge $result */
        $result = $this->store->getSignUpChallengeByToken($token);

        $this->assertSame($token, $result->getToken());
        $this->assertSame($username, $result->userIdentity->username());
        $this->assertInstanceOf(SignUpChallenge::class, $result);
        $this->assertInstanceOf(UserIdentity::class, $result->userIdentity);
    }

    public function testGetSignUpChallengeByTokenReturnsNullWhenNotFound(): void
    {
        $token = 'nonexistent_token';
        $challengeStmt = $this->createMock(PDOStatement::class);
        $challengeStmt->expects($this->once())
            ->method('execute')
            ->with(['token' => $token]);
        $challengeStmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(false);
        $this->prepareStatementQueue[] = $challengeStmt;

        $this->setupPrepareCallback();

        $result = $this->store->getSignUpChallengeByToken($token);

        $this->assertNull($result);
    }

    public function testDeleteSignUpChallengeByTokenDeletesChallenge(): void
    {
        $token = 'signup_token_123';
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['token' => $token]);
        $this->prepareStatementQueue[] = $stmt;

        $this->setupPrepareCallback();

        $this->store->deleteSignUpChallengeByToken($token);
    }

    public function testSaveResetPasswordChallengeSavesChallenge(): void
    {
        $token = 'reset_token_123';
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $user = $this->createUserIdentity(
            username: 'test@example.com',
            hash1: 'hash1',
            hash2: 'hash2',
            seed: 'seed',
            isActive: true,
            isBlocked: false,
            roles: []
        );
        $challenge = ResetPasswordChallenge::build($token, $expiresAt, $user);
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([
                'id' => $token,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                'user_id' => 'test@example.com',
            ]);
        $this->prepareStatementQueue[] = $stmt;
        $this->setupPrepareCallback();

        $this->store->saveResetPasswordChallenge($challenge);
    }

    public function testGetResetPasswordChallengeByTokenReturnsChallengeWithUserIdentity(): void
    {
        $token = 'reset_token_123';
        $username = 'test@example.com';
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $challengeStmt = $this->createMock(PDOStatement::class);
        $challengeStmt->expects($this->once())
            ->method('execute')
            ->with(['token' => $token]);
        $challengeStmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id' => $token,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                'user_id' => $username,
                'hash1' => 'hash1',
                'hash2' => 'hash2',
                'is_active' => 1,
                'is_blocked' => 0,
                'seed' => 'seed123',
            ]);
        $this->prepareStatementQueue[] = $challengeStmt;
        $rolesStmt = $this->createMock(PDOStatement::class);
        $rolesStmt->expects($this->once())
            ->method('execute')
            ->with(['user_id' => $username]);
        $rolesStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                ['role' => 'ROLE_USER'],
            ]);
        $this->prepareStatementQueue[] = $rolesStmt;
        $this->setupPrepareCallback();

        /** @var ResetPasswordChallenge $result */
        $result = $this->store->getResetPasswordChallengeByToken($token);

        $this->assertSame($token, $result->getToken());
        $this->assertSame($username, $result->userIdentity->username());
        $this->assertInstanceOf(ResetPasswordChallenge::class, $result);
        $this->assertInstanceOf(UserIdentity::class, $result->userIdentity);
    }

    public function testGetResetPasswordChallengeByTokenReturnsNullWhenNotFound(): void
    {
        $token = 'nonexistent_token';
        $challengeStmt = $this->createMock(PDOStatement::class);
        $challengeStmt->expects($this->once())
            ->method('execute')
            ->with(['token' => $token]);
        $challengeStmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(false);
        $this->prepareStatementQueue[] = $challengeStmt;

        $this->setupPrepareCallback();

        $result = $this->store->getResetPasswordChallengeByToken($token);

        $this->assertNull($result);
    }

    public function testDeleteResetPasswordChallengeByTokenDeletesChallenge(): void
    {
        $token = 'reset_token_123';
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['token' => $token]);
        $this->prepareStatementQueue[] = $stmt;

        $this->setupPrepareCallback();

        $this->store->deleteResetPasswordChallengeByToken($token);
    }

    /**
     * @param array<string> $roles
     */
    private function createUserIdentity(
        string $username,
        string $hash1,
        string $hash2,
        string $seed,
        bool $isActive,
        bool $isBlocked,
        array $roles
    ): UserIdentity {
        return UserIdentity::build(
            hash1: $hash1,
            hash2: $hash2,
            roles: $roles,
            seed: $seed,
            username: $username,
            isActive: $isActive,
            isBlocked: $isBlocked
        );
    }

    private function setupPrepareCallback(): void
    {
        $this->pdo->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturnCallback(function (string $sql): PDOStatement {
                if (empty($this->prepareStatementQueue)) {
                    $fallbackStmt = $this->createMock(PDOStatement::class);
                    $fallbackStmt->expects($this->any())->method('execute');
                    $fallbackStmt->expects($this->any())->method('fetch')->willReturn(false);
                    $fallbackStmt->expects($this->any())->method('fetchAll')->willReturn([]);
                    return $fallbackStmt;
                }

                return array_shift($this->prepareStatementQueue);
            });
    }
}
