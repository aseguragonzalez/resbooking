<?php

declare(strict_types=1);

namespace Tests\Unit\Framework\Mvc\Security\Infrastructure;

use Framework\Mvc\Security\Domain\Entities\ResetPasswordChallenge;
use Framework\Mvc\Security\Domain\Entities\UserIdentity;
use Framework\Mvc\Security\Infrastructure\SqlResetPasswordChallengeRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SqlResetPasswordChallengeRepositoryTest extends TestCase
{
    private PDO&MockObject $pdo;
    private SqlResetPasswordChallengeRepository $repository;
    /** @var array<PDOStatement> */
    private array $prepareStatementQueue = [];

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->repository = new SqlResetPasswordChallengeRepository($this->pdo);
        $this->prepareStatementQueue = [];
    }

    public function testSaveSavesChallenge(): void
    {
        $token = 'reset_token_123';
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $user = UserIdentity::build(
            hash1: 'hash1',
            hash2: 'hash2',
            roles: [],
            seed: 'seed',
            username: 'test@example.com',
            isActive: true,
            isBlocked: false
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

        $this->repository->save($challenge);
    }

    public function testGetByTokenReturnsChallengeWithUserIdentity(): void
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

        $result = $this->repository->getByToken($token);

        $this->assertInstanceOf(ResetPasswordChallenge::class, $result);
        $this->assertSame($token, $result->token);
        $this->assertSame($username, $result->userIdentity->username());
        $this->assertInstanceOf(UserIdentity::class, $result->userIdentity);
    }

    public function testGetByTokenReturnsNullWhenNotFound(): void
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

        $result = $this->repository->getByToken($token);

        $this->assertNull($result);
    }

    public function testDeleteByTokenDeletesChallenge(): void
    {
        $token = 'reset_token_123';
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['token' => $token]);
        $this->prepareStatementQueue[] = $stmt;

        $this->setupPrepareCallback();

        $this->repository->deleteByToken($token);
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
